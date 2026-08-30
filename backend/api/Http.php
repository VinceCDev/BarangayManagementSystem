<?php
/**
 * ---------------------------------------------------------------------------
 *  api/Http.php — tiny request / response helpers for the JSON API
 * ---------------------------------------------------------------------------
 *  Every API response has the same envelope so clients can rely on it:
 *
 *      success : { "data": <payload>, "meta": { ... }? }
 *      error   : { "error": { "message": "...", "code": "...", "fields": {}? } }
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

/** Send a JSON payload and stop. */
function api_send(mixed $payload, int $status = 200): never
{
    http_response_code($status);
    header('X-Content-Type-Options: nosniff');
    // 204/205 must not carry a body.
    if ($status === 204 || $status === 205) {
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/** Success envelope. */
function api_ok(mixed $data, array $meta = [], int $status = 200): never
{
    $body = ['data' => $data];
    if ($meta) {
        $body['meta'] = $meta;
    }
    api_send($body, $status);
}

/** Error envelope. */
function api_fail(string $message, int $status = 400, string $code = '', array $fields = []): never
{
    $err = ['message' => $message, 'code' => $code ?: (string) $status];
    if ($fields) {
        $err['fields'] = $fields;
    }
    api_send(['error' => $err], $status);
}

/**
 * Decode the request body. Accepts JSON (preferred) and falls back to normal
 * form-encoded POST data so the API is easy to try from an HTML form or curl.
 *
 * @return array<string,mixed>
 */
function api_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $ct  = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($ct, 'application/json')) {
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            api_fail('Request body is not valid JSON.', 400, 'invalid_json');
        }
        return $data;
    }

    if ($_POST) {
        return $_POST;
    }

    // last try: maybe a JSON body without the header
    if ($raw !== '') {
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [];
}

/** Read + clamp the ?page= / ?per_page= pagination inputs. */
function api_pagination(int $defaultPer = 20, int $maxPer = 100): array
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $per  = (int) ($_GET['per_page'] ?? $defaultPer);
    $per  = max(1, min($maxPer, $per));
    return [$page, $per, ($page - 1) * $per];
}
