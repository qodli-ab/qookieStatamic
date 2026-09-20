@extends('statamic::layout')
@section('title', 'QookieQloud')
@push('head')
@include('qookie-statamic::partials.connection-styles')
<style>
.qoq-live-hero {grid-template-columns:1.4fr 1fr;padding:32px;gap:28px}
.qoq-live-hero .qoq-connect-wordmark {margin-bottom:22px}
.qoq-live-hero h1 {font-size:30px;overflow-wrap:anywhere}
.qoq-live-hero .qoq-connect-guide {width:100%}
.qoq-live-kpis {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin:24px 0}
.qoq-live-card {background:var(--qoq-card,#fff);border:1px solid var(--qoq-border,#dce5df);border-radius:10px;padding:24px;color:var(--qoq-text,#18352a)}
.qoq-live-card h2 {font-size:17px;font-weight:700;margin:0 0 20px}
.qoq-live-label {font-size:13px;color:var(--qoq-muted,#63766b)}
.qoq-live-number {font-size:28px;font-weight:700;margin-top:6px}
.qoq-live-kpis .qoq-live-card {display:flex;align-items:center;gap:14px;padding:20px}
.qoq-live-kpis .qoq-live-card > div {min-width:0}
.qoq-live-settings input:focus-visible {outline:3px solid var(--qoq-accent-dark);outline-offset:4px}
.qoq-live-grid {display:grid;grid-template-columns:1.3fr 1fr;gap:20px}
.qoq-live-rows {margin:0}
.qoq-live-rows > div {display:flex;justify-content:space-between;gap:20px;padding:12px 0;border-bottom:1px solid var(--qoq-border,#dce5df)}
.qoq-live-rows dd {font-weight:650;text-align:right;overflow-wrap:anywhere}
.qoq-live-rows dt {color:var(--qoq-muted,#63766b)}
.qoq-live-settings label {display:flex;gap:10px;align-items:center;margin:18px 0;font-size:14px}
.qoq-live-note {font-size:13px;line-height:1.7;color:var(--qoq-muted,#63766b);margin:18px 0}
.qoq-live-alert {padding:16px 20px;border:1px solid #d5c590;background:#fff9e7;color:#66521e;border-radius:8px;margin-top:20px;font-size:14px}
.qoq-live-actions {display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-top:22px}
.qoq-live-hero .qoq-live-rows dt {color:#c5d7ce}.qoq-live-hero .qoq-live-rows dd {color:#fff}
.qoq-live-hero .qoq-live-rows > div {border-color:#365346}
@media(max-width:900px){.qoq-live-hero,.qoq-live-grid{grid-template-columns:1fr}.qoq-live-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:480px){.qoq-live-kpis{grid-template-columns:1fr}.qoq-live-hero{padding:22px}}
#content-card:has(.qoq-live-page) {padding-left:0;padding-right:0}
#content-card .max-w-page:has(.qoq-live-page) {max-width:none}
/* Full-width brand backdrop with aligned, boxed dashboard content. */
.qoq-dashboard.qoq-live-page {max-width:none;width:100%;padding:0 0 48px}
.qoq-live-container {width:100%;max-width:1180px;margin:0 auto}
.qoq-live-band {padding:38px 32px 30px;background:radial-gradient(ellipse at 85% 0%,#306348 0%,transparent 60%),linear-gradient(120deg,#10291f,#173d2e);border-radius:12px}
.qoq-live-band .qoq-live-hero {padding:0 0 30px;background:none;border-radius:0;gap:64px}
.qoq-live-band .qoq-connect-logo {width:174px}
.qoq-live-band .qoq-connect-wordmark {margin-bottom:26px}
.qoq-live-band .qoq-live-hero h1 {font-size:clamp(28px,3vw,38px);max-width:none}
.qoq-live-band .qoq-connect-guide {background:#ffffff06;border-color:#ffffff20;padding:24px}
.qoq-live-band .qoq-live-kpis {margin:0;gap:14px}
.qoq-live-band .qoq-live-card {background:#ffffff09;border-color:#ffffff24;color:#fff;border-radius:10px;padding:22px}
.qoq-live-band .qoq-live-label {color:#b9d1c3;font-size:12px}
.qoq-live-band .qoq-icon {background:#7ed9ab18;color:#83d7ae}
.qoq-live-band .qoq-icon::before {background:#83d7ae}
.qoq-live-body {padding-top:28px}
.qoq-live-body .qoq-live-alert {margin:0 0 22px}
@media(max-width:1240px){.qoq-live-body{padding-left:24px;padding-right:24px}}
@media(max-width:900px){.qoq-live-band{padding:28px 24px}.qoq-live-band .qoq-live-hero{gap:28px}.qoq-live-band .qoq-connect-guide{max-width:none}}
@media(max-width:480px){.qoq-live-band{padding:24px 18px}.qoq-live-body{padding-left:8px;padding-right:8px}.qoq-live-band .qoq-live-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.qoq-live-band .qoq-live-card{padding:16px;gap:10px;flex-direction:column;align-items:flex-start}}
.qoq-connection-button {display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 16px;border:1px solid var(--qoq-border);border-radius:7px;background:var(--qoq-soft);color:var(--qoq-text);font-size:13px;font-weight:700;cursor:pointer}
.qoq-connection-button:hover {border-color:var(--qoq-accent-dark)}
.qoq-connection-danger {color:#b42332;background:#fff1f2;border-color:#f2b8bf}
.qoq-connection-danger:hover {background:#ffe2e6;border-color:#b42332}
.qoq-connection-button:focus-visible {outline:3px solid var(--qoq-accent-dark);outline-offset:3px}
.qoq-connection-button:disabled {opacity:.55;cursor:wait}
</style>
@endpush
@section('content')
@php
    $metric = fn ($key) => isset($stats[$key]) ? number_format($stats[$key]) : '—';
@endphp
<div class="qoq-dashboard qoq-connect qoq-live-page">
    <div class="qoq-live-band">
    <div class="qoq-live-container">
    <section class="qoq-connect-hero qoq-live-hero">
        <div>
            <div class="qoq-connect-wordmark"><img src="data:image/png;base64,{{ $logoBase64 }}" alt="QookieQloud" width="240" class="qoq-connect-logo"></div>
            <p class="qoq-connect-kicker">{{ __('qookie-statamic::dashboard.overview') }}</p>
            <h1>{{ $connection['domain'] }}<span class="qoq-connect-dot">.</span></h1>
            <p class="qoq-connect-lead">{{ __('qookie-statamic::dashboard.intro') }}</p>
            <a class="qoq-connect-cta" href="{{ $dashboardUrl }}" target="_blank" rel="noopener noreferrer">{{ __('qookie-statamic::dashboard.open_app') }} <span aria-hidden="true">↗</span></a>
        </div>
        <aside class="qoq-connect-guide">
            <h2>{{ __('qookie-statamic::dashboard.installation') }}</h2>
            <dl class="qoq-live-rows">
                <div><dt>{{ __('qookie-statamic::dashboard.api') }}</dt><dd>{{ $remoteStatus }}</dd></div>
                <div><dt>{{ __('qookie-statamic::dashboard.banner') }}</dt><dd>{{ __('qookie-statamic::dashboard.'.($settings['enabled'] ? 'active' : 'paused')) }}</dd></div>
                <div><dt>{{ __('qookie-statamic::dashboard.visitors') }}</dt><dd>{{ __('qookie-statamic::dashboard.'.($settings['load_for_authenticated'] ? 'all_visitors' : 'public_visitors')) }}</dd></div>
            </dl>
        </aside>
    </section>
    <div class="qoq-live-kpis">
        @foreach(['consents_today','cookies','audit_score','consents_total'] as $key)
        <section class="qoq-live-card"><span class="qoq-icon {{ ['consents_today'=>'consent', 'cookies'=>'cookies', 'audit_score'=>'audit', 'consents_total'=>'consent'][$key] }}" aria-hidden="true"></span><div><p class="qoq-live-label">{{ __('qookie-statamic::dashboard.'.$key) }}</p><p class="qoq-live-number">{{ $metric($key) }}@if($key === 'audit_score' && isset($stats[$key]))<small> / 100</small>@endif</p></div></section>
        @endforeach
    </div>
    </div>
    </div>
    <div class="qoq-live-container qoq-live-body">
    @if(!$stats)<p class="qoq-live-alert" role="status">{{ __('qookie-statamic::dashboard.unavailable') }}</p>@endif
    <div class="qoq-live-grid">
        <section class="qoq-live-card">
            <h2>{{ __('qookie-statamic::dashboard.consent_overview') }}</h2>
            <dl class="qoq-live-rows">
                @foreach(['consents_accepted','consents_rejected','consents_custom','consents_week','consents_month'] as $key)
                <div><dt>{{ __('qookie-statamic::dashboard.'.$key) }}</dt><dd>{{ $metric($key) }}</dd></div>
                @endforeach
            </dl>
        </section>
        <section class="qoq-live-card qoq-live-settings">
            <h2>{{ __('qookie-statamic::dashboard.settings') }}</h2>
            <form method="post" action="{{ route('qookie-statamic.cp.update') }}">@csrf
                <label class="qoq-toggle-row"><span><strong>{{ __('qookie-statamic::connection.enabled') }}</strong><em>{{ __('qookie-statamic::dashboard.enabled_hint') }}</em></span><input type="checkbox" role="switch" name="enabled" value="1" @checked($settings['enabled'])></label>
                <label class="qoq-toggle-row"><span><strong>{{ __('qookie-statamic::connection.authenticated') }}</strong><em>{{ __('qookie-statamic::dashboard.load_for_authenticated_hint') }}</em></span><input type="checkbox" role="switch" name="load_for_authenticated" value="1" @checked($settings['load_for_authenticated'])></label>
                <p class="qoq-live-note">{{ __('qookie-statamic::dashboard.settings_hint') }}</p>
                <button class="qoq-connect-cta" type="submit">{{ __('qookie-statamic::connection.save') }}</button>
            </form>
        </section>
        <section class="qoq-live-card">
            <h2>{{ __('qookie-statamic::dashboard.scan') }}</h2>
            <dl class="qoq-live-rows">
                @foreach(['cookies','pages_scanned'] as $key)
                <div><dt>{{ __('qookie-statamic::dashboard.'.$key) }}</dt><dd>{{ $metric($key) }}</dd></div>
                @endforeach
                <div><dt>{{ __('qookie-statamic::dashboard.last_scan') }}</dt><dd>{{ $stats['last_scan_at'] ?? '—' }}</dd></div>
            </dl>
        </section>
        <section class="qoq-live-card">
            <h2>{{ __('qookie-statamic::dashboard.connection') }}</h2>
            <p>{{ __('qookie-statamic::connection.connected', ['domain' => $connection['domain']]) }}</p>
            <p class="qoq-live-note">{{ __('qookie-statamic::connection.cache') }}</p>
            <div class="qoq-live-actions">
                <form id="qookie-connect-form" data-confirm="{{ __('qookie-statamic::dashboard.confirm_change') }}" method="post" target="qookie-connect" action="{{ route('qookie-statamic.cp.connect') }}">@csrf<button type="submit" class="qoq-connection-button">{{ __('qookie-statamic::connection.reconnect') }}</button></form>
                <form id="qookie-disconnect-form" data-confirm="{{ __('qookie-statamic::dashboard.confirm_disconnect', ['domain' => $connection['domain']]) }}" method="post" action="{{ route('qookie-statamic.cp.disconnect') }}">@csrf<button type="submit" class="qoq-connection-button qoq-connection-danger">{{ __('qookie-statamic::connection.disconnect') }}</button></form>
            </div>
        </section>
    </div>
</div>
</div>
@endsection
@section('scripts')
<script>
(() => {
    let popup;
    document.addEventListener('submit', event => {
        const form = event.target;
        if (!['qookie-connect-form', 'qookie-disconnect-form'].includes(form.id)) return;
        if (!window.confirm(form.dataset.confirm)) { event.preventDefault(); return; }
        if (form.id === 'qookie-connect-form') {
            popup = window.open('', 'qookie-connect', 'width=640,height=780');
        } else {
            form.querySelector('button').disabled = true;
        }
    });
    window.addEventListener('message', event => {
        if (event.origin === window.location.origin && event.source === popup && event.data?.type === 'qookie-connected') window.location.reload();
    });
})();
</script>
@endsection
