@php
    $quota = is_array($stats['page_quota'] ?? null) ? $stats['page_quota'] : [];
    $quotaState = ($quota['restriction_active'] ?? false) === true ? 'restricted' : (($quota['over_limit'] ?? null) === true ? 'over' : (($quota['over_limit'] ?? null) === false ? 'within' : 'pending'));
@endphp
<section class="qoq-live-card">
    <h2>{{ __('qookie-statamic::dashboard.quota_quota') }}</h2>
    <p role="status">{{ __('qookie-statamic::dashboard.quota_'.$quotaState) }}</p>
    <dl class="qoq-live-rows">
    @foreach(['verified_count', 'page_limit', 'verified_at', 'last_attempt_status', 'last_attempt_at', 'notified_at', 'effective_at'] as $key)
        <div><dt>{{ __('qookie-statamic::dashboard.quota_'.$key) }}</dt><dd>{{ $key === 'page_limit' && isset($quota[$key]) && is_numeric($quota[$key]) && $quota[$key] <= 0 ? __('qookie-statamic::dashboard.quota_unlimited') : ($quota[$key] ?? '—') }}</dd></div>
    @endforeach
    </dl>
</section>
