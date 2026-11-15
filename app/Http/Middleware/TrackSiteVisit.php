<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    /**
     * Record public platform and storefront page visits.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->recordVisit($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->expectsJson() || $request->ajax()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if ($request->is(
            'admin',
            'admin/*',
            'super-admin',
            'super-admin/*',
            'staff',
            'staff/*',
            'cpanel-deploy',
            'up',
            'livewire/*',
            'api/*',
            'paystack/*',
            'storage/*',
            'build/*',
            'favicon*',
            'robots.txt',
        )) {
            return false;
        }

        $userAgent = strtolower((string) $request->userAgent());
        foreach (['bot', 'crawler', 'spider', 'slurp', 'headless', 'wget', 'curl'] as $signature) {
            if ($userAgent !== '' && str_contains($userAgent, $signature)) {
                return false;
            }
        }

        return true;
    }

    private function recordVisit(Request $request): void
    {
        try {
            $tenantId = app(CurrentTenant::class)->id();
            $sessionId = $request->hasSession() ? $request->session()->getId() : null;
            $path = '/'.ltrim($request->path(), '/');
            $today = now()->toDateString();

            $attributes = [
                'tenant_id' => $tenantId,
                'session_id' => $sessionId,
                'path' => Str::limit($path, 255, ''),
                'visited_on' => $today,
            ];

            if ($sessionId !== null && SiteVisit::query()->where($attributes)->exists()) {
                return;
            }

            SiteVisit::create([
                ...$attributes,
                'ip_address' => $request->ip(),
                'referer' => $request->headers->get('referer')
                    ? Str::limit((string) $request->headers->get('referer'), 500, '')
                    : null,
                'user_agent' => $request->userAgent()
                    ? Str::limit((string) $request->userAgent(), 500, '')
                    : null,
            ]);
        } catch (\Throwable) {
            // Never interrupt page delivery if visit logging fails.
        }
    }
}
