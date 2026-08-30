<?php
/**
 * ResidentDashboard.php — landing page for a signed-in resident.
 * Shows their document-request status, recent messages and barangay
 * announcements. Records are matched by the account e-mail.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['resident']);

$pdo   = db();
$email = current_username();

$reqStats = ['total' => 0];
$st = $pdo->prepare('SELECT COUNT(*) FROM document_requests WHERE email = ?');
$st->execute([$email]);
$reqStats['total'] = (int) $st->fetchColumn();

$myRequests = $pdo->prepare(
    'SELECT dr.*, c.certificate_name FROM document_requests dr
       LEFT JOIN certificates c ON c.id = dr.certificate_id
      WHERE dr.email = ? ORDER BY dr.id DESC LIMIT 5'
);
$myRequests->execute([$email]);
$myRequests = $myRequests->fetchAll();

$myMsgs = $pdo->prepare('SELECT * FROM receivemessages WHERE email = ? ORDER BY id DESC LIMIT 4');
$myMsgs->execute([$email]);
$myMsgs = $myMsgs->fetchAll();

$announcements = $pdo->query('SELECT activity, date, description FROM activity ORDER BY date DESC, id DESC LIMIT 4')->fetchAll();

$st = $pdo->prepare('SELECT firstname FROM profiledata WHERE email = ? LIMIT 1');
$st->execute([$email]);
$firstName = $st->fetchColumn() ?: 'Resident';

$page_title   = 'Dashboard';
$page_heading = 'Welcome, ' . $firstName;
$page_subtitle = 'Your barangay services at a glance.';
$active_nav   = 'dashboard';
require __DIR__ . '/../partials/admin_top.php';
?>

<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
    <a class="stat text-decoration-none" href="<?= page_url('MyRequests.php') ?>">
        <span class="stat__icon"><i class="bi bi-file-earmark-text"></i></span>
        <span><span class="stat__value"><?= number_format($reqStats['total']) ?></span>
        <span class="stat__label">My document requests</span></span>
    </a>
    <a class="stat stat--green text-decoration-none" href="<?= page_url('MyMessages.php') ?>">
        <span class="stat__icon"><i class="bi bi-chat-left-dots"></i></span>
        <span><span class="stat__value"><?= count($myMsgs) ?>+</span>
        <span class="stat__label">My messages</span></span>
    </a>
    <a class="stat stat--teal text-decoration-none" href="<?= page_url('Certificate.php') ?>">
        <span class="stat__icon"><i class="bi bi-plus-square"></i></span>
        <span><span class="stat__value">New</span>
        <span class="stat__label">Request a document</span></span>
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-hd">
                <span class="card-hd__title"><i class="bi bi-file-earmark-text"></i> My recent requests</span>
                <span class="spacer"></span>
                <a class="btn btn-sm btn-outline-secondary" href="<?= page_url('MyRequests.php') ?>">View all</a>
            </div>
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Certificate</th><th>Purpose</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php if (!$myRequests): ?>
                        <tr><td colspan="3"><div class="empty py-4"><i class="bi bi-inbox"></i>No requests yet.
                            <div class="mt-2"><a class="btn btn-sm btn-primary" href="<?= page_url('Certificate.php') ?>">Request a document</a></div></div></td></tr>
                    <?php else: foreach ($myRequests as $r): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($r['certificate_name'] ?? '—') ?></td>
                            <td><?= e($r['purpose'] ?: '—') ?></td>
                            <td><?= e($r['request_date'] ? date('M j, Y', strtotime((string) $r['request_date'])) : '—') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-hd"><span class="card-hd__title"><i class="bi bi-megaphone"></i> Announcements</span></div>
            <div class="card-bd stack">
                <?php if (!$announcements): ?>
                    <div class="empty py-4"><i class="bi bi-inbox"></i>No announcements.</div>
                <?php else: foreach ($announcements as $a): ?>
                    <div>
                        <div class="fw-semibold"><?= e($a['activity']) ?></div>
                        <div class="text-caption"><?= e($a['date'] ? date('M j, Y', strtotime((string) $a['date'])) : '') ?></div>
                        <div class="small text-muted-2"><?= e($a['description']) ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
