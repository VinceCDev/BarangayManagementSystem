<?php
/**
 * DocumentRequest.php — certificate requests submitted by residents (read-only list).
 * Each row links to the generated PDF via backend/actions/service_pdf.php.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$search  = trim($_GET['search'] ?? '');
$perPage = 12;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$like    = '%' . $search . '%';

$st = $pdo->prepare(
    'SELECT COUNT(*) FROM document_requests
      WHERE fullName LIKE ? OR purpose LIKE ? OR email LIKE ? OR business LIKE ?'
);
$st->execute([$like, $like, $like, $like]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare(
    'SELECT dr.*, c.certificate_name
       FROM document_requests dr
       LEFT JOIN certificates c ON c.id = dr.certificate_id
      WHERE dr.fullName LIKE ? OR dr.purpose LIKE ? OR dr.email LIKE ? OR dr.business LIKE ?
      ORDER BY dr.id DESC LIMIT ? OFFSET ?'
);
$st->bindValue(1, $like); $st->bindValue(2, $like); $st->bindValue(3, $like); $st->bindValue(4, $like);
$st->bindValue(5, $perPage, PDO::PARAM_INT);
$st->bindValue(6, $offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();

$pdfUrl = action_url('service_pdf.php');

$page_title    = 'Document Requests';
$page_heading  = 'Document Requests';
$page_subtitle = $total . ' request' . ($total === 1 ? '' : 's');
$active_nav    = 'requests';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
                   placeholder="Search requester, purpose, email…">
        </div>
        <?php if ($search): ?><a class="btn btn-outline-secondary" href="?">Clear</a><?php endif; ?>
        <span class="spacer"></span>
        <span class="text-caption"><?= $total ?> result<?= $total === 1 ? '' : 's' ?></span>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Requested by</th><th>Certificate</th><th>Purpose</th><th>Date</th><th class="col-actions">Document</th></tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="5"><div class="empty"><i class="bi bi-file-earmark-x"></i><h3>No document requests</h3></div></td></tr>
            <?php else: foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= e($r['fullName']) ?></div>
                        <div class="text-caption"><?= e($r['email'] ?: '—') ?><?= $r['business'] ? ' · ' . e($r['business']) : '' ?></div>
                    </td>
                    <td><span class="pill pill--info"><?= e($r['certificate_name'] ?? 'Unknown') ?></span></td>
                    <td class="text-truncate" style="max-width:16rem"><?= e($r['purpose'] ?: '—') ?></td>
                    <td><?= e($r['request_date'] ? date('M j, Y', strtotime((string) $r['request_date'])) : '—') ?></td>
                    <td class="col-actions">
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

    <?php if ($pages > 1): ?>
    <div class="card-ft pager">
        <span class="pager__info">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
        <a class="btn btn-sm btn-outline-secondary <?= $page <= 1 ? 'disabled' : '' ?>" href="?<?= http_build_query(['search' => $search, 'page' => $page - 1]) ?>">Previous</a>
        <a class="btn btn-sm btn-outline-secondary <?= $page >= $pages ? 'disabled' : '' ?>" href="?<?= http_build_query(['search' => $search, 'page' => $page + 1]) ?>">Next</a>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
