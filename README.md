# Enterprise E-Commerce & Operations Platform (Laravel)

A full-stack Laravel application for a multi-role e-commerce marketplace with an enterprise-oriented order lifecycle. Customers place orders via a Blade-based storefront, vendors manage fulfillment actions, and administrators oversee users, vendors, products, and operations. The platform uses PostgreSQL for persistence, Redis-backed queues for background processing, Laravel Reverb for real-time updates, and Mailtrap for local email testing.

---

## Key Features

- Multi-role access model: Admin, Vendor, Customer (role & permission driven)
- Customer shopping flow: browse products, cart, checkout, order history
- Vendor operations: manage products, manage stock, and manually progress order statuses
- Enterprise order lifecycle with explicit vendor actions (Accept, Ship, Complete)
- Event → Listener → Job architecture for asynchronous workflows
- Redis queues for domain workloads:
	- payment_queue
	- inventory_queue
	- shipping_queue
- Email notifications designed for local testing via Mailtrap
- Real-time broadcasting via Laravel Reverb (WebSocket)
- File storage for uploaded images and operational documents (public disk + symlinks)
- Role & permission system
- Secure authentication (OTP login and Google OAuth present)

---

## Architecture Overview

This project follows a conventional Laravel MVC structure with additional operational patterns:

- HTTP layer: controllers, form requests, middleware, and role-based route groups
- Domain logic: services for focused workflows (e.g., OTP generation/verification)
- Asynchronous processing: events trigger listeners, which dispatch jobs onto Redis queues
- Notifications: emails are sent via Laravel’s mail system (Mailtrap recommended for local)
- Real-time updates: broadcasting through Laravel Reverb where applicable
- Storage: uploaded assets served from the public storage disk via symlink

---

## Technology Stack

- Backend: Laravel (PHP)
- Database: PostgreSQL
- Frontend (Customer & Vendor): Blade templates + Vite
- Admin UI: React (planned/partial) located in [admin-ui/](admin-ui/)
- Cache/Queue: Redis
- Broadcasting: Laravel Reverb (WebSocket)
- Authorization: role & permission system
- Testing: PHPUnit

---

## User Roles & Responsibilities

**Customer**

- Browse products and product details
- Manage cart and checkout
- View order history and order details
- Use OTP login (where enabled) for passwordless authentication

**Vendor**

- Manage product catalog and inventory
- View incoming orders
- Manually progress order statuses:
	- Accept
	- Ship
	- Complete
- Operational responsibility for fulfillment actions (no automatic completion)

**Admin**

- Monitor platform operations
- Manage users and vendors
- Review/approve products (where implemented)
- View vendor business details and uploaded branding assets (where implemented)

---

## Order Lifecycle Flow

The order flow is intentionally vendor-controlled:

1. Customer places an order (order is created and visible to vendor)
2. Vendor reviews and manually accepts the order
3. Vendor ships the order when ready
4. Vendor completes the order after fulfillment is done

Important behavior:

- Vendor actions are manual (Accept, Ship, Complete)
- No automatic order completion
- Jobs and emails trigger only after vendor actions (through events/listeners/jobs)

---

## Background Processing & Queues

Redis is required for background processing.

Queues used by this project:

- payment_queue: payment-related jobs
- inventory_queue: stock/inventory updates
- shipping_queue: shipment preparation and shipping updates

Workers should be running for all queues during local development to ensure background work and emails are processed.

---

## Email & Notification System

- Local email testing is intended to use Mailtrap via SMTP configuration.
- Many emails are dispatched through queued jobs (background processing).
- Jobs and emails are designed to trigger only after vendor actions.

If emails do not appear in Mailtrap:

- Verify Mailtrap SMTP settings in `.env`
- Ensure queue workers are running for `payment_queue`, `inventory_queue`, and `shipping_queue`
- Check application logs in [storage/logs/laravel.log](storage/logs/laravel.log)

---

## Installation & Setup (Local)

Prerequisites:

- PHP (with required Laravel extensions)
- Composer
- Node.js + npm
- PostgreSQL
- Redis

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

3) Configure PostgreSQL credentials in `.env` and create the database.

4) Run migrations

```bash
php artisan migrate
```

5) Storage symlink (for uploaded files)

```bash
php artisan storage:link
```

---

## Running the Project

No Docker is used.

### Option A: One-command local runner (recommended)

Use [run-project.sh](run-project.sh), which starts:

- Laravel HTTP server
- Vite dev server
- Laravel Reverb server
- Redis queue workers (`payment_queue`, `inventory_queue`, `shipping_queue`)

```bash
chmod +x run-project.sh
./run-project.sh
```

### Option B: Run services manually

In separate terminals:

1) Backend server

```bash
php artisan serve
```

2) Frontend dev server

```bash
npm run dev
```

3) Broadcasting (Reverb)

```bash
php artisan reverb:start
```

4) Queue workers (Redis)

```bash
php artisan queue:work redis --queue=payment_queue
php artisan queue:work redis --queue=inventory_queue
php artisan queue:work redis --queue=shipping_queue
```

---

## Environment Configuration

Key settings to verify in `.env`:

**Application**

- APP_NAME, APP_ENV, APP_DEBUG, APP_URL

**Database (PostgreSQL)**

- DB_CONNECTION=pgsql
- DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

**Redis / Queues**

- QUEUE_CONNECTION=redis
- REDIS_HOST, REDIS_PORT
- Optional queue names:
	- QUEUE_PAYMENT=payment_queue
	- QUEUE_INVENTORY=inventory_queue
	- QUEUE_SHIPPING=shipping_queue

**Mail (Mailtrap)**

- MAIL_MAILER=smtp
- MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD
- MAIL_FROM_ADDRESS, MAIL_FROM_NAME

**Broadcasting (Reverb)**

- Ensure Reverb-related variables match your local setup (host/port as needed)

---

## Folder Structure (High Level)

- [app/](app/) — application code (controllers, jobs, events, listeners, models, services)
- [routes/](routes/) — web and API routes (role-based groups and auth flows)
- [resources/](resources/) — Blade views, frontend assets, and email templates
- [database/](database/) — migrations, seeders, factories
- [config/](config/) — framework and application configuration
- [public/](public/) — public entrypoint and built assets
- [storage/](storage/) — logs, cached files, and uploaded content (via public disk)
- [tests/](tests/) — PHPUnit tests
- [admin-ui/](admin-ui/) — React-based admin UI (planned/partial)

---

## Security & Best Practices

- Role-based access control enforced via middleware and route grouping
- Input validation through request validation in controllers/form requests
- Background processing used for operational workloads
- Signed routes used for time-limited access where applicable
- OTP flows are rate-limited to reduce abuse
- Store secrets in `.env` and never commit them

---

## Screenshots

Placeholders (add images as the UI stabilizes):

- Customer storefront
- Vendor dashboard (orders + products)
- Admin views (vendor/product management)
- Order lifecycle status progression

---

## Future Enhancements

- Complete and integrate the React admin UI under [admin-ui/](admin-ui/)
- Expand observability (structured logs, metrics, dashboards)
- Add more automated tests for critical workflows (orders, queues, notifications)
- Harden email/broadcast delivery configuration for production

---

## About Laravel

This repository uses the Laravel framework.

- Elegant MVC foundation (routing, controllers, middleware)
- Eloquent ORM + migrations
- Queues, events, notifications, and broadcasting

## Learning Laravel

- Docs: https://laravel.com/docs
- Laracasts: https://laracasts.com

## Laravel Sponsors

Laravel sponsors: https://laravel.com/sponsors

## Contributing (Laravel)

Laravel contribution guide: https://laravel.com/docs/contributions

## Code of Conduct (Laravel)

Laravel code of conduct: https://laravel.com/docs/contributions#code-of-conduct

## Security Vulnerabilities (Laravel)

Laravel security policy: https://laravel.com/docs/security

---

## License / Usage Note

No license is currently specified. Treat this repository as proprietary unless a license file is added.

