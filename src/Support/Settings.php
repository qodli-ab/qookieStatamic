<?php

namespace Qodli\QookieStatamic\Support;

use Illuminate\Support\Facades\File;

class Settings
{
    public function all(): array
    {
        return array_merge($this->defaults(), $this->stored());
    }

    public function enabled(): bool
    {
        return (bool) $this->all()['enabled'];
    }

    public function loadForAuthenticated(): bool
    {
        return (bool) $this->all()['load_for_authenticated'];
    }

    public function save(array $settings): void
    {
        $data = [
            'enabled' => (bool) ($settings['enabled'] ?? false),
            'load_for_authenticated' => (bool) ($settings['load_for_authenticated'] ?? false),
        ];

        File::ensureDirectoryExists(dirname($this->path()));
        File::put($this->path(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }

    private function defaults(): array
    {
        return [
            'enabled' => (bool) config('qookie-statamic.enabled', true),
            'load_for_authenticated' => (bool) config('qookie-statamic.load_for_authenticated', false),
        ];
    }

    private function stored(): array
    {
        if (! File::exists($this->path())) {
            return [];
        }

        $settings = json_decode(File::get($this->path()), true);

        return is_array($settings) ? $settings : [];
    }

    private function path(): string
    {
        return storage_path('app/qookie-statamic/settings.json');
    }
}
