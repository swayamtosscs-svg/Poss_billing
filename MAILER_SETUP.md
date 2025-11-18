## Mailer Configuration (Gmail)

Use the following values inside your local `.env` file to send reset links and invoices through Gmail. Replace the password with a Gmail "App Password" generated from account security settings.

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=swayam.toss.cs@gmail.com
MAIL_PASSWORD=your-google-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="swayam.toss.cs@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

After updating `.env`, run:

```
php artisan config:clear
php artisan queue:restart
```

Then trigger a password reset (Auth → Forgot Password) to verify that the email arrives.

