<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="referrer" content="no-referrer"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ __('qookie-statamic::connection.title') }}</title></head>
<body>
<p>{{ __($ok ? 'qookie-statamic::connection.success' : 'qookie-statamic::connection.failed') }}</p>
<a href="{{ route('qookie-statamic.cp.settings') }}">{{ __('qookie-statamic::connection.return') }}</a>
@if($ok)
<script>
if (window.opener && !window.opener.closed) {
    window.opener.postMessage({ type: 'qookie-connected' }, window.location.origin);
    window.close();
}
</script>
@endif
</body></html>
