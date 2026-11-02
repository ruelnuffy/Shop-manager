<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $currentTenant = app(CurrentTenant::class);

        // If signing in from the ShopManager platform console without an active tenant,
        // resolve the user's tenant from their credentials.
        if (! $currentTenant->check()) {
            $email = $this->string('email')->toString();
            $password = $this->string('password')->toString();

            $candidates = User::withoutTenancy()
                ->with('tenant')
                ->where('email', $email)
                ->get();

            $matchedUser = $candidates->first(function (User $candidate) use ($password) {
                return $candidate->tenant
                    && $candidate->tenant->status === 'active'
                    && Hash::check($password, $candidate->password);
            });

            if (! $matchedUser) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }

            $currentTenant->set($matchedUser->tenant);
            $this->attributes->set('resolved_from_platform_login', true);

            if ($this->hasSession()) {
                $this->session()->put('active_tenant_id', $matchedUser->tenant_id);
            }
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($this->hasSession() && $currentTenant->id()) {
            $this->session()->put('active_tenant_id', $currentTenant->id());
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $tenantId = app(CurrentTenant::class)->id() ?? 'global';

        return Str::transliterate($tenantId.'|'.Str::lower($this->string('email')).'|'.$this->ip());
    }
}
