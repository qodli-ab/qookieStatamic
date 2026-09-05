# QookieQloud Consent Management for Statamic

Statamic addon that loads the QookieQloud consent manager on public pages.

## Installation

Add the addon as a path repository in your Statamic project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../qookie-Statamic"
        }
    ],
    "require": {
        "qodli/qookie-statamic": "*"
    }
}
```

Then run:

```bash
composer update qodli/qookie-statamic
php please vendor:publish --tag=qookie-statamic-config
```

## Usage

Add the tag before `</body>` in your main Antlers layout:

```antlers
{{ qookieqloud }}
```

By default, the loader is only rendered for public visitors.

## Configuration

Publish the config file or set environment variables:

```env
QOOKIEQLOUD_ENABLED=true
QOOKIEQLOUD_LOADER_URL=https://js.qookieqloud.com/consentLoader.js
QOOKIEQLOUD_APP_URL=https://app.qookieqloud.com
QOOKIEQLOUD_LOAD_FOR_AUTHENTICATED=false
```
