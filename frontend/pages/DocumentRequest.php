<?php
/**
 * DocumentRequest.php — certificate requests submitted by residents (read-only list).
 * Each row links to the generated PDF via backend/actions/service_pdf.php.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$certOptions = $pdo->query('SELECT id, certificate_name FROM certificates ORDER BY certificate_name')
                   ->fetchAll(PDO::FETCH_KEY_PAIR);

$search = trim($_GET['search'] ?? '');
$fCert  = (int) ($_GET['cert'] ?? 0);
$perPage = 12;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$where  = ['(dr.fullName LIKE ? OR dr.purpose LIKE ? OR dr.email LIKE ? OR dr.business LIKE ?)'];
$params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'];
if ($fCert && isset($certOptions[$fCert])) { $where[] = 'dr.certificate_id = ?'; $params[] = $fCert; }
$whereSql = implode(' AND ', $where);

$st = $pdo->prepare("SELECT COUNT(*) FROM document_requests dr WHERE $whereSql");
$st->execute($params);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

$st = $pdo->prepare(
    "SELECT dr.*, c.certificate_name
       FROM document_requests dr
       LEFT JOIN certificates c ON c.id = dr.certificate_id
      WHERE $whereSql
      ORDER BY dr.id DESC LIMIT $perPage OFFSET $offset"
);
$st->execute($params);
$rows = $st->fetchAll();

$qs = ['search' => $search, 'cert' => $fCert];
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
        <span class="spacer"></span>
        <?= filter_select('cert', (string) $fCert, ['' => 'All certificates'] + $certOptions) ?>
        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
        <?php if ($search || $fCert): ?><a class="btn btn-sm btn-outline-secondary" href="?">Reset</a><?php endif; ?>
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

    <?= render_pager($page, $pages, $total, $qs ?? ['search' => $search], $perPage) ?>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
