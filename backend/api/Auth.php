<?php
/**
 * ---------------------------------------------------------------------------
 *  api/Auth.php — authentication + role checks for the JSON API
 * ---------------------------------------------------------------------------
 *  The API reuses the same PHP session as the website: call
 *  POST /api/v1/auth/login once (or be signed in through the browser) and
 *  the session cookie authorises every subsequent request.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';   // starts the session, current_username()

/** Role slugs, mirrored from frontend bootstrap so the API stays standalone. */
function api_role_of(string $username): string
{
    try {
        $st = db()->prepare('SELECT userType FROM users WHERE userName = ? LIMIT 1');
        $st->execute([$username]);
        $t = strtolower(trim((string) $st->fetchColumn()));
    } catch (Throwable $e) {
        error_log('api_role_of: ' . $e->getMessage());
        $t = '';
    }

    return match ($t) {
        'admin', 'staff', ''                  => 'admin',
        'official', 'barangay_official'        => 'official',
        'sk', 'sk_chairman', 'sk_chairperson' => 'sk_chairman',
        'treasurer', 'barangay_treasurer'     => 'treasurer',
        'resident'                            => 'resident',
        default                               => 'resident',
    };
}

/** @return array{username:string,role:string,name:string,id:?int}|null */
function api_user(): ?array
{
    $u = current_username();
    if (!$u) {
        return null;
    }
    $name = $u;
    try {
        $st = db()->prepare('SELECT fullName FROM users WHERE userName = ? LIMIT 1');
        $st->execute([$u]);
        $name = (string) ($st->fetchColumn() ?: $u);
    } catch (Throwable $e) {
        error_log('api_user: ' . $e->getMessage());
    }
    return ['username' => $u, 'role' => api_role_of($u), 'name' => $name, 'id' => current_user_id()];
}

/** 401 unless somebody is signed in. Returns the user array. */
function require_api_auth(): array
{
    $user = api_user();
    if ($user === null) {
        api_fail('Authentication required.', 401, 'unauthenticated');
    }
    return $user;
}

/**
 * 403 unless the signed-in user's role is in $roles. 'admin' always passes.
 * An empty list means "admin only".
 */
function require_api_role(array $roles): array
{
    $user = require_api_auth();
    if ($user['role'] !== 'admin' && !in_array($user['role'], $roles, true)) {
        api_fail('Your role does not have access to this resource.', 403, 'forbidden');
    }
    return $user;
}
