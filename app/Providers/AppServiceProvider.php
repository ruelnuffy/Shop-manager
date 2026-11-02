<?php

namespace App\Providers;

use App\Services\CurrentTenant;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrentTenant::class, function () {
            return new CurrentTenant;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = (string) config('app.url', '');
        $parsed = ! empty($appUrl) ? parse_url($appUrl) : [];

        if ($this->app->runningInConsole() && ! $this->app->bound('request')) {
            return;
        }

        if (! empty($appUrl)) {
            if (! empty($parsed['path']) && $parsed['path'] !== '/') {
                URL::forceRootUrl(rtrim($appUrl, '/'));
            }
            if (! empty($parsed['scheme']) && $parsed['scheme'] === 'https') {
                URL::forceScheme('https');
            }
        }

        // Dynamic subfolder auto-detection if accessing via subfolder (e.g. /test)
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])) {
            $scriptDir = trim(str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME'])), '/');
            if ($scriptDir !== '' && ! empty($scriptDir) && (empty($parsed['path']) || $parsed['path'] === '/')) {
                $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                URL::forceRootUrl($scheme.'://'.$_SERVER['HTTP_HOST'].'/'.$scriptDir);
            }
        }
    }
}
