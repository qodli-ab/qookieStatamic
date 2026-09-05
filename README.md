# QookieQloud Consent Management for Statamic

QookieQloud Consent Management connects a Statamic site to QookieQloud and automatically loads the consent manager on public pages.

QookieQloud helps site owners handle cookie consent, cookie categorisation, banner presentation and consent records from one central control panel. It combines automated cookie scanning, AI-assisted cookie classification, domain verification and privacy insights so teams can understand what their site loads, how cookies are categorised and where consent coverage can be improved.

When a Statamic site is connected to QookieQloud, the consent manager can be loaded automatically while the operational work stays in QookieQloud: banner design, consent categories, cookie scan results, Privacy Audit Score and domain-level settings. This addon keeps the Statamic side intentionally lightweight: install it, enable the loader, verify the domain and manage the consent experience in QookieQloud.

## Features

- Automatic consent manager injection on public Statamic pages.
- No manual layout edits required after installation.
- Domain verification from the Statamic control panel.
- Dashboard overview with consent and cookie scan metrics.
- Privacy Audit Score and domain insights from QookieQloud.
- AI-assisted cookie classification in the QookieQloud platform.
- Optional loading for authenticated visitors.
- Manual Antlers tag available for custom implementations.

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
php please vendor:publish --tag=qookie-statamic
```

## Usage

Once installed, the addon injects the QookieQloud loader before the closing `</body>` tag on public HTML pages.

No manual layout changes are required.

If you want to place the loader manually instead, the addon also provides this Antlers tag:

```antlers
{{ qookieqloud }}
```

By default, the loader is only rendered for public visitors.

## Configuration

The addon works without manual setup. Site owners can manage the loader from the QookieQloud page in the Statamic control panel.

These optional environment variables are only used as defaults before settings are saved in the control panel:

```env
QOOKIEQLOUD_ENABLED=true
QOOKIEQLOUD_LOAD_FOR_AUTHENTICATED=false
```

The control panel page includes domain verification and dashboard metrics using the same signed API flow as the WordPress plugin.

## Author

QookieQloud Consent Management for Statamic is developed and maintained by Qodli AB.

- Website: https://qodli.se
- Email: hello@qodli.se

## Support

For help with the addon, domain verification or your QookieQloud account, contact QookieQloud at hello@qookieqloud.com or visit the helpcenter: https://qookieqloud.com/helpdesk/.
