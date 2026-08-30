<?php
/**
 * ===========================================================================
 *  api/index.php — JSON REST API entry point  (prefix: /api/v1)
 * ===========================================================================
 *  Reached through the front controller (root index.php) for any /api/... URL,
 *  and also runnable directly at /backend/api/index.php?route=v1/residents.
 *
 *  Resources (see api/resources.php) expose the standard five verbs:
 *
 *      GET    /api/v1/{resource}            list  (?search= &page= &per_page=)
 *      GET    /api/v1/{resource}/{id}       read
 *      POST   /api/v1/{resource}            create
 *      PUT    /api/v1/{resource}/{id}       replace
 *      PATCH  /api/v1/{resource}/{id}       partial update
 *      DELETE /api/v1/{resource}/{id}       delete
 *
 *  Auth (session cookie — call login first, or be signed in via the site):
 *
 *      POST   /api/v1/auth/login    { username, password }
 *      POST   /api/v1/auth/logout
 *      GET    /api/v1/auth/me
 * ===========================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/Http.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Resource.php';
require_once __DIR__ . '/resources.php';
require_once __DIR__ . '/controllers/AuthController.php';

/* --- Resolve the route string --------------------------------------------- */
$route = $_SERVER['API_ROUTE']
    ?? $_GET['route']
    ?? ltrim((string) ($_SERVER['PATH_INFO'] ?? ''), '/');
$route = trim((string) $route, '/');
$route = preg_replace('#^v1/?#', '', $route);      // only one version for now

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'OPTIONS') {
    api_send(null, 204);
}

$seg = $route === '' ? [] : explode('/', $route);

/* --- API index ---------------------------------------------------------- */
if (!$seg) {
    api_ok([
        'name'      => 'Barangay Management System API',
        'version'   => 'v1',
        'resources' => array_keys(api_resources()),
        'auth'      => ['POST v1/auth/login', 'POST v1/auth/logout', 'GET v1/auth/me'],
    ]);
}

/* --- Auth endpoints ---------------------------------------------------- */
if ($seg[0] === 'auth') {
    $auth   = new AuthController();
    $action = $seg[1] ?? '';

    match (true) {
        $action === 'login'  && $method === 'POST' => $auth->login(),
        $action === 'logout' && $method === 'POST' => $auth->logout(),
        $action === 'me'     && $method === 'GET'  => $auth->me(),
        default => api_fail('Unknown auth endpoint.', 404, 'not_found'),
    };
}

/* --- Resource endpoints --------------------------------------------- */
$resources = api_resources();
$slug      = $seg[0];

if (!isset($resources[$slug])) {
    api_fail("Unknown resource '$slug'. Try one of: " . implode(', ', array_keys($resources)) . '.', 404, 'not_found');
}

$cfg = $resources[$slug];
$id  = $seg[1] ?? null;
if (isset($seg[2])) {
    api_fail('Nested routes are not supported.', 404, 'not_found');
}

$resource = new Resource($cfg);
if (!$resource->allows($method)) {
    header('Allow: ' . implode(', ', $cfg['methods'] ?? ['GET']));
    api_fail("Method $method is not allowed on '$slug'.", 405, 'method_not_allowed');
}

$readRoles  = $cfg['read_roles']  ?? [];
$writeRoles = $cfg['write_roles'] ?? [];

switch ($method) {
    case 'GET':
        require_api_role($readRoles);
        $id === null ? $resource->index() : $resource->show($id);
        break;

    case 'POST':
        require_api_role($writeRoles);
        if ($id !== null) {
            api_fail('POST is not allowed on a specific item. Use PUT or PATCH.', 405, 'method_not_allowed');
        }
        $resource->store(api_body());
        break;

    case 'PUT':
    case 'PATCH':
        require_api_role($writeRoles);
        if ($id === null) {
            api_fail('An item id is required for updates.', 400, 'id_required');
        }
        $resource->update($id, api_body(), partial: $method === 'PATCH');
        break;

    case 'DELETE':
        require_api_role($writeRoles);
        if ($id === null) {
            api_fail('An item id is required for delete.', 400, 'id_required');
        }
        $resource->destroy($id);
        break;

    default:
        api_fail("Method $method is not supported.", 405, 'method_not_allowed');
}
