<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Billing POS (Laravel 11 + TailwindCSS + Alpine.js)

Production-ready Point of Sale Billing application featuring dashboard analytics, POS screen, inventory, customers, sales, reports, roles, settings, and backups.

### Features
- Authentication with Breeze (Blade), Sanctum API-ready
- POS billing screen with cart, payments, discounts, taxes
- Inventory and categories management with image uploads
- Customers with loyalty points and purchase history
- Sales, PDF invoice and Excel exports
- Reports with filters and Chart.js graphs
- Role-based access: Admin, Manager, Cashier
- Settings page: store info, tax, currency, payment methods
- Database backups (Spatie Laravel Backup)
- TailwindCSS + Alpine.js UI, dark/light mode, toasts

### Getting Started
1) Install dependencies
```bash
composer install
npm install
```
2) Configure environment
```bash
cp .env.example .env
php artisan key:generate
```
3) Database
```bash
# Update DB_* in .env for MySQL; or keep SQLite (database/database.sqlite)
php artisan migrate --seed
php artisan storage:link
```
4) Build assets
```bash
npm run build # or: npm run dev
```
5) Run
```bash
php artisan serve
```

Default admin: admin@example.com / password
Update password after first login.

### Environment
- `APP_CURRENCY` (default `INR`)
- `APP_LOCALE` / `locale.default` setting (`en` or `hi`)
- Queue driver defaults to `database`; run `php artisan queue:work`
- Ensure `MAIL_MAILER` is configured to deliver invoice emails (see `MAILER_SETUP.md` for Gmail-ready snippet)

### Laravel Sail (Docker)
```bash
php artisan sail:install --services=mysql,redis,mailpit
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

### Deployment Notes
- **cPanel / Shared Hosting**
  - Upload project to root, point domain to `public/`
  - Configure `.env`, run `php artisan migrate --force`
  - Set cron for queue: `* * * * * php /path/artisan queue:work --once`
  - Schedule backups: `php artisan backup:run --only-db`
- **Laravel Forge / VPS**
  - Build script: `php artisan migrate --force && npm ci && npm run build`
  - Enable queue worker and scheduler
  - Configure storage symlink & backup disk
- **Backups**
  - UI trigger in `Settings` > `Database Backups`
  - Configure `config/backup.php` disk credentials for cloud storage

### Packages
- laravel/breeze
- laravel/sanctum
- barryvdh/laravel-dompdf
- maatwebsite/excel
- spatie/laravel-backup
- alpinejs, sweetalert2, chart.js, notyf

### Deployment
- Ready for cPanel, Forge, or VPS
- Set APP_ENV=production, APP_DEBUG=false
- Configure queue (database) and schedule backups
- Run: php artisan storage:link; php artisan migrate --force

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
