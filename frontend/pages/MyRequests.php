<?php
/**
 * MyRequests.php — a resident's own certificate / document requests.
 * Matched by the account e-mail; each row links to its generated PDF.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['resident']);

$pdo   = db();
$email = current_username();

$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$st = $pdo->prepare('SELECT COUNT(*) FROM document_requests WHERE email = ?');
$st->execute([$email]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

$st = $pdo->prepare(
    "SELECT dr.*, c.certificate_name FROM document_requests dr
       LEFT JOIN certificates c ON c.id = dr.certificate_id
      WHERE dr.email = ? ORDER BY dr.id DESC LIMIT $perPage OFFSET $offset"
);
$st->execute([$email]);
$rows = $st->fetchAll();

$pdfUrl = action_url('service_pdf.php');

$page_title   = 'My Document Requests';
$page_heading = 'My Document Requests';
$page_subtitle = $total . ' request' . ($total === 1 ? '' : 's');
$active_nav   = 'myrequests';
$page_actions = '<a class="btn btn-primary" href="' . page_url('Certificate.php') . '"><i class="bi bi-plus-lg me-1"></i>Request a Document</a>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Certificate</th><th>Purpose</th><th>Date requested</th><th class="col-actions">Document</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="4"><div class="empty">
                    <i class="bi bi-file-earmark-x"></i><h3>No requests yet</h3>
                    <p>Use “Request a Document” to get a barangay certificate or clearance.</p>
                </div></td></tr>
            <?php else: foreach ($rows as $r):
                $view = ['Certificate' => $r['certificate_name'] ?? '—', 'Full name' => $r['fullName'],
                         'Purpose' => $r['purpose'], 'Address' => $r['address'], 'Business' => $r['business'] ?: '—',
                         'Date requested' => $r['request_date']]; ?>
                <tr>
                    <td><span class="pill pill--info"><?= e($r['certificate_name'] ?? 'Unknown') ?></span></td>
                    <td><?= e($r['purpose'] ?: '—') ?></td>
                    <td><?= e($r['request_date'] ? date('M j, Y', strtotime((string) $r['request_date'])) : '—') ?></td>
                    <td class="col-actions">
                        <?= view_button($view, 'Request details') ?>
                        <a class="btn btn-sm btn-outline-secondary" target="_blank"
                           href="<?= $pdfUrl ?>?id=<?= (int) $r['id'] ?>&action=view"><i class="bi bi-eye me-1"></i>View</a>
                        <a class="btn btn-sm btn-outline-secondary"
                           href="<?= $pdfUrl ?>?id=<?= (int) $r['id'] ?>&action=download"><i class="bi bi-download me-1"></i>PDF</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?= render_pager($page, $pages, $total, [], $perPage) ?>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
