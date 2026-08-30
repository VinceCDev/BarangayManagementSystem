<?php
/**
 * task_save.php — create / update a task, or (for an assignee) change its status.
 *
 * POST forms:
 *   status_only=1, id, status                     -> assignee advances their own task
 *   id(optional), title, description, assignee_email, priority, status, due_date
 *                                                 -> admin creates / edits
 */
declare(strict_types=1);

require __DIR__ . '/../../frontend/partials/bootstrap.php';
require_role(['official', 'sk_chairman', 'treasurer']);

$pdo  = db();
$back = PAGES_URL . '/Tasks.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $back);
    exit;
}

try {
    /* ---- assignee changing only the status of their own task ---------- */
    if (($_POST['status_only'] ?? '') === '1') {
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['Pending', 'In Progress', 'Done'], true)) {
            exit('Invalid status.');
        }
        $sql = 'UPDATE tasks SET status = ? WHERE id = ?';
        $args = [$status, $id];
        if (!is_admin()) {                       // assignees can only touch their own
            $sql .= ' AND assignee_email = ?';
            $args[] = current_username();
        }
        $pdo->prepare($sql)->execute($args);
        header('Location: ' . $back);
        exit;
    }

    /* ---- admin create / edit --------------------------------------- */
    if (!is_admin()) {
        http_response_code(403);
        exit('Only an administrator can create or edit tasks.');
    }

    $id          = (int) ($_POST['id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assignee    = trim($_POST['assignee_email'] ?? '');
    $priority    = in_array($_POST['priority'] ?? '', ['Low', 'Normal', 'High'], true) ? $_POST['priority'] : 'Normal';
    $status      = in_array($_POST['status'] ?? '', ['Pending', 'In Progress', 'Done'], true) ? $_POST['status'] : 'Pending';
    $due         = ($_POST['due_date'] ?? '') !== '' ? $_POST['due_date'] : null;

    if ($title === '' || $assignee === '') {
        exit('Title and assignee are required.');
    }

    // Resolve the assignee's display name + role.
    $u = $pdo->prepare('SELECT fullName, userType FROM users WHERE userName = ? LIMIT 1');
    $u->execute([$assignee]);
    $row = $u->fetch() ?: ['fullName' => $assignee, 'userType' => 'resident'];
    $aName = $row['fullName'];
    $aRole = strtolower((string) $row['userType']);

    if ($id > 0) {
        $pdo->prepare(
            'UPDATE tasks SET title=?, description=?, assignee_email=?, assignee_name=?, assignee_role=?,
                              priority=?, status=?, due_date=? WHERE id=?'
        )->execute([$title, $description, $assignee, $aName, $aRole, $priority, $status, $due, $id]);
    } else {
        $pdo->prepare(
            'INSERT INTO tasks (title, description, assignee_email, assignee_name, assignee_role,
                                priority, status, due_date, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$title, $description, $assignee, $aName, $aRole, $priority, $status, $due, current_username()]);
    }

    header('Location: ' . $back);
    exit;
} catch (Throwable $e) {
    error_log('task_save.php: ' . $e->getMessage());
    exit('Could not save the task. Please try again.');
}
