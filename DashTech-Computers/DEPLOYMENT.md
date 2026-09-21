# DashTech deployment checklist

Before publishing this update, configure these server environment variables, `php/config.local.php` and fill in the values:

- `SMTP_PASSWORD`, `RECAPTCHA_SECRET_KEY`, and `ADMIN_PASSWORD_HASH`
- `DB_NAME`, `DB_USER`, and `DB_PASSWORD`
- Optional configuration is listed in `php/config.example.php`.

The previous SMTP password, database password, and reCAPTCHA secret were stored in files in this project. Rotate all three immediately in the relevant provider dashboards, then set the new values in the hosting control panel or the blocked `config.local.php` file.

## Payments

Payment cannot be safely enabled without selecting a provider and supplying its private API keys, webhook secret, pricing/amount rules, and business account details. For Nigeria, Paystack is a sensible default. Once those are supplied, implement checkout on a dedicated payment page and verify provider webhooks server-side before marking any booking as paid.
