<?php
/**
 * Home.php — public home page for Barangay Paule 1.
 * Served at the site root ("/") by the front controller (root index.php);
 * still reachable directly at /frontend/pages/Home.php.
 */
require __DIR__ . '/../partials/bootstrap.php';

$pdo = db();
$one = static fn (string $s) => $pdo->query($s)->fetch() ?: [];

$stat = $one('SELECT founding_years FROM statistics ORDER BY id LIMIT 1');
$map  = $one('SELECT total_land_area FROM map_statics ORDER BY id LIMIT 1');
$pop  = $one('SELECT number_of_population FROM population ORDER BY id LIMIT 1');
$activities = (int) $pdo->query('SELECT COUNT(*) FROM activity')->fetchColumn();
$intro = $one('SELECT paragraph FROM introduction ORDER BY id LIMIT 1');

$officials = $pdo->query('SELECT photo, fullName, position FROM barangay_officials ORDER BY id')->fetchAll();

$facts = [
    ['icon' => 'bi-calendar-event', 'value' => $stat['founding_years'] ?? '—',  'label' => 'Founding year'],
    ['icon' => 'bi-geo-alt',        'value' => $map['total_land_area'] ?? '—',  'label' => 'Land area'],
    ['icon' => 'bi-people',         'value' => $pop['number_of_population'] ?? '—', 'label' => 'Population'],
    ['icon' => 'bi-list-check',     'value' => number_format($activities),      'label' => 'Activities held'],
];

$page_title = 'Official Website';
$active     = 'home';
$head_extra = '<style>.hero{--hero-img:url(' . asset('images/assembly.jpeg') . ')}</style>';

require __DIR__ . '/../partials/public_top.php';
?>

<header class="hero">
    <div class="container">
        <p class="section__eyebrow text-white-50">Welcome &amp; Discover</p>
        <h1>Barangay Paule 1<br>Rizal, Laguna</h1>
        <p>Embracing progress and unity as we move forward together toward a brighter future for every family in our community.</p>
        <div class="d-flex gap-2 justify-content-center mt-4">
            <a href="<?= page_url('GeneralInformation.php') ?>" class="btn btn-primary btn-lg">Learn more <i class="bi bi-arrow-right ms-1"></i></a>
            <a href="<?= page_url('Certificate.php') ?>" class="btn btn-light btn-lg">Request a document</a>
        </div>
    </div>
</header>

<!-- Foundational statistics -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section__eyebrow">At a glance</p>
            <h2 class="section__title">Foundational statistics</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($facts as $f): ?>
            <div class="col-6 col-lg-3">
                <div class="info-card text-center">
                    <div class="icon-circle mx-auto"><i class="bi <?= e($f['icon']) ?>"></i></div>
                    <div class="h3 mb-0"><?= e((string) $f['value']) ?></div>
                    <div class="text-muted-2 small text-uppercase" style="letter-spacing:.04em"><?= e($f['label']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About -->
<section class="section" style="background:var(--surface)">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="<?= asset('images/assembly.jpeg') ?>" alt="Barangay assembly"
                     class="img-fluid rounded-xl shadow-card" style="object-fit:cover;width:100%;max-height:360px">
            </div>
            <div class="col-lg-6">
                <p class="section__eyebrow">About us</p>
                <h2 class="section__title">By reshaping our barangay, we transform the community.</h2>
                <p class="text-muted-2">
                    <?= e($intro['paragraph'] ?? 'Barangay Paule 1 is committed to overcoming every obstacle on its path to responsive and transparent public service.') ?>
                </p>
                <a href="<?= page_url('History.php') ?>" class="btn btn-outline-secondary">Our history</a>
            </div>
        </div>
    </div>
</section>

<!-- Officials -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section__eyebrow">Leadership</p>
            <h2 class="section__title">Meet our barangay officials</h2>
        </div>
        <?php if (!$officials): ?>
            <p class="text-center text-muted-2">Officials will be listed here soon.</p>
        <?php else: ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($officials as $o): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="info-card text-center h-100">
                    <img src="<?= upload_url('photos/' . ($o['photo'] ?? '')) ?>" alt="<?= e($o['fullName']) ?>"
                         class="rounded-circle mb-3" style="width:96px;height:96px;object-fit:cover;background:var(--brand-50)"
                         onerror="this.src='<?= asset('images/logo1.png') ?>'">
                    <h3 class="h6 mb-1"><?= e($o['fullName']) ?></h3>
                    <p class="text-muted-2 small mb-0"><?= e($o['position']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
