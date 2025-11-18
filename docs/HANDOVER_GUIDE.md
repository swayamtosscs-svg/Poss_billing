# Billing POS – Project Handover Guide

This guide gives new developers enough context to run, extend, and operate the Billing POS platform. It focuses on the flow of data, the moving parts (controllers, services, jobs, queues, views), and the database connections/environments that keep everything running.

---

## 1. System Overview
- **Stack**: Laravel 11 (PHP 8.2+), MySQL or SQLite, TailwindCSS, Alpine.js, Chart.js, SweetAlert2, Notyf, Laravel Breeze (Blade), Sanctum-ready APIs.
- **Primary Features**: POS sales screen, invoicing (PDF + email), inventory & categories, customers with loyalty points, vendors/parties, purchases, stock adjustments, expenses & payments, reports/exports, settings, and support tickets.
- **Runtime Expectations**: Web server (`php artisan serve` or nginx/apache), queue worker (`php artisan queue:work`), scheduler (`php artisan schedule:work`), asset build (`npm run build`), and backup disk configured (`config/backup.php`).

---

## 2. Project Structure & Flow

| Layer | Location | Notes |
|-------|----------|-------|
| HTTP routes | `routes/web.php`, `routes/api.php`, `routes/auth.php` | Web routes are behind `auth` middleware; `Route::redirect('/', '/dashboard')` for home. |
| Controllers | `app/Http/Controllers/*` | RESTful resource controllers per module (products, sales, purchases, tickets, etc.). |
| Requests & validation | `app/Http/Requests/*` | Each module’s form request encapsulates validation + authorization (e.g., `SaleRequest`, `ProductRequest`). |
| Views | `resources/views` | Blade templates with Tailwind + Alpine components. POS UI lives under `resources/views/sales`. |
| Front-end assets | `resources/js/app.js`, `resources/css/app.css` | Alpine bootstrapping + Chart.js registration + Notyf configuration; compiled via Vite. |
| Services | `app/Services/SaleService.php` | Encapsulates domain logic (e.g., sale creation with stock validation). |
| Events / Listeners | `app/Events/SaleRecorded.php`, `app/Listeners/UpdateStockAndLoyalty.php` | Trigger background tasks (stock decrement, loyalty points, invoice email). |
| Jobs / Mail | `app/Jobs/SendSaleInvoiceJob.php`, `app/Mail/SaleInvoiceMail.php` | Queue-based invoice generation and delivery with DOMPDF attachment. |
| Models | `app/Models/*` | Plain Eloquent models with relationships + attribute casting/appenders. |
| Config | `config/*.php` | Database, queue, mail, backup, etc. Use `.env` to override per environment. |
| Database | `database/migrations`, `database/seeders`, `database/database.sqlite` | Schema + sample data. Factories/seeders support tests and demo content. |

High-level request flow:
1. Request hits `routes/web.php` and is authenticated (Laravel Breeze guards).
2. Controller resolves dependencies (services, models) using Laravel’s container.
3. Services handle transactional logic; events are dispatched for async work.
4. Views return Blade templates enriched with Alpine/JS data (JSON-encoded via controllers).
5. Queue workers process jobs (invoice emails, backup notifications).

---

## 3. Feature Walkthroughs

### 3.1 Authentication & Authorization
- Provided by Laravel Breeze; routes registered in `routes/auth.php`.
- Roles stored in `roles` table with pivot to users (see `RoleSeeder.php`). Authorization gates/policies follow Laravel defaults; ensure roles are attached via seeder.
- Sanctum-ready APIs: install tokens via `User` model traits if API is needed.

### 3.2 Dashboard
- Route: `GET /dashboard` via `DashboardController::__invoke`.
- Aggregates KPI metrics (sales totals, inventory alerts) and feeds Chart.js via JSON embedded in `resources/views/dashboard.blade.php`.

### 3.3 Products & Inventory
- `ProductController` handles CRUD, Excel import/export (using `ProductsImport` / `ProductsExport`).
- Product images stored on the `assets` disk (`public/assets/...`). `Product::thumbnail_url` dynamically resolves URLs, falling back to generated SVG placeholders.
- Stock adjustments occur via dedicated resource `StockAdjustmentController` or as side-effects of purchases/sales.

### 3.4 POS / Sales Flow
1. User opens `sales.create` (POS screen) or `sales.create-invoice` (quick invoice). Controller preloads products, categories, and customers.
2. Submission hits `SaleController@store`, validated by `SaleRequest`.
3. `SaleService::create()` (`app/Services/SaleService.php`) runs inside a DB transaction:
   - Locks products (`Product::lockForUpdate()`) to ensure stock accuracy.
   - Calculates line totals, aggregates discounts/taxes, persists `Sale` + `SaleItem` rows.
4. `SaleRecorded` event is dispatched with the hydrated sale.
5. `UpdateStockAndLoyalty` listener (queued) decrements product stock, increments customer loyalty points, and dispatches `SendSaleInvoiceJob` if an email exists.
6. `SendSaleInvoiceJob` loads sale data, renders `sales.invoice-pdf` through DOMPDF, and emails it via `SaleInvoiceMail`, attaching the PDF.

### 3.5 Customers & Loyalty
- Customers reside in `customers` table with `points` column.
- Loyalty accrual occurs inside `UpdateStockAndLoyalty`: `floor(total_amount / 100)`.
- Search endpoints (`customers/search`) provide AJAX lookups for POS/autocomplete.

### 3.6 Purchases, Expenses, Payments, Quotations, Tickets
- Each module follows the same resource pattern (controller + Blade views). Data tables share soft business logic (e.g., vendor selection in `PartyController`, expense categorization).
- Reporting endpoints consolidate filters via `ReportController` (CSV/PDF exports via `SalesReportExport`).

### 3.7 Settings & Backups
- `SettingController` persists key/value pairs in `settings` table (`store.*`, `tax.rate`, `payment.methods`, `locale.default`).
- Store logos stored on `assets` disk with cropping support; removal handled by `deleteLogo`.
- Database backups triggered via `POST /settings/backup`, which calls `Artisan::call('backup:run --only-db')`. Users can download zipped backup artifacts from the same page.

### 3.8 Support Tickets & Notifications
- Support tickets tracked via `SupportTicket` model and `SupportTicketController`.
- Notification to staff can be wired via mail/queue if required (currently synchronous).

---

## 4. Database Connections & Schema

### 4.1 Connection Strategy (`config/database.php`)
- Default driver: `sqlite` with database stored at `database/database.sqlite`.
- MySQL/MariaDB ready via `.env` keys:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=pos_billing
  DB_USERNAME=root
  DB_PASSWORD=secret
  DB_CHARSET=utf8mb4
  DB_COLLATION=utf8mb4_unicode_ci
  ```
- PostgreSQL, SQL Server, and MariaDB configs are also scaffolded.
- Redis caching configured under `database.redis.*` with prefix derived from `APP_NAME`.

### 4.2 Environment Files & Scripts
- Copy `.env` from `.env.example` (if absent, re-run `php artisan config:publish` or duplicate from another environment).
- Helper scripts:
  - `setup_database.bat`: prompts for MySQL root password, writes `.env`, tests connection, runs migrations, and starts `php artisan serve`.
  - `USE_XAMPP_MYSQL.bat` / `switch_to_xampp.bat`: switches to XAMPP’s MySQL port (3307) and blank password.
  - `setup_and_run.bat` / `RUN_SERVER.bat`: wraps composer install, npm build, migrations, and serves the app.
  - `RESET_MYSQL_PASSWORD.bat`, `try_common_passwords.ps1`: utilities for local password recovery (see `SETUP_INSTRUCTIONS.md`).

### 4.3 Migration & Seeding Workflow
```
php artisan migrate --seed    # populates default roles, admin user, sample inventory/sales
php artisan storage:link      # exposes uploads via /storage
```
Key tables (non-exhaustive):
- `users`, `roles`, `role_user` – authentication and RBAC.
- `products`, `categories`, `stock_adjustments` – catalog + stock levels.
- `customers`, `sales`, `sale_items` – POS transactions and loyalty tracking.
- `purchases`, `purchase_items`, `parties` – vendor procurement.
- `expenses`, `expense_categories`, `payments`, `bank_accounts` – finance.
- `settings` – store configuration values.
- `support_tickets` – customer issues.
- `jobs`, `failed_jobs`, `job_batches` – queue infrastructure.

Refer to `database/migrations/*.php` for column-level details; each model’s `$fillable` array mirrors editable fields.

### 4.4 Database Switching Cheat Sheet
- **SQLite (default)**: leave `DB_CONNECTION=sqlite`, ensure `database/database.sqlite` exists (touch file if missing).
- **Local MySQL 8.0**: run `setup_database.bat`, ensure MySQL service is running, update `.env`, rerun migrations.
- **XAMPP MySQL**: stop Windows `MySQL80` service, start MySQL from XAMPP, set `DB_PORT=3307`, empty password, and rerun migrations.
- **Production MySQL/MariaDB**: set `.env` credentials, run `php artisan migrate --force`.

---

## 5. Background Jobs, Queues, and Notifications

- Queue driver default is `database` (`config/queue.php`). Ensure `php artisan queue:work --tries=3` is running (systemd, Supervisor, or Laravel Forge worker).
- Critical jobs:
  - `SendSaleInvoiceJob`: generates invoice PDFs and sends `SaleInvoiceMail`.
  - Future jobs can be added by implementing `ShouldQueue`.
- Failed jobs stored in `failed_jobs` table; monitor via `php artisan queue:failed`.
- Scheduler tasks (crontab/Task Scheduler):
  - `* * * * * php artisan schedule:run` (queues backup cleanup, report generation, etc., as defined in `app/Console/Kernel.php`).
  - Optionally run `php artisan backup:run --only-db` nightly.

---

## 6. Integrations & External Services

- **Mail**: Configure `.env` or follow `MAILER_SETUP.md` for Gmail SMTP (App Password). After changes run `php artisan config:clear` and `php artisan queue:restart`.
- **Exports**:
  - Excel via `Maatwebsite\Excel` (see `app/Exports/*`).
  - PDF invoices via `barryvdh/laravel-dompdf`.
- **Backups**: `spatie/laravel-backup` configured in `config/backup.php`. Update disk credentials (S3, FTP, etc.) and whitelist paths if needed.
- **Notifications/UI**: `resources/js/app.js` registers SweetAlert2 and Notyf globally; use in Blade with `window.notyf.success(...)`.

---

## 7. Deployment & Operations

### 7.1 Local Development Checklist
1. `composer install`
2. `npm install && npm run dev` (or `npm run build`)
3. `cp .env.example .env && php artisan key:generate`
4. Configure DB (SQLite or MySQL) and mailer.
5. `php artisan migrate --seed`
6. `php artisan storage:link`
7. `php artisan serve`
8. `php artisan queue:work` (separate terminal)

Default admin: `admin@example.com / password`.

### 7.2 Production Deployment
- Sync code (git pull/SFTP).
- Run `composer install --optimize-autoloader` and `npm ci && npm run build`.
- Copy `.env`, set `APP_ENV=production`, `APP_DEBUG=false`.
- `php artisan key:generate` (initial only), `php artisan migrate --force`, `php artisan storage:link`.
- Configure Supervisor/systemd for:
  - `php artisan queue:work --sleep=3 --tries=3`
  - `php artisan schedule:run` via cron every minute.
- Set correct permissions on `storage` and `bootstrap/cache`.

### 7.3 Monitoring & Logs
- Application logs: `storage/logs/laravel.log`.
- Queue failures: `php artisan queue:failed`.
- Backups: stored under `storage/app/laravel-backup`, visible in Settings UI.
- Front-end assets: `public/build/` (Vite manifest); run `npm run build` on deploy.

---

## 8. Testing & Quality

- Automated tests:
  - Feature tests under `tests/Feature/*` (Pest + PHPUnit).
  - Run `php artisan test` or `vendor/bin/pest`.
- Factories for key models (`database/factories/*`) ease seeding and tests.
- CI-ready: add GitHub Actions or similar to run `composer test` + `npm run build`.

---

## 9. Operational Playbook

| Task | Command / Location | Notes |
|------|--------------------|-------|
| Run queue worker | `php artisan queue:work --tries=3` | Needed for invoice emails & stock/listener jobs. |
| Replay failed jobs | `php artisan queue:retry all` | Investigate logs before replaying. |
| Trigger manual DB backup | Settings → “Run Backup” or `php artisan backup:run --only-db` | Requires configured disk & storage permissions. |
| Import products | UI action posts to `ProductController@import` (Excel). Template under Settings page. |
| Export sales report | UI call to `SalesReportExport`, downloads XLSX filtered by user input. |
| Change locale | `GET /locale/{locale}` toggles `session('app_locale')` (`en` or `hi`). |
| Update store branding | Settings page uploads/crops logos; files saved under `public/assets/branding`. |

---

## 10. Handover Checklist for New Developer
1. **Environment**: Confirm `.env` has correct DB + mail credentials. Test DB connectivity (`php artisan migrate:status`).
2. **Dependencies**: Run `composer install`, `npm install`, `php artisan key:generate`.
3. **Storage**: Ensure `storage`, `bootstrap/cache`, and `public/assets` are writable; run `php artisan storage:link`.
4. **Database**: Run migrations + seeders. If using MySQL, optionally execute `setup_database.bat`.
5. **Queue/Scheduler**: Start workers and cron jobs; verify `jobs` table entries are processing.
6. **Backups**: Confirm `config/backup.php` disk credentials; run a manual backup to validate.
7. **Mail**: Update `.env` per `MAILER_SETUP.md`, send test invoice/password reset.
8. **Testing**: Execute `php artisan test` before deployments.
9. **Documentation**: Keep this guide, `README.md`, and `MAILER_SETUP.md` up to date with any infrastructure changes.

With this guide, a new maintainer should be able to install, operate, and extend the Billing POS application confidently, including understanding how the database connections are configured and how data flows through the system.


