<?php

namespace Qodli\QookieStatamic\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use RuntimeException;

class Connection
{
    private function path(): string
    {
        return config('qookie-statamic.connection_path', storage_path('app/qookie-statamic/connection.enc'));
    }

    public function read(): ?array
    {
        if (!File::exists($this->path())) return null;
        return json_decode(Crypt::decryptString(File::get($this->path())), true, 512, JSON_THROW_ON_ERROR);
    }

    public function save(array $data): void
    {
        if (!preg_match('/\Aqq_sec_[a-f0-9]{64}\z/D', $data['server_secret'] ?? '')
            || !preg_match('/\Aqq_pk_[a-f0-9]{64}\z/D', $data['site_key'] ?? '')
            || !is_string($data['domain'] ?? null) || empty($data['installation_id'])) {
            throw new RuntimeException('Invalid connection response.');
        }
        $record = array_intersect_key($data, array_flip(['installation_id', 'domain', 'site_key', 'server_secret', 'scopes']));
        File::ensureDirectoryExists(dirname($this->path()), 0700);
        File::replace($this->path(), Crypt::encryptString(json_encode($record, JSON_THROW_ON_ERROR)), 0600);
    }

    public function forget(): void { File::delete($this->path()); }

    public function publicData(): ?array
    {
        $data = $this->read();
        return $data ? array_intersect_key($data, array_flip(['installation_id', 'domain', 'site_key'])) : null;
    }
}
