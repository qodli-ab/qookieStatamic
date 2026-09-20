@extends('statamic::layout')
@section('title', 'QookieQloud')
@push('head')
@include('qookie-statamic::partials.connection-styles')
@endpush
@section('content')
<div class="qoq-dashboard qoq-connect">
    <section class="qoq-connect-hero">
        <div class="qoq-connect-copy">
            <div class="qoq-connect-wordmark"><img src="data:image/png;base64,{{ $logoBase64 }}" alt="QookieQloud" width="240" class="qoq-connect-logo"></div>
            <p class="qoq-connect-kicker">{{ __('qookie-statamic::connection.for_statamic') }}</p>
            <h1>{{ __($connection ? 'qookie-statamic::connection.title' : 'qookie-statamic::connection.welcome') }}<span class="qoq-connect-dot">.</span></h1>
            <p class="qoq-connect-lead">{{ __($connection ? 'qookie-statamic::connection.manage_intro' : 'qookie-statamic::connection.welcome_intro') }}</p>
            <form id="qookie-connect-form" method="post" target="qookie-connect" action="{{ route('qookie-statamic.cp.connect') }}">@csrf
                <button type="submit" class="qoq-connect-cta">{{ __($connection ? 'qookie-statamic::connection.reconnect' : 'qookie-statamic::connection.connect') }} <span aria-hidden="true">↗</span></button>
            </form>
            <p class="qoq-connect-caption">{{ __('qookie-statamic::connection.popup_hint') }}</p>
        </div>
        <aside class="qoq-connect-guide">
            <h2>{{ __('qookie-statamic::connection.guide_title') }}</h2>
            <ol>
                @foreach(['login', 'choose', 'activate'] as $step)
                <li><span class="qoq-connect-number" aria-hidden="true">{{ $loop->iteration }}</span><div><h3>{{ __('qookie-statamic::connection.step_'.$step) }}</h3><p>{{ __('qookie-statamic::connection.step_'.$step.'_body') }}</p></div></li>
                @endforeach
            </ol>
        </aside>
    </section>
    @if($connection)
    <section class="qoq-card qoq-connect-settings">

    <form method="post" action="{{ route('qookie-statamic.cp.update') }}">@csrf
        <label><input type="checkbox" name="enabled" value="1" @checked($settings['enabled'])> {{ __('qookie-statamic::connection.enabled') }}</label>
        <label><input type="checkbox" name="load_for_authenticated" value="1" @checked($settings['load_for_authenticated'])> {{ __('qookie-statamic::connection.authenticated') }}</label>
        <button class="btn" type="submit">{{ __('qookie-statamic::connection.save') }}</button>
    </form>

        <p>{{ __('qookie-statamic::connection.connected', ['domain' => $connection['domain']]) }}</p>
        <p>{{ __('qookie-statamic::connection.status', ['status' => $remoteStatus]) }}</p>
        <form method="post" action="{{ route('qookie-statamic.cp.disconnect') }}">@csrf
            <button type="submit" class="btn">{{ __('qookie-statamic::connection.disconnect') }}</button>
        </form>

        <p>{{ __('qookie-statamic::connection.cache') }}</p>
    </section>
    @else
    <section class="qoq-connect-help" aria-label="{{ __('qookie-statamic::connection.help_title') }}">
        <div><p class="qoq-eyebrow">{{ __('qookie-statamic::connection.help_title') }}</p><h2>{{ __('qookie-statamic::connection.before_title') }}</h2><p>{{ __('qookie-statamic::connection.before_body') }}</p></div>
        <div class="qoq-connect-questions">
            @foreach(['account', 'changes', 'disconnect'] as $question)
            <details><summary>{{ __('qookie-statamic::connection.faq_'.$question) }}</summary><p>{{ __('qookie-statamic::connection.faq_'.$question.'_body') }}</p></details>
            @endforeach
        </div>
    </section>
    @endif
</div>
<script>
(() => {
    let popup;
    document.getElementById('qookie-connect-form').addEventListener('submit', () => {
        popup = window.open('', 'qookie-connect', 'width=640,height=780');
    });
    window.addEventListener('message', event => {
        if (event.origin === window.location.origin && event.source === popup && event.data?.type === 'qookie-connected') window.location.reload();
    });
})();
</script>
@endsection
