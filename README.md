# ClinicHub — Laravel Migration Guide

This folder contains the full Laravel 11 conversion of the original flat-PHP ClinicHub project.

---

## What's Included

| Original PHP File | Laravel Equivalent |
|---|---|
| `login.php` | `AuthController@login` + `views/auth/login.blade.php` |
| `student_dashboard.php` | `StudentController@dashboard` + `views/student/dashboard.blade.php` |
| `staff_dashboard.php` | `StaffController@dashboard` + `views/staff/dashboard.blade.php` |
| `superadmin_dashboard.php` | `SuperAdminController@dashboard` + `views/superadmin/dashboard.blade.php` |
| `appointment.php` | `AppointmentController` + `views/staff/appointments.blade.php` |
| `medicalrecord.php` | `MedicalRecordController` + `views/staff/medical_records.blade.php` |
| `inventory.php` | `InventoryController` + `views/staff/inventory.blade.php` |
| `manage_users.php` | `UserController` + `views/superadmin/manage_users.blade.php` |
| `send_message.php` / `inbox.php` | `MessageController` + `views/staff/messages.blade.php` |
| `settings.php` | `SettingsController` + `views/superadmin/settings.blade.php` |
| `audit_log.php` | `AuditLog::record()` model helper |
| `cron_appointment_reminders.php` | `artisan clinichub:reminders` command |
| `database.php` | `config/database.php` + `.env` |
| `access_control.php` / `allowRoles()` | `RoleMiddleware` |
| `settings_loader.php` | `SystemSetting::current()` model |
| Auto-logout session checks | `AutoLogoutMiddleware` |

---

## Step-by-Step: How to Set Up

### Step 1 — Install Laravel (fresh machine)

```bash
# Requires PHP 8.2+, Composer, Node.js
composer create-project laravel/laravel clinichub-laravel
cd clinichub-laravel
```

### Step 2 — Drag and Drop These Files

Replace or create each file in your fresh Laravel project:

**`app/Http/Controllers/`** — drop ALL files from this folder:
- `AuthController.php`
- `StudentController.php`
- `StaffController.php`
- `SuperAdminController.php`
- `AppointmentController.php`
- `MedicalRecordController.php`
- `InventoryController.php`
- `UserController.php`
- `MessageController.php`
- `AnnouncementController.php`
- `ReportController.php`
- `SettingsController.php`
- `BackupController.php`

**`app/Http/Middleware/`** — drop:
- `RoleMiddleware.php`
- `AutoLogoutMiddleware.php`

**`app/Models/`** — drop ALL:
- `User.php`, `Appointment.php`, `MedicalRecord.php`
- `Inventory.php`, `Patient.php`, `Log.php`
- `AuditLog.php`, `Message.php`, `Announcement.php`, `SystemSetting.php`

**`app/Mail/`** — drop:
- `WelcomeMail.php`, `AppointmentConfirmedMail.php`, `AppointmentReminderMail.php`

**`app/Console/Commands/`** — drop:
- `SendAppointmentReminders.php`

**`routes/`** — replace:
- `web.php`
- `console.php`

**`database/migrations/`** — drop all 3 migration files

**`resources/views/`** — drop entire folder structure:
- `layouts/app.blade.php`
- `partials/sidebar.blade.php`
- `partials/topbar.blade.php`
- `auth/login.blade.php`
- `student/` — all blade files
- `staff/` — all blade files
- `superadmin/` — all blade files

**`bootstrap/app.php`** — replace with the provided file

**`config/auth.php`** — replace

**`public/js/sidebar.js`** — copy

**`Dockerfile`** + **`docker-compose.yml`** — copy to project root

### Step 3 — Copy CSS & Images from Original Project

```
FROM (original project)          →  TO (Laravel public/)
──────────────────────────────────────────────────────────
login.css                        →  public/css/login.css
style.css                        →  public/css/style.css
staffdash.css                    →  public/css/staffdash.css
stadash.css                      →  public/css/stadash.css
student_dashboard.css            →  public/css/student_dashboard.css
superadmin_dashboard.css         →  public/css/superadmin_dashboard.css
appointment.css                  →  public/css/appointment.css
inventory.css                    →  public/css/inventory.css
medicalrecord.css                →  public/css/medicalrecord.css
messages.css                     →  public/css/messages.css
record.css                       →  public/css/record.css
schedule.css                     →  public/css/schedule.css
settings.css                     →  public/css/settings.css
logs.css                         →  public/css/logs.css
manage_users.css                 →  public/css/manage_users.css
backup.css                       →  public/css/backup.css
health_safety.css                →  public/css/health_safety.css
superadmin_logs.css              →  public/css/superadmin_logs.css

asset/images/                    →  public/images/
asset/TIPS/                      →  public/images/TIPS/
```

### Step 4 — Configure Environment

```bash
cp .env.example .env
```

Edit `.env`:
```
DB_HOST=db          # or 127.0.0.1 for local
DB_DATABASE=clinic_db
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_16_char_app_password
```

```bash
php artisan key:generate
```

### Step 5 — Run Migrations

```bash
# Option A: Use the new Laravel migrations (fresh database)
php artisan migrate

# Option B: Import the original SQL dump (keeps existing data)
mysql -u root -p clinic_db < clinic_db.sql
```

### Step 6 — Deploy with Docker

```bash
docker compose up --build -d

# Check containers
docker compose ps

# Run migrations inside container
docker exec clinichub_web php artisan migrate

# Or seed with original SQL
docker exec -i clinichub_db mysql -u root -psecret clinic_db < clinic_db.sql
```

Open browser: **http://localhost**

### Step 7 — Test Login

| Role | ID Number | Default from seed data |
|---|---|---|
| Student | 142117 | Use the password you registered with |
| Staff | 333333 | — |
| STA | 123456 | — |
| Superadmin | admin01 | Generate bcrypt hash in `.env` setup |

---

## Key Differences from Original PHP

| Old PHP | Laravel |
|---|---|
| `$_SESSION['role']` everywhere | `Auth::user()->role` |
| `header("Location: ...")` | `redirect()->route(...)` |
| Raw `mysqli_query(...)` | Eloquent ORM |
| `include "access_control.php"` | `middleware('role:staff,sta')` |
| `include "settings_loader.php"` | `SystemSetting::current()` |
| Hardcoded DB credentials | `.env` + `config/database.php` |
| Inline session timeout | `AutoLogoutMiddleware` |
| Flat CSS includes | `asset('css/filename.css')` |

---

## Running the Cron Reminder (manual test)

```bash
# Inside container
docker exec clinichub_web php artisan clinichub:reminders

# Locally
php artisan clinichub:reminders
```

---

## URL Map

| Page | Old URL | New URL |
|---|---|---|
| Login | `/login.php` | `/login` |
| Student Dashboard | `/student_dashboard` | `/student/dashboard` |
| Staff Dashboard | `/staff_dashboard` | `/staff/dashboard` |
| STA Dashboard | `/sta_dashboard` | `/sta/dashboard` |
| Superadmin Dashboard | `/superadmin_dashboard` | `/admin/dashboard` |
| Appointments (staff) | `/appointment` | `/staff/appointments` |
| Medical Records | `/medicalrecord` | `/staff/medical-records` |
| Inventory | `/inventory` | `/staff/inventory` |
| Manage Users | `/manage_users.php` | `/admin/users` |
| Settings | `/settings.php` | `/admin/settings` |
| Backup | `/backup.php` | `/admin/backup` |
