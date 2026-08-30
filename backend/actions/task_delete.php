<?php
/** task_delete.php — remove a task (admin only). GET id. */
declare(strict_types=1);

require __DIR__ . '/../../frontend/partials/bootstrap.php';
require_role([]);                 // admin-only (require_role always lets admin through)

$back = PAGES_URL . '/Tasks.php';
$id   = (int) ($_GET['id'] ?? 0);

if (!is_admin()) {
    http_response_code(403);
    exit('Only an administrator can delete tasks.');
}

try {
    db()->prepare('DELETE FROM tasks WHERE id = ?')->execute([$id]);
} catch (Throwable $e) {
    error_log('task_delete.php: ' . $e->getMessage());
}
header('Location: ' . $back);
