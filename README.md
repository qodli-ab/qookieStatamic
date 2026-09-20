# QookieQloud Consent Management for Statamic

Connect your Statamic website to QookieQloud, automatically load its cookie banner and view consent and cookie metrics in the Statamic control panel. Manage banner design, consent categories and domain settings in QookieQloud.

## Requirements

- PHP 8.1 or later and Statamic 5 or 6, subject to your Statamic version's own PHP requirements.
- A QookieQloud account with access to the domain you want to connect. Partners can select customer domains they are authorised to manage.
- A Statamic super user to connect and manage the integration.
- QookieQloud v2 access enabled for your account role during the rollout.

## Installation

Add the addon repository to your Statamic project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:qodli-ab/qookieStatamic.git"
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

For local development, use a path repository pointing to your addon checkout instead:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../qookie-Statamic"
        }
    ]
}
```

## Connect your website

1. Open **QookieQloud** in the Statamic control panel.
2. Click **Connect to QookieQloud**. A new window opens at `https://app.qookieqloud.com`.
3. Sign in, select a domain (or add one if it is missing), and approve the installation.
4. Return to Statamic, check **Enable the banner**, and save your settings.
5. Clear your site's full-page cache.

There are no API keys to copy and no required environment variables for a normal v2 connection. The callback URL is generated from the installation's route configuration, including a custom control-panel path or host. The browser returns to Statamic; QookieQloud does not need to make an inbound server request to your installation.

The connection grants API access only. It does not allow the plugin to sign in to your QookieQloud account.

## Dashboard and settings

Once connected, the plugin shows consent totals, today's consent choices, detected cookies, scan information and the Privacy Audit Score. Statistics belong to the selected QookieQloud domain. If statistics cannot be loaded, the dashboard shows unavailable values rather than falling back to the old API.

- **Enable the banner:** automatically inject the loader before the closing `</body>` tag on public HTML pages.
- **Show the banner for logged-in users:** useful when previewing while editing your website. Disabled by default.
- **Change connection:** confirm the action, then choose a domain in QookieQloud. The current local connection is kept until the new connection succeeds.
- **Disconnect:** confirm to revoke this installation's private API access and remove its local connection. The domain remains in QookieQloud. Clear the site cache afterwards.

Banner design and consent settings are managed in QookieQloud. The plugin includes its own logo, styles and English/Swedish interface translations.

For custom templates, the addon also provides an Antlers tag:

```antlers
{{ qookieqloud }}
```

## Local development and multiple installations

You can connect `http://127.0.0.1:8027` and the live website to the same QookieQloud domain. Each connection receives its own private server key, while the domain's public Site Key and banner configuration are shared. An approved connection adds its origin to the public key's allowed origins. HTTP is supported for explicit localhost/127.0.0.1 development origins; other origins require HTTPS.

Both installations report cookies and consents into the same domain, so local tests contribute to production statistics. Browser storage is separated by origin and uses the original Qookie storage names without added installation prefixes or suffixes. Earlier v2 storage names are migrated when accessed.

“Latest contact” in QookieQloud records the last banner configuration request, not every page view or plugin dashboard request. Banner configuration can be cached in the browser for two hours. The v2 badge appears only when that latest configuration request used v2; a later v1 request replaces it.

Disconnecting one installation does not revoke another installation's private key. Currently, disconnecting also leaves that origin on the shared public key's allowlist. A control panel hosted on a different origin from the public website does not automatically authorise the public website's origin.

## Configuration

Optional defaults, used until settings are saved in the control panel:

```env
QOOKIEQLOUD_ENABLED=true
QOOKIEQLOUD_LOAD_FOR_AUTHENTICATED=false
```

Advanced configuration (normally leave these unchanged):

```env
QOOKIEQLOUD_V2_ENABLED=true
QOOKIEQLOUD_APP_URL=https://app.qookieqloud.com
QOOKIEQLOUD_V2_LOADER_URL=https://cf-cdn.qookieqloud.com/v2/consentLoader.js
```

The private installation credentials are encrypted using the site's Laravel `APP_KEY` and stored in `storage/app/qookie-statamic/connection.enc`. Preserve that key and private storage across deployments. Do not publish the file or copy an existing installation's credentials into another environment; connect each installation separately.

The public Site Key is included in the banner script. The private server key is used only for server-to-server API requests.

## Upgrading from the legacy integration

V2 is enabled by default in this version and requires a connection. An unconnected v2 installation does not inject the banner and does not automatically fall back to v1.

Plan the connection when upgrading an existing site. To retain the legacy behaviour temporarily, explicitly set `QOOKIEQLOUD_V2_ENABLED=false` and refresh Laravel's configuration cache if used. Remove that override when you are ready to connect via v2, then verify the banner and clear the site's page cache. Existing v1 backend endpoints remain available.


## Support

Contact [hello@qookieqloud.com](mailto:hello@qookieqloud.com) or visit the [QookieQloud helpdesk](https://qookieqloud.com/helpdesk/).

Developed and maintained by [Qodli AB](https://qodli.se).
