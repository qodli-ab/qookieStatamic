<?php

namespace Qodli\QookieStatamic\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class QookieQloudApi
{
    private const APP_URL = 'https://app.qookieqloud.com';
    private const CHECK_DOMAIN_URL = self::APP_URL . '/api/v1/check-domain';
    private const STATS_URL = self::APP_URL . '/api/v1/domainstats';
    private const SECRET = 'dapfe1?Wutfix/cerhig';
    private const CACHE_MINUTES = 30;
    private const TIMEOUT = 8;

    public function checkDomain(string $domain, bool $refresh = false): array
    {
        return $this->remember("domain:{$domain}", $refresh, function () use ($domain): array {
            $response = $this->post(self::CHECK_DOMAIN_URL, $domain);

            if (! $response['ok']) {
                return [
                    'status' => 'not_checked',
                    'registered' => false,
                    'error' => $response['error'],
                    'checked_at' => now()->toIso8601String(),
                ];
            }

            $registered = (bool) data_get($response['data'], 'registered', false);

            return [
                'status' => $registered ? 'registered' : 'not_registered',
                'registered' => $registered,
                'error' => null,
                'checked_at' => now()->toIso8601String(),
            ];
        });
    }

    public function stats(string $domain, bool $refresh = false): array
    {
        return $this->remember("stats:{$domain}", $refresh, function () use ($domain): array {
            $response = $this->post(self::STATS_URL, $domain);

            if (! $response['ok']) {
                return $this->emptyStats($response['error']);
            }

            $data = $response['data'];
            $updatedAt = data_get($data, 'updated_at');

            return array_merge($data, [
                'consents_total' => (int) data_get($data, 'consents_total', 0),
                'consents_today' => (int) data_get($data, 'consents_today', 0),
                'consents_accepted' => (int) data_get($data, 'consents_accepted', data_get($data, 'consents_today', 0)),
                'consents_rejected' => (int) data_get($data, 'consents_rejected', 0),
                'consents_custom' => (int) data_get($data, 'consents_custom', 0),
                'consents_week' => (int) data_get($data, 'consents_week', data_get($data, 'consents_total', 0)),
                'consents_month' => (int) data_get($data, 'consents_month', data_get($data, 'consents_total', 0)),
                'cookies' => (int) data_get($data, 'cookies', 0),
                'trackers' => (int) data_get($data, 'trackers', 0),
                'audit_score' => data_get($data, 'audit_score'),
                'audit_risk_level' => data_get($data, 'audit_risk_level'),
                'pages_scanned' => data_get($data, 'pages_scanned', 'N/A'),
                'scan_status' => data_get($data, 'scan_status', 'Unknown'),
                'updated_local' => $this->formatUpdatedAt($updatedAt),
                'source' => 'api',
                'error' => null,
            ]);
        });
    }

    public function forget(string $domain): void
    {
        Cache::forget($this->cacheKey("domain:{$domain}"));
        Cache::forget($this->cacheKey("stats:{$domain}"));
    }

    public function dashboardUrl(): string
    {
        return self::APP_URL;
    }

    private function post(string $url, string $domain): array
    {
        $secret = self::SECRET;
        $timestamp = time();
        $signature = hash_hmac('sha256', $domain . $timestamp, $secret);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(self::TIMEOUT)
                ->withHeaders([
                    'X-Timestamp' => $timestamp,
                    'X-Signature' => $signature,
                    'Authorization' => 'Bearer ' . $secret,
                ])
                ->post($url, ['domain' => $domain]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage(), 'data' => []];
        }

        if (! $response->successful() || ! is_array($response->json())) {
            return [
                'ok' => false,
                'error' => 'Unexpected API response: HTTP ' . $response->status(),
                'data' => [],
            ];
        }

        return ['ok' => true, 'error' => null, 'data' => $response->json()];
    }

    private function remember(string $key, bool $refresh, callable $callback): array
    {
        $cacheKey = $this->cacheKey($key);

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_MINUTES), $callback);
    }

    private function cacheKey(string $key): string
    {
        return 'qookie-statamic:' . $key;
    }

    private function emptyStats(?string $error = null): array
    {
        return [
            'consents_total' => 0,
            'consents_today' => 0,
            'consents_accepted' => 0,
            'consents_rejected' => 0,
            'consents_custom' => 0,
            'consents_week' => 0,
            'consents_month' => 0,
            'cookies' => 0,
            'trackers' => 0,
            'audit_score' => null,
            'audit_risk_level' => null,
            'pages_scanned' => 'N/A',
            'scan_status' => 'Unknown',
            'updated_local' => 'Not available',
            'source' => 'fallback',
            'error' => $error,
        ];
    }

    private function formatUpdatedAt(mixed $updatedAt): string
    {
        if (! $updatedAt) {
            return 'Not available';
        }

        try {
            $date = is_numeric($updatedAt)
                ? Carbon::createFromTimestamp((int) $updatedAt)
                : Carbon::parse($updatedAt);

            return $date->timezone(config('app.timezone'))->format('Y-m-d H:i');
        } catch (\Throwable) {
            return 'Not available';
        }
    }
}
