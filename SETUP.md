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

## Status

- **Phase 1 — DONE:** database rebuilt & seeded, single config, connection fixed,
  authentication hardened, all pages load without fatal errors.
- **Phase 2 — TODO:** physically move files into `frontend/` `backend/` `upload/`
  and update includes / links.
- **Phase 3 — TODO:** convert the remaining handlers and page queries to PDO
  prepared statements, add CSRF tokens, escape all output, add missing
  `require_login()` guards, de-duplicate the header/sidebar markup.
