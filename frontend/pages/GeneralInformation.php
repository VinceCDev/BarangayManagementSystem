<?php
/**
 * GeneralInformation.php — public "General Information" page.
 * All content is editable from the admin › Barangay Information page.
 */
require __DIR__ . '/../partials/bootstrap.php';

$pdo = db();
$one = static fn (string $s) => $pdo->query($s)->fetch() ?: [];
$col = static fn (string $s, string $c) => array_column($pdo->query($s)->fetchAll(), $c);

$intro   = $one('SELECT paragraph FROM introduction ORDER BY id LIMIT 1')['paragraph'] ?? '';
$vision  = $one('SELECT paragraph FROM vision ORDER BY id LIMIT 1')['paragraph'] ?? '';
$mission = $one('SELECT paragraph FROM mission ORDER BY id LIMIT 1')['paragraph'] ?? '';
$pop     = $one('SELECT number_of_population, average_household_size FROM population ORDER BY id LIMIT 1');

$economics = $col('SELECT message FROM economics ORDER BY id', 'message');
$business  = $col('SELECT business_text FROM major_business ORDER BY id', 'business_text');
$income    = $col('SELECT income_text FROM major_income ORDER BY id', 'income_text');

$page_title = 'General Information';
$active     = 'info';
require __DIR__ . '/../partials/public_top.php';
?>

<header class="hero" style="min-height:38vh;--hero-img:linear-gradient(#0b2f6e,#0b2f6e)">
    <div class="container">
        <p class="section__eyebrow text-white-50">Our Barangay</p>
        <h1>General Information</h1>
        <p>Get to know Barangay Paule 1 — our people, our vision and our local economy.</p>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4"><div class="info-card h-100">
                <div class="icon-circle"><i class="bi bi-card-text"></i></div>
                <h3 class="h5">Introduction</h3>
                <p class="text-muted-2 mb-0"><?= nl2br(e($intro ?: 'Not available yet.')) ?></p>
            </div></div>
            <div class="col-lg-4"><div class="info-card h-100">
                <div class="icon-circle"><i class="bi bi-eye"></i></div>
                <h3 class="h5">Vision</h3>
                <p class="text-muted-2 mb-0"><?= nl2br(e($vision ?: 'Not available yet.')) ?></p>
            </div></div>
            <div class="col-lg-4"><div class="info-card h-100">
                <div class="icon-circle"><i class="bi bi-flag"></i></div>
                <h3 class="h5">Mission</h3>
                <p class="text-muted-2 mb-0"><?= nl2br(e($mission ?: 'Not available yet.')) ?></p>
            </div></div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--surface)">
    <div class="container">
        <p class="section__eyebrow">Demographics</p>
        <h2 class="section__title mb-4">Population</h2>
        <div class="row g-4">
            <div class="col-sm-6 col-lg-4"><div class="info-card">
                <div class="h2 mb-0"><?= e($pop['number_of_population'] ?? '—') ?></div>
                <div class="text-muted-2">Total population</div>
            </div></div>
            <div class="col-sm-6 col-lg-4"><div class="info-card">
                <div class="h2 mb-0"><?= e($pop['average_household_size'] ?? '—') ?></div>
                <div class="text-muted-2">Average household size</div>
            </div></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <p class="section__eyebrow">Local Economy</p>
        <h2 class="section__title mb-4">Economy</h2>
        <div class="row g-4">
            <?php
            $blocks = [
                ['Predominant activities', 'bi-graph-up', $economics],
                ['Major businesses', 'bi-shop', $business],
                ['Sources of income', 'bi-cash-coin', $income],
            ];
            foreach ($blocks as [$title, $icon, $items]): ?>
            <div class="col-lg-4"><div class="info-card h-100">
                <div class="icon-circle"><i class="bi <?= e($icon) ?>"></i></div>
                <h3 class="h5"><?= e($title) ?></h3>
                <?php if ($items): ?>
                    <ul class="text-muted-2 mb-0 ps-3">
                        <?php foreach ($items as $it): ?><li><?= e($it) ?></li><?php endforeach; ?>
                    </ul>
                <?php else: ?><p class="text-muted-2 mb-0">Not available yet.</p><?php endif; ?>
            </div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
