<?php
/**
 * ---------------------------------------------------------------------------
 *  routes/pages.php — clean-URL <-> page-file map
 * ---------------------------------------------------------------------------
 *  The front controller (root index.php) turns a pretty path such as
 *
 *      /BarangayManagementSystem-main/pages/login
 *
 *  into an include of  frontend/pages/Login.php .  This file is the single
 *  source of truth for that mapping and is used in BOTH directions:
 *
 *    - forward   : page_slug_to_file('login')      -> 'Login'
 *    - reverse   : page_file_to_slug('Login.php')  -> 'login'   (used by page_url())
 *
 *  Keys are the slug shown in the address bar; values are the exact file
 *  base name in frontend/pages/ (without the .php extension — some of the
 *  legacy views have spaces or an "&" in the name, which is fine here).
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

/** @return array<string,string> slug => "File base name" (no .php) */
function page_route_map(): array
{
    return [
        // --- auth / account ------------------------------------------------
        'login'               => 'Login',
        'register'            => 'Register',
        'forgot-password'     => 'ForgotPassword',
        'reset-password'      => 'ResetPassword',
        'profile'             => 'UserProfile',

        // --- dashboards --------------------------------------------------
        'dashboard'           => 'AdminDashboard',
        'resident-dashboard'  => 'ResidentDashboard',

        // --- admin: records / services / content ------------------------
        'residents'           => 'Resident',
        'officials'           => 'BarangayOfficial',
        'blotter'             => 'Blotter',
        'certificates'        => 'Certificate',
        'document-requests'   => 'DocumentRequest',
        'forms'               => 'Forms',
        'information'         => 'Information',
        'faq-admin'           => 'BarangayFAQ',
        'users'               => 'Users',

        // --- work: tasks / activity / messages -------------------------
        'tasks'               => 'Tasks',
        'activity'            => 'Activity',
        'messages'            => 'BarangayContact&Message',

        // --- resident portal ------------------------------------------
        'my-requests'         => 'MyRequests',
        'my-messages'         => 'MyMessages',
        'request-document'    => 'RequestDocument',

        // --- profile-setup wizard -----------------------------------
        'personal-data'       => 'Personal Data',
        'other-info'          => 'Other Info',
        'proof-of-identity'   => 'Proof of Identity',

        // --- public site --------------------------------------------
        'general-information' => 'GeneralInformation',
        'history'             => 'History',
        'maps'                => 'Maps',
        'photos'              => 'Photos',
        'faq'                 => 'FAQ',
        'contact'             => 'Contact',
    ];
}

/**
 * Resolve a URL slug to a page file base name.
 *
 * @return string|null  "Login"  (or null when the slug is unknown)
 */
function page_slug_to_file(string $slug): ?string
{
    $slug = strtolower(trim($slug, '/'));
    return page_route_map()[$slug] ?? null;
}

/**
 * Reverse lookup used by page_url(): accepts either a slug ("login") or a
 * legacy file name ("Login.php" / "Login") and returns the clean slug.
 * Falls back to a slugified version of the name so unmapped pages still work.
 */
function page_file_to_slug(string $nameOrSlug): string
{
    $name = preg_replace('/\.php$/i', '', trim($nameOrSlug, '/'));

    // already a known slug?
    if (isset(page_route_map()[strtolower($name)])) {
        return strtolower($name);
    }

    // known file base -> slug
    $flip = array_change_key_case(array_flip(page_route_map()), CASE_LOWER);
    if (isset($flip[strtolower($name)])) {
        return $flip[strtolower($name)];
    }

    // unknown: derive a reasonable slug ("BarangayFAQ" -> "barangay-faq")
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(
        preg_replace('/(?<=[a-z])(?=[A-Z])/', '-', $name)
    ));
    return trim((string) $slug, '-');
}
