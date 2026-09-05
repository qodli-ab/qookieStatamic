@extends('statamic::layout')

@section('title', 'QookieQloud')

@section('content')
    @php
        $registered = (bool) data_get($domainStatus, 'registered');
        $statusLabel = $registered ? 'Verified' : 'Needs verification';
        $statusClass = $registered ? 'is-good' : 'is-warning';
        $enabledLabel = $enabled ? 'Active' : 'Paused';
        $visitorMode = $loadForAuthenticated ? 'All visitors' : 'Public visitors';
        $sourceLabel = data_get($stats, 'source') === 'api' ? 'Live data' : 'Waiting for data';
    @endphp

    <div class="qoq-dashboard">
        <header class="qoq-header">
            <div class="qoq-brand">
                <div class="qoq-mark" aria-hidden="true">
                    @include('qookie-statamic::partials.eyes')
                </div>
                <div>
                    <p class="qoq-eyebrow">QookieQloud for Statamic</p>
                    <h1>Consent Management</h1>
                    <p class="qoq-lead">Automatic consent loader, domain verification and key consent metrics for this site.</p>
                </div>
            </div>

            <div class="qoq-header-actions">
                <form method="POST" action="{{ route('qookie-statamic.cp.verify') }}">
                    @csrf
                    <button type="submit" class="qoq-button secondary">Verify domain</button>
                </form>
                <a href="{{ $dashboardUrl }}" target="_blank" rel="noopener" class="qoq-button primary external">Open QookieQloud</a>
            </div>
        </header>

        <section class="qoq-grid qoq-kpis" aria-label="QookieQloud overview">
            <article class="qoq-card qoq-kpi">
                <span class="qoq-icon status" aria-hidden="true"></span>
                <div>
                    <p>Domain status</p>
                    <strong class="{{ $statusClass }}">{{ $statusLabel }}</strong>
                </div>
            </article>

            <article class="qoq-card qoq-kpi">
                <span class="qoq-icon loader" aria-hidden="true"></span>
                <div>
                    <p>Loader</p>
                    <strong>{{ $enabledLabel }}</strong>
                </div>
            </article>

            <article class="qoq-card qoq-kpi">
                <span class="qoq-icon consent" aria-hidden="true"></span>
                <div>
                    <p>Consents today</p>
                    <strong>{{ number_format((int) data_get($stats, 'consents_today', 0)) }}</strong>
                </div>
            </article>

            <article class="qoq-card qoq-kpi">
                <span class="qoq-icon cookies" aria-hidden="true"></span>
                <div>
                    <p>Cookies found</p>
                    <strong>{{ number_format((int) data_get($stats, 'cookies', 0)) }}</strong>
                </div>
            </article>
        </section>

        <section class="qoq-grid qoq-main" aria-label="QookieQloud details">
            <article class="qoq-card qoq-panel">
                <div class="qoq-card-header">
                    <div>
                        <p class="qoq-eyebrow">Verification</p>
                        <h2>{{ $domain }}</h2>
                    </div>
                    <span class="qoq-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>

                <p class="qoq-muted">
                    The addon checks whether this domain is registered in QookieQloud using the same signed API flow as the WordPress plugin.
                </p>

                <div class="qoq-detail-list">
                    <div>
                        <span>Last check</span>
                        <strong>{{ data_get($domainStatus, 'checked_at') ? \Illuminate\Support\Carbon::parse(data_get($domainStatus, 'checked_at'))->timezone(config('app.timezone'))->format('Y-m-d H:i') : 'Not checked' }}</strong>
                    </div>
                    <div>
                        <span>Frontend injection</span>
                        <strong>{{ $enabled ? 'Injects automatically before the closing body tag' : 'Paused from plugin settings' }}</strong>
                    </div>
                    <div>
                        <span>Visitor mode</span>
                        <strong>{{ $visitorMode }}</strong>
                    </div>
                </div>
            </article>

            <article class="qoq-card qoq-panel">
                <div class="qoq-card-header">
                    <div>
                        <p class="qoq-eyebrow">Consent Overview</p>
                        <h2>{{ $sourceLabel }}</h2>
                    </div>
                    <a href="{{ $dashboardUrl }}" target="_blank" rel="noopener" class="qoq-card-link external">View analytics</a>
                </div>

                <div class="qoq-breakdown">
                    <div class="accepted">
                        <span>Accepted</span>
                        <strong>{{ number_format((int) data_get($stats, 'consents_accepted', 0)) }}</strong>
                    </div>
                    <div class="rejected">
                        <span>Rejected</span>
                        <strong>{{ number_format((int) data_get($stats, 'consents_rejected', 0)) }}</strong>
                    </div>
                    <div class="custom">
                        <span>Custom</span>
                        <strong>{{ number_format((int) data_get($stats, 'consents_custom', 0)) }}</strong>
                    </div>
                </div>

                <div class="qoq-detail-list">
                    <div>
                        <span>This week</span>
                        <strong>{{ number_format((int) data_get($stats, 'consents_week', 0)) }}</strong>
                    </div>
                    <div>
                        <span>This month</span>
                        <strong>{{ number_format((int) data_get($stats, 'consents_month', 0)) }}</strong>
                    </div>
                    <div>
                        <span>Total consents</span>
                        <strong>{{ number_format((int) data_get($stats, 'consents_total', 0)) }}</strong>
                    </div>
                </div>
            </article>

            <article class="qoq-card qoq-panel">
                <div class="qoq-card-header">
                    <div>
                        <p class="qoq-eyebrow">Cookie Scan</p>
                        <h2>{{ data_get($stats, 'scan_status', 'Unknown') }}</h2>
                    </div>
                    <a href="{{ $dashboardUrl }}" target="_blank" rel="noopener" class="qoq-card-link external">View cookies</a>
                </div>

                <div class="qoq-detail-list flush">
                    <div>
                        <span>Cookies</span>
                        <strong>{{ number_format((int) data_get($stats, 'cookies', 0)) }}</strong>
                    </div>
                    <div>
                        <span>Trackers</span>
                        <strong>{{ number_format((int) data_get($stats, 'trackers', 0)) }}</strong>
                    </div>
                    <div>
                        <span>Pages scanned</span>
                        <strong>{{ data_get($stats, 'pages_scanned', 'N/A') }}</strong>
                    </div>
                    <div>
                        <span>Last scan</span>
                        <strong>{{ data_get($stats, 'updated_local', 'Not available') }}</strong>
                    </div>
                </div>
            </article>

            <article class="qoq-card qoq-panel qoq-panel-accent">
                <p class="qoq-eyebrow">Banner Settings</p>
                <h2>Plugin settings</h2>
                <p class="qoq-muted">Control how the QookieQloud loader behaves on this Statamic site.</p>

                <form method="POST" action="{{ route('qookie-statamic.cp.update') }}" class="qoq-settings-form">
                    @csrf
                    <label class="qoq-toggle-row">
                        <span>
                            <strong>Load consent manager</strong>
                            <em>Automatically inject QookieQloud on public HTML pages.</em>
                        </span>
                        <input type="hidden" name="enabled" value="0">
                        <input type="checkbox" name="enabled" value="1" @checked($enabled)>
                    </label>

                    <label class="qoq-toggle-row">
                        <span>
                            <strong>Include logged-in visitors</strong>
                            <em>Useful for previewing the banner while editing the site.</em>
                        </span>
                        <input type="hidden" name="load_for_authenticated" value="0">
                        <input type="checkbox" name="load_for_authenticated" value="1" @checked($loadForAuthenticated)>
                    </label>

                    <div class="qoq-actions">
                        <button type="submit" class="qoq-button primary">Save settings</button>
                        <a href="{{ $dashboardUrl }}" target="_blank" rel="noopener" class="qoq-button secondary external">Open dashboard</a>
                    </div>
                </form>

                @if (data_get($stats, 'error'))
                    <p class="qoq-warning">Stats could not be fetched: {{ data_get($stats, 'error') }}</p>
                @endif
            </article>
        </section>
    </div>
@endsection
