# FamTrack-QR

**FamTrack-QR** — *Household Profiling & Relief Distribution Management System* — is a centralized digital platform built for the **Office of the Municipal Disaster Risk Reduction and Management Office (MDRRMO) of Naic, Cavite, Philippines**. It lets the local government manage household profiles across all 30 barangays, generate QR-coded resident IDs, and efficiently track *ayuda* (relief aid) distribution during disaster response operations — replacing manual, paper-based logging with a fast, auditable, scan-based workflow.

Originally developed as an OJT (on-the-job training) capstone project.

## Screenshots

**Landing page**

<img width="1902" height="1036" alt="Screenshot 2026-09-07 131034" src="https://github.com/user-attachments/assets/7708588f-f9f2-4da6-8dfb-f88b3e32a17b" />


## What it does

- **Household & family registration** — Encoders record household profiles (location, housing, utilities, contact info) and family member details (demographics, PWD/senior/solo-parent/4Ps status, etc.), which admins review and approve.
- **QR identification cards** — Once a household is approved, the system generates a unique, branded QR code (SVG) for the household, and can also generate individual QR codes for family heads. Each code embeds a structured serial (e.g. `NIC-TB-HH-2026-00001`) tied to the barangay, year, and record.
- **Distribution events** — Admins create relief distribution events (event name, relief items, target barangays, schedule, GPS location, scan mode) and can start, end, or cancel them.
- **QR-based scanning** — Field staff use a camera-based scanner to check households or family heads in/out of a distribution event, with validation against the expected QR type, duplicate-scan prevention, and optional recipient release photos.
- **Reporting & exports** — Distribution logs can be exported as CSV, PDF, or Excel (including a customizable export), and dashboards summarize activity per event and barangay.
- **Role-based access** — Five roles with distinct dashboards and permissions: **Super Admin**, **Admin**, **Encoder**, **Staff**, and **Auditor**.
- **Audit trail** — Key actions (account changes, approvals, event lifecycle, distributions) are logged for accountability and reviewed by admins/auditors.
- **Account management** — Super admins invite users by email (invite-link based account setup), manage roles, and can deactivate, archive, or restore accounts.

## Roles

| Role | Typical responsibilities |
|---|---|
| **Super Admin** | Manage user accounts and roles, view system-wide audit trails |
| **Admin** | Approve households, manage distribution events, generate QR codes, view reports/exports, trail logs |
| **Encoder** | Register and edit household & family member records |
| **Staff** | Scan QR codes during active distribution events, view scan history |
| **Auditor** | Read-only oversight of households, distribution logs, and audit trail |

## Tech stack

- **Backend:** [Laravel 12](https://laravel.com) (PHP ^8.2)
- **Auth scaffolding:** Laravel Breeze
- **Frontend:** Blade templates, [Alpine.js](https://alpinejs.dev), [Bootstrap 5](https://getbootstrap.com), [Tailwind CSS](https://tailwindcss.com), built with [Vite](https://vitejs.dev)
- **QR generation:** [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- **Exports:** [maatwebsite/excel](https://laravel-excel.com) (XLSX/CSV), [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) (PDF)
- **Database:** MySQL

## Getting started

### Requirements

- PHP ^8.2 with the extensions Laravel requires
- Composer
- Node.js & npm
- MySQL

### Installation

```bash
git clone https://github.com/Ducktpe/Project_FamTrack-QR.git
cd Project_FamTrack-QR

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Update the `.env` file with your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and mail settings (used for account-invite emails), then run:

```bash
php artisan migrate --seed
php artisan storage:link
```

> `storage:link` is required — generated QR codes are written to `storage/app/public` and served via the public disk.

### Running the app

```bash
composer run dev
```

This runs the Laravel server, queue listener, log watcher (Pail), and Vite dev server together. Alternatively, run them individually:

```bash
php artisan serve
npm run dev
```

Visit `http://localhost:8000` (or your `APP_URL`). Create your first Super Admin account via a database seeder or `php artisan tinker`, since account creation for other roles happens through the Super Admin's invite flow.

### Building for production

```bash
npm run build
```

## Project structure highlights

- `app/Models` — `Household`, `FamilyMember`, `DistributionEvent`, `DistributionLog`, `QrCode`, `AuditLog`, `User`, etc.
- `app/Http/Controllers/{Admin,Auditor,Encoder,Staff,SuperAdmin}` — role-scoped controllers
- `app/Services/QrCodeService.php` — builds branded QR SVGs for households and family heads
- `app/Http/Middleware/RoleMiddleware.php` — enforces the role hierarchy (`super_admin` → `admin` → `encoder`/`staff`/`auditor`)
- `database/migrations` — full schema history for households, family members, distribution events/logs, QR codes, audit logs, and PH location data (province/municipality/barangay)
- `resources/views` — Blade views grouped by role dashboard

## Contributing

This is an active school/community project developed collaboratively. Please open a pull request against `main` and describe the change clearly. Check existing branches before starting new work to avoid duplicate effort.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
