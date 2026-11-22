<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends Model
{
    protected $fillable = [
        'tenant_id',
        'session_id',
        'ip_address',
        'path',
        'referer',
        'user_agent',
        'visited_on',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Parse user_agent into human-readable device, OS, and browser details.
     *
     * @return array{device: string, os: string, browser: string, is_bot: bool}
     */
    public function deviceSummary(): array
    {
        $ua = trim((string) $this->user_agent);
        if ($ua === '') {
            return [
                'device' => 'Unknown Device',
                'os' => 'Unknown OS',
                'browser' => 'Unknown',
                'is_bot' => false,
            ];
        }

        $isBot = (bool) preg_match('/bot|crawl|spider|slurp|curl|wget|headless|uptime|monitor/i', $ua);

        $os = match (true) {
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Other OS',
        };

        $device = match (true) {
            $isBot => 'Bot / Crawler',
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') && preg_match('/;\s*([^;)]+?)\s+Build\//i', $ua, $m) === 1 => trim($m[1]),
            str_contains($ua, 'Android') && str_contains($ua, 'Mobile') => 'Android Phone',
            str_contains($ua, 'Android') => 'Android Tablet',
            str_contains($ua, 'Macintosh') => 'Mac',
            str_contains($ua, 'Windows') => 'Windows PC',
            str_contains($ua, 'Linux') => 'Linux PC',
            default => 'Unknown Device',
        };

        $browser = match (true) {
            str_contains($ua, 'WhatsApp') => 'WhatsApp',
            str_contains($ua, 'Instagram') => 'Instagram',
            str_contains($ua, 'FBAN') || str_contains($ua, 'FBAV') => 'Facebook App',
            str_contains($ua, 'Edg/') || str_contains($ua, 'EdgA/') => 'Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'Chrome/') || str_contains($ua, 'CriOS/') => 'Chrome',
            str_contains($ua, 'Firefox/') || str_contains($ua, 'FxiOS/') => 'Firefox',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Browser',
        };

        return [
            'device' => $device,
            'os' => $os,
            'browser' => $browser,
            'is_bot' => $isBot,
        ];
    }
}
