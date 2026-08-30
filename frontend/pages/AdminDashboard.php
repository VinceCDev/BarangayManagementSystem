<?php
/**
 * AdminDashboard.php — overview of the whole system.
 * Six headline counters plus compact "recent activity" panels; each panel
 * links to the full management page.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();                       // any signed-in user (handles login + logout)

// Residents have their own dashboard.
if (current_role() === 'resident') {
    header('Location: ' . page_url('ResidentDashboard.php'));
    exit;
}
require_role(['official', 'sk_chairman', 'treasurer']);

$pdo = db();

/** Small helper: SELECT COUNT(*) FROM <table>. */
$countOf = static function (string $table) use ($pdo): int {
    // Table name is a hard-coded literal below, never user input.
    return (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
};

$allStats = [
    ['key' => 'officials', 'label' => 'Barangay Officials', 'value' => $countOf('barangay_officials'), 'icon' => 'bi-people',            'href' => 'BarangayOfficial.php', 'mod' => ''],
    ['key' => 'residents', 'label' => 'Residents',          'value' => $countOf('residents'),          'icon' => 'bi-person-vcard',      'href' => 'Resident.php',         'mod' => 'stat--green'],
    ['key' => 'blotter',   'label' => 'Blotter Records',    'value' => $countOf('blotterrecords'),     'icon' => 'bi-journal-text',      'href' => 'Blotter.php',          'mod' => 'stat--red'],
    ['key' => 'requests',  'label' => 'Document Requests',  'value' => $countOf('document_requests'),  'icon' => 'bi-file-earmark-text', 'href' => 'DocumentRequest.php',  'mod' => 'stat--gold'],
    ['key' => 'tasks',     'label' => 'Open Tasks',         'value' => (int) $pdo->query("SELECT COUNT(*) FROM tasks WHERE status <> 'Done'")->fetchColumn(), 'icon' => 'bi-check2-square', 'href' => 'Tasks.php', 'mod' => 'stat--violet'],
    ['key' => 'activity',  'label' => 'Activities',         'value' => $countOf('activity'),           'icon' => 'bi-calendar-event',    'href' => 'Activity.php',         'mod' => 'stat--teal'],
];
$stats = array_values(array_filter($allStats, static fn ($s) => role_can($s['key'])));

$recentResidents = $pdo->query(
    'SELECT photo, full_name, age, occupation, contact FROM residents ORDER BY id DESC LIMIT 6'
)->fetchAll();

$recentBlotter = $pdo->query(
    'SELECT status, complainant, personToComplaint, created_at FROM blotterrecords ORDER BY id DESC LIMIT 6'
)->fetchAll();

$recentActivities = $pdo->query(
    'SELECT photos, activity, date, description FROM activity ORDER BY id DESC LIMIT 5'
)->fetchAll();

$recentRequests = $pdo->query(
    'SELECT dr.fullName, c.certificate_name, dr.purpose, dr.request_date
       FROM document_requests dr
       LEFT JOIN certificates c ON c.id = dr.certificate_id
      ORDER BY dr.id DESC LIMIT 6'
)->fetchAll();

/** Map a blotter status string to a pill style. */
function status_pill(string $status): string
{
    $s = strtolower(trim($status));
    $cls = match (true) {
        str_contains($s, 'resolve'), str_contains($s, 'done'), str_contains($s, 'settle') => 'pill--success',
        str_contains($s, 'progress'), str_contains($s, 'ongoing')                          => 'pill--info',
        str_contains($s, 'pending'), $s === ''                                             => 'pill--warning',
        default                                                                            => 'pill--muted',
    };
    return '<span class="pill ' . $cls . '">' . e($status ?: 'Pending') . '</span>';
}

$page_title    = 'Dashboard';
$page_heading  = 'Dashboard';
$page_subtitle = 'A snapshot of barangay records and service requests.';
$active_nav    = 'dashboard';

require __DIR__ . '/../partials/admin_top.php';
?>

<!-- Headline counters -->
<section class="stat-grid" aria-label="Summary">
    <?php foreach ($stats as $s): ?>
        <a class="stat <?= e($s['mod']) ?> text-decoration-none" href="<?= page_url($s['href']) ?>">
            <span class="stat__icon"><i class="bi <?= e($s['icon']) ?>"></i></span>
            <span>
                <span class="stat__value"><?= number_format($s['value']) ?></span>
                <span class="stat__label"><?= e($s['label']) ?></span>
            </span>
        </a>
    <?php endforeach; ?>
</section>

<div class="row g-4">
    <!-- Recent residents -->
    <?php if (role_can('residents')): ?>
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-hd">
                <span class="card-hd__title"><i class="bi bi-person-vcard"></i> Recent Residents</span>
                <span class="spacer"></span>
                <a class="btn btn-sm btn-outline-secondary" href="<?= page_url('Resident.php') ?>">View all</a>
            </div>
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Name</th><th>Age</th><th>Occupation</th><th>Contact</th></tr></thead>
                    <tbody>
                    <?php if (!$recentResidents): ?>
                        <tr><td colspan="4"><div class="empty py-4"><i class="bi bi-inbox"></i>No residents yet.</div></td></tr>
                    <?php else: foreach ($recentResidents as $r): ?>
                        <tr>
                            <td class="d-flex align-items-center gap-2">
                                <img class="thumb" src="<?= upload_url('resident_photo/' . ($r['photo'] ?? '')) ?>"
                                     alt="" onerror="this.src='<?= asset('images/logo1.png') ?>'">
                                <span class="fw-semibold"><?= e($r['full_name']) ?></span>
                            </td>
                            <td><?= e((string) $r['age']) ?></td>
                            <td><?= e($r['occupation']) ?></td>
                            <td><?= e($r['contact']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent blotter -->
    <?php if (role_can('blotter')): ?>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-hd">
                <span class="card-hd__title"><i class="bi bi-journal-text"></i> Recent Blotter</span>
                <span class="spacer"></span>
                <a class="btn btn-sm btn-outline-secondary" href="<?= page_url('Blotter.php') ?>">View all</a>
            </div>
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Complainant</th><th>Respondent</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if (!$recentBlotter): ?>
                        <tr><td colspan="3"><div class="empty py-4"><i class="bi bi-inbox"></i>No blotter records.</div></td></tr>
                    <?php else: foreach ($recentBlotter as $b): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($b['complainant']) ?></td>
                            <td><?= e($b['personToComplaint']) ?></td>
                            <td><?= status_pill((string) $b['status']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent document requests -->
    <?php if (role_can('requests')): ?>
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-hd">
                <span class="card-hd__title"><i class="bi bi-file-earmark-text"></i> Recent Document Requests</span>
                <span class="spacer"></span>
                <a class="btn btn-sm btn-outline-secondary" href="<?= page_url('DocumentRequest.php') ?>">View all</a>
            </div>
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Requested by</th><th>Certificate</th><th>Purpose</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php if (!$recentRequests): ?>
                        <tr><td colspan="4"><div class="empty py-4"><i class="bi bi-inbox"></i>No requests yet.</div></td></tr>
                    <?php else: foreach ($recentRequests as $q): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($q['fullName']) ?></td>
                            <td><?= e($q['certificate_name'] ?? '—') ?></td>
                            <td><?= e($q['purpose']) ?></td>
                            <td><?= e($q['request_date'] ? date('M j, Y', strtotime((string) $q['request_date'])) : '—') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent activities -->
    <?php if (role_can('activity')): ?>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-hd">
                <span class="card-hd__title"><i class="bi bi-calendar-event"></i> Recent Activities</span>
                <span class="spacer"></span>
                <a class="btn btn-sm btn-outline-secondary" href="<?= page_url('Activity.php') ?>">View all</a>
            </div>
            <div class="card-bd stack">
                <?php if (!$recentActivities): ?>
                    <div class="empty py-4"><i class="bi bi-inbox"></i>No activities recorded.</div>
                <?php else: foreach ($recentActivities as $a): ?>
                    <div class="d-flex gap-3">
                        <img class="thumb" style="width:48px;height:48px;border-radius:10px"
                             src="<?= upload_url('activity_photos/' . ($a['photos'] ?? '')) ?>"
                             alt="" onerror="this.src='<?= asset('images/logo1.png') ?>'">
                        <div>
                            <div class="fw-semibold"><?= e($a['activity']) ?></div>
                            <div class="text-caption"><?= e($a['date'] ? date('M j, Y', strtotime((string) $a['date'])) : '') ?></div>
                            <div class="small text-muted-2 text-truncate" style="max-width:22rem"><?= e($a['description']) ?></div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
