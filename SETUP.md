# Barangay Management System — Setup & Refactor Notes

## What was wrong

| Problem | Fix |
|---|---|
| `connection.php` pointed at port **3306** with password `Allen_122` | MariaDB here runs on **3307** with an **empty** `root` password. Credentials moved to `backend/config/config.php`. |
| The `barangay_management_system` database existed but had **0 tables**, and there was no `.sql` file anywhere | Schema **reverse-engineered from the PHP source** into `backend/database/schema.sql` (+ `seed.sql`). |
| A second database, `file_management_system`, was also required and also missing | Recreated in the same `schema.sql`. |
| DB credentials + Gmail SMTP password hard-coded in ~12 files | All read from `backend/config/config.php` now. |
| Passwords stored and compared in **plain text**; login built by string concatenation (**SQL injection**) | `login_test.php`, `users_insert.php`, `user_update.php`, `update_password.php` rewritten with **PDO prepared statements** + `password_hash()` / `password_verify()`. Old plain-text passwords are upgraded automatically on next login. |

## First-time setup

1. Start Apache + MySQL in XAMPP.
2. Build the databases:
   ```
   php backend/database/install.php
   ```
3. Open `http://localhost/BarangayManagementSystem-main/Login.php`
4. Log in with:
   - **Username:** `admin@barangay.gov.ph`
   - **Password:** `Admin@123`  *(change this immediately)*

If MySQL is on a different port/password, edit **`backend/config/config.php`** only.

## New folder layout (in progress)

```
backend/
  config/     config.php (local, git-ignored), config.sample.php, database.php
  helpers/    auth.php (require_login, logout), functions.php (e(), redirect(), save_uploaded_image())
  database/   schema.sql, seed.sql, install.php
  actions/    (Phase 2) the *_insert / *_update / *_delete handlers
frontend/
  pages/      (Phase 2) the page views
  partials/   (Phase 2) shared header / sidebar
  css/  js/
upload/       (Phase 2) all uploaded files consolidated here
```

`connection.php` is kept as a thin compatibility shim so every existing page
keeps working during the migration.

## Front-end

The entire front-end was rebuilt (all 25 pages) on a single hand-written
design system, `frontend/assets/css/app.css` (civic style, layered on
Bootstrap 5.3), with shared layout partials in `frontend/partials/`:

| Partial | Used by |
|---|---|
| `bootstrap.php` | every page — session, DB, helpers, `require_admin()` |
| `nav.php` | the admin sidebar menu (edit the menu here) |
| `admin_top.php` / `admin_bottom.php` | the 12 admin pages |
| `public_top.php` / `public_bottom.php` | the 8 public-site pages |
| `auth_top.php` / `auth_bottom.php` | Login, Forgot / Reset password |
| `onboarding_top.php` / `onboarding_bottom.php` | the 3-step profile wizard |

A page now looks like:

```php
require __DIR__ . '/../partials/bootstrap.php';
require_admin();                       // admin pages only
$page_title = 'Residents'; $active_nav = 'residents';
require __DIR__ . '/../partials/admin_top.php';
/* ...page content only... */
require __DIR__ . '/../partials/admin_bottom.php';
```

The 29 old per-page stylesheets are archived in
`frontend/assets/css/_unused_legacy/` (safe to delete).

## Status

- **Database** — rebuilt & seeded, single config, connection fixed. ✅
- **Structure** — `frontend/` `backend/` `upload/` with shared partials. ✅
- **Front-end** — all 25 pages rebuilt on the new design system. ✅
- **Backend hardening (in progress):** auth, login, user CRUD, FAQ, contact,
  information, and the profile-setup handlers now use PDO prepared statements
  and validation. A few remaining handlers (`resident_*`, `blotter_*`,
  `activity_*`, `barangay_official_*`, `forms_*`, `service_pdf`,
  `services_submit`, `update_profile`, `update_basic_details`,
  `update_other_info`, delete endpoints) still use the original mysqli code —
  functional, but the next pass should convert them and add CSRF tokens.
