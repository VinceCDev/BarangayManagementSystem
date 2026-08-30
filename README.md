# Barangay Management System

A web application for **Barangay Paule 1, Rizal, Laguna** — a public information
site plus a role-aware back office for residents' records, blotter entries,
document/certificate requests, tasks, activities and system users.

Built with **PHP 8.2** (procedural + a light MVC-ish structure), **MariaDB**,
**Bootstrap 5.3** and one hand-written civic design system. No build step, no
Composer — it runs straight out of `htdocs`.

---

## Table of contents

- [Requirements](#requirements)
- [First-time setup](#first-time-setup)
- [URLs & routing](#urls--routing)
- [REST API](#rest-api)
- [Project structure](#project-structure)
- [Roles & access](#roles--access)
- [How a page is built](#how-a-page-is-built)
- [Security notes](#security-notes)
- [Testing](#testing)
- [Known follow-ups](#known-follow-ups)

---

## Requirements

| Need | Version / notes |
|---|---|
| PHP | 8.1+ (developed on 8.2). Extensions: `pdo_mysql`, `mysqli`, `fileinfo`, `openssl` |
| MariaDB / MySQL | 10.4+ / 8.0+ |
| Apache | with **`mod_rewrite`** enabled and `AllowOverride All` for `htdocs` (both are on by default in XAMPP) |
| Browser | any current evergreen browser |

The project folder must sit directly under the web root, e.g.
`C:\xampp\htdocs\BarangayManagementSystem-main`.

---

## First-time setup

1. **Start Apache + MySQL** in the XAMPP control panel.

2. **Create the config file** (it is git-ignored because it holds credentials):

   ```
   cp backend/config/config.sample.php backend/config/config.php
   ```

   Then open `backend/config/config.php` and set your MySQL **port** and
   **password**, and `app.base_url` if the folder name differs.
   *This is the only file you edit for local settings.*

3. **Build the databases** (schema + seed data for both
   `barangay_management_system` and `file_management_system`):

   ```
   php backend/database/install.php
   ```

4. **Open the site:** <http://localhost/BarangayManagementSystem-main/>

5. **Sign in** at `…/pages/login`:

   | | |
   |---|---|
   | Username | `admin@barangay.gov.ph` |
   | Password | `Admin@123` — **change it immediately** |

### Running without Apache (PHP dev server)

```
php -S localhost:8000 -t . router.php      # from the project root
```

Then browse <http://localhost:8000/pages/login>. `router.php` reproduces the
`.htaccess` behaviour for the built-in server.

---

## URLs & routing

Every request that is not a real file is sent to the **front controller**
(`index.php`) by `.htaccess` (Apache) or `router.php` (dev server). It maps
clean paths to views and API handlers. **The old file paths still work**, so
nothing bookmarked or hard-coded breaks.

| Clean URL | Serves |
|---|---|
| `/` | the public home page |
| `/pages/{slug}` | a view in `frontend/pages/` |
| `/api/v1/…` | the JSON REST API |
| `/logout` | ends the session, returns to `/pages/login` |
| `/actions/{name}` | alias for `backend/actions/{name}.php` |

### Page slugs

| Slug | View | | Slug | View |
|---|---|---|---|---|
| `login` | Login | | `residents` | Resident |
| `register` | Register | | `officials` | BarangayOfficial |
| `forgot-password` | ForgotPassword | | `blotter` | Blotter |
| `reset-password` | ResetPassword | | `certificates` | Certificate |
| `profile` | UserProfile | | `document-requests` | DocumentRequest |
| `dashboard` | AdminDashboard | | `forms` | Forms |
| `resident-dashboard` | ResidentDashboard | | `information` | Information |
| `tasks` | Tasks | | `faq-admin` | BarangayFAQ |
| `activity` | Activity | | `users` | Users |
| `messages` | BarangayContact&Message | | `my-requests` | MyRequests |
| `request-document` | RequestDocument | | `my-messages` | MyMessages |
| `general-information` | GeneralInformation | | `history` | History |
| `maps` | Maps | | `photos` | Photos |
| `faq` | FAQ | | `contact` | Contact |

The map lives in **`backend/routes/pages.php`** and is used both ways — the
`page_url('Login.php')` helper returns `/BarangayManagementSystem-main/pages/login`.

---

## REST API

Base path: **`/api/v1`**. All responses are JSON with a fixed envelope:

```jsonc
// success
{ "data": <payload>, "meta": { "page": 1, "per_page": 20, "total": 42, "total_pages": 3 } }
// error
{ "error": { "message": "…", "code": "…", "fields": { "field": "why" } } }
```

### Authentication

The API shares the website's PHP session. Sign in once and the session cookie
authorises the rest.

| Method & path | Body | Result |
|---|---|---|
| `POST /api/v1/auth/login` | `{ "username", "password" }` | `200` + the user, sets the session cookie |
| `POST /api/v1/auth/logout` | – | `200`, clears the session |
| `GET  /api/v1/auth/me` | – | `200` + the current user, or `401` |

### Resources

Each resource supports the standard five verbs:

```
GET    /api/v1/{resource}          list   — ?search= &page= &per_page= (max 100)
GET    /api/v1/{resource}/{id}     read
POST   /api/v1/{resource}          create
PUT    /api/v1/{resource}/{id}     replace  (all required fields)
PATCH  /api/v1/{resource}/{id}     update   (any subset of fields)
DELETE /api/v1/{resource}/{id}     delete   — 204
```

| Resource | Table | Read | Write |
|---|---|---|---|
| `residents` | `residents` | admin | admin |
| `officials` | `barangay_officials` | admin | admin |
| `blotter` | `blotterrecords` | admin | admin |
| `certificates` | `certificates` | admin | admin |
| `document-requests` | `document_requests` | admin, treasurer | admin, treasurer |
| `tasks` | `tasks` | admin, official, sk_chairman, treasurer | admin |
| `faqs` | `faq` | admin | admin |
| `activities` | `activity` | admin | admin |
| `contacts` | `contacts` | admin | admin |
| `messages` | `receivemessages` | admin | *delete only* |
| `users` | `users` | admin | admin (`password` is write-only, never returned) |

Column whitelists, required fields and searchable columns are declared in
**`backend/api/resources.php`**; the generic controller
(`backend/api/Resource.php`) runs every query through a prepared statement.

### Examples

```bash
# log in (save the cookie)
curl -c jar.txt -X POST http://localhost/BarangayManagementSystem-main/api/v1/auth/login \
     -H 'Content-Type: application/json' \
     -d '{"username":"admin@barangay.gov.ph","password":"Admin@123"}'

# list residents, page 2
curl -b jar.txt 'http://localhost/BarangayManagementSystem-main/api/v1/residents?page=2&per_page=10'

# create an FAQ
curl -b jar.txt -X POST http://localhost/BarangayManagementSystem-main/api/v1/faqs \
     -H 'Content-Type: application/json' \
     -d '{"question":"How do I request a certificate?","answer":"Sign in and open Request a Document."}'

# partial update
curl -b jar.txt -X PATCH http://localhost/BarangayManagementSystem-main/api/v1/faqs/5 \
     -H 'Content-Type: application/json' -d '{"answer":"Updated answer."}'

# delete
curl -b jar.txt -X DELETE http://localhost/BarangayManagementSystem-main/api/v1/faqs/5
```

| Status | When |
|---|---|
| `200` / `201` / `204` | ok / created / deleted |
| `401` | not signed in |
| `403` | signed in, wrong role |
| `404` | unknown resource or id |
| `405` | verb not allowed on that resource |
| `422` | validation failed (`error.fields` says which) |

---

## Project structure

```
BarangayManagementSystem-main/
├── index.php                 Front controller (routes every request)
├── router.php                Same routing for `php -S`
├── .htaccess                 Apache rewrite -> index.php
│
├── backend/
│   ├── config/               config.php (local, git-ignored) · config.sample.php · database.php
│   ├── routes/               pages.php  (clean-URL <-> view map)
│   ├── api/                  index.php · Http.php · Auth.php · Resource.php · resources.php · controllers/
│   ├── helpers/              auth.php (session) · functions.php (e(), redirect(), save_uploaded_image())
│   ├── database/             schema.sql · seed.sql · install.php
│   ├── actions/              form / AJAX handlers (*_insert, *_update, *_delete, …)
│   ├── connection.php        mysqli compatibility shim ($conn, $fileManagementConn)
│   └── lib/                  PHPMailer, FPDF, FPDI (vendored)
│
├── frontend/
│   ├── pages/                the views (Home, Login, Resident, …)
│   ├── partials/             bootstrap.php + shared layouts (see below)
│   └── assets/               css/app.css (the only stylesheet), js, images
│
└── upload/                   user-uploaded files (git-ignored)
```

### Databases

- **`barangay_management_system`** — all business data (~25 tables).
- **`file_management_system`** — the document-tracking tables (6 tables).

Both are rebuilt by `php backend/database/install.php`. Credentials live only
in `backend/config/config.php`.

---

## Roles & access

`users.userType` holds a role slug. `current_role()` normalises legacy values
(`admin` / `staff` / empty → `admin`).

| Role | Sees |
|---|---|
| `admin` | everything |
| `official`, `sk_chairman` | Dashboard, **Tasks**, Activities, Messages, Profile |
| `treasurer` | the above **+ Document Requests** |
| `resident` | Resident dashboard, My Document Requests, Request a Document, My Messages, Profile |

Enforced in two places:

- **Navigation** — `frontend/partials/nav.php` only emits menu items the role owns.
- **Page guards** — each view calls `require_role([...])` (`[]` = admin-only;
  admin always passes; otherwise `403`). The same rule is applied in the API by
  `require_api_role()`.

---

## How a page is built

Views set a few variables and include a shared layout:

```php
require __DIR__ . '/../partials/bootstrap.php';   // session, DB, helpers, URL builders
require_role([]);                                 // guard: admin only
$page_title   = 'Residents';
$page_heading = 'Barangay Residents';
$active_nav   = 'residents';
require __DIR__ . '/../partials/admin_top.php';
/* …page content only… */
require __DIR__ . '/../partials/admin_bottom.php';
```

| Layout partial | Used by |
|---|---|
| `bootstrap.php` | every page — session, DB, helpers, guards, `page_url()` |
| `nav.php` | the sidebar menu (edit the menu here) |
| `admin_top.php` / `admin_bottom.php` | the back-office pages |
| `public_top.php` / `public_bottom.php` | the public site |
| `auth_top.php` / `auth_bottom.php` | Login, Register, Forgot / Reset password |

The front-end was fully rebuilt on one design system,
`frontend/assets/css/app.css` (civic style over Bootstrap 5.3). Cache-busting
is automatic via `asset()` (`?v=<filemtime>`).

Shared helpers rendered from PHP: `render_pager()` (numbered pagination),
`filter_select()` (auto-submitting filter dropdown), `view_button()` (row
"view" modal).

---

## Security notes

Fixed during the refactor:

- **Credentials** were hard-coded in ~12 files (DB on the wrong port/password,
  a Gmail app password in `contact_insert.php`). All of it now comes from
  `backend/config/config.php`, which is git-ignored.
- **Login** was string-concatenated SQL comparing **plain-text** passwords.
  Rebuilt with PDO prepared statements + `password_hash()` / `password_verify()`;
  old plain-text passwords are upgraded to a hash on the next successful login.
  The session id is regenerated on login (anti-fixation).
- **Output escaping** — `e()` everywhere user data is printed (was stored XSS).
- **Uploads** — `save_uploaded_image()` checks the real MIME type, caps the
  size and stores a random file name.
- **API** — every query is a prepared statement over a column whitelist; role
  checks on every verb; `users.password` is never serialised.

`config.php`, dotfiles and `composer.*` are denied over HTTP by `.htaccess`.

---

## Testing

No framework — checks are run against the app directly:

```
# lint everything
find frontend backend index.php -name '*.php' -not -path '*/lib/*' -print0 | xargs -0 -n1 php -l

# smoke-test routes + API on the dev server
php -S 127.0.0.1:8000 -t . router.php
curl 127.0.0.1:8000/pages/login
curl -c j -X POST 127.0.0.1:8000/api/v1/auth/login -H 'Content-Type: application/json' \
     -d '{"username":"admin@barangay.gov.ph","password":"Admin@123"}'
curl -b j 127.0.0.1:8000/api/v1/residents
```

The last full pass covered functionality, RBAC (guest × 5 roles × every
protected page), the API CRUD matrix, responsive layout (600 / 768 / 1440),
Safari `backdrop-filter`, and page-load timings (all views 40–80 ms, 2–6
queries each, no N+1).

---

## Known follow-ups

- **Legacy `backend/actions/*.php`** — the original mysqli handlers
  (`resident_*`, `blotter_*`, `activity_*`, `barangay_official_*`, `forms_*`,
  `faq1_*`, `user_delete`, `user_update`, `fetch_user_details`, …) still lack a
  `require_login()` / `require_role()` guard and, in a few cases, build queries
  by string concatenation. Prefer the REST API for new work; port these next
  and add CSRF tokens. The refactored handlers (`login_test`,
  `register_resident`, `task_save`, `services_submit`) are already hardened.
- Pin SweetAlert2 to an exact version (currently `@11`).
- Add `<label for>` / `id` pairs on the Login and Register inputs.
- Add far-future `Cache-Control` for `frontend/assets/` (URLs are already
  cache-busted).
