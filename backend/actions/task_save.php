<?php
/**
 * task_save.php — create / update a task, or (for an assignee) update their
 * own task: change status, add a note, and upload a file for the admin to
 * view and download.
 *
 * POST forms (both may include an "attachment" file upload):
 *   status_only=1, id, status, note              -> assignee updates their task
 *   id(optional), title, description, assignee_email, priority, status,
 *   due_date, note                               -> admin creates / edits
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

/** Save an uploaded task file, return its stored name (or null). */
function save_task_file(): ?string
{
    if (($_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $f = $_FILES['attachment'];
    if ($f['size'] > 10_000_000) {
        throw new RuntimeException('The file is too large (max 10 MB).');
    }
    $ext = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx'];
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Allowed file types: PDF, image, Word or Excel.');
    }
    $dir = UPLOAD_PATH . '/task_files';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Could not store the file.');
    }
    return $name;
}

try {
    $newFile = save_task_file();
    $note    = trim($_POST['note'] ?? '');

    /* ---- assignee updating their own task --------------------------- */
    if (($_POST['status_only'] ?? '') === '1') {
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['Pending', 'In Progress', 'Done'], true)) {
            exit('Invalid status.');
        }

        $sets = ['status = ?'];
        $args = [$status];
        if ($note !== '')      { $sets[] = 'note = ?';       $args[] = $note; }
        if ($newFile !== null) { $sets[] = 'attachment = ?'; $args[] = $newFile; }

        $sql = 'UPDATE tasks SET ' . implode(', ', $sets) . ' WHERE id = ?';
        $args[] = $id;
        if (!is_admin()) {                       // assignees can only touch their own
            $sql .= ' AND assignee_email = ?';
            $args[] = current_username();
        }
        $pdo->prepare($sql)->execute($args);
        header('Location: ' . $back);
        exit;
    }

    /* ---- admin create / edit -------------------------------------- */
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

    $u = $pdo->prepare('SELECT fullName, userType FROM users WHERE userName = ? LIMIT 1');
    $u->execute([$assignee]);
    $row = $u->fetch() ?: ['fullName' => $assignee, 'userType' => 'resident'];
    $aName = $row['fullName'];
    $aRole = strtolower((string) $row['userType']);

    if ($id > 0) {
        $sets = ['title=?', 'description=?', 'assignee_email=?', 'assignee_name=?', 'assignee_role=?',
                 'priority=?', 'status=?', 'due_date=?', 'note=?'];
        $args = [$title, $description, $assignee, $aName, $aRole, $priority, $status, $due, $note];
        if ($newFile !== null) { $sets[] = 'attachment=?'; $args[] = $newFile; }
        $args[] = $id;
        $pdo->prepare('UPDATE tasks SET ' . implode(', ', $sets) . ' WHERE id=?')->execute($args);
    } else {
        $pdo->prepare(
            'INSERT INTO tasks (title, description, assignee_email, assignee_name, assignee_role,
                                priority, status, due_date, note, attachment, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$title, $description, $assignee, $aName, $aRole, $priority, $status, $due, $note, $newFile, current_username()]);
    }

    header('Location: ' . $back);
    exit;
} catch (RuntimeException $e) {
    exit('Upload problem: ' . $e->getMessage());
} catch (Throwable $e) {
    error_log('task_save.php: ' . $e->getMessage());
    exit('Could not save the task. Please try again.');
}
