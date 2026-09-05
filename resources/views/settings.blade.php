@extends('statamic::layout')

@section('title', 'QookieQloud')

@section('content')
    <div class="max-w-2xl">
        <header class="mb-6">
            <h1>QookieQloud</h1>
            <p class="text-gray-700 mt-2">
                Consent manager integration for this Statamic site.
            </p>
        </header>

        <div class="card p-4">
            <dl class="divide-y">
                <div class="py-3 flex justify-between gap-4">
                    <dt class="font-bold">Status</dt>
                    <dd>{{ $enabled ? 'Enabled' : 'Disabled' }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="font-bold">Loader URL</dt>
                    <dd class="text-right break-all">{{ $loaderUrl }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="font-bold">Authenticated visitors</dt>
                    <dd>{{ $loadForAuthenticated ? 'Loads script' : 'Skipped' }}</dd>
                </div>
            </dl>
        </div>

        <div class="card p-4 mt-4">
            <h2 class="mb-2">Install in your layout</h2>
            <p class="text-gray-700 mb-3">
                Add this tag before the closing body tag in your main Antlers layout:
            </p>
            <pre class="bg-gray-900 text-white p-3 rounded text-sm overflow-x-auto">{{ "{{ qookieqloud }}" }}</pre>
        </div>

        <div class="mt-4">
            <a href="{{ rtrim($appUrl, '/') }}/app" target="_blank" rel="noopener" class="btn-primary">
                Open QookieQloud dashboard
            </a>
        </div>
    </div>
@endsection
