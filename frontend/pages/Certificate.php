<?php
/**
 * Certificate.php — PUBLIC list of the documents a resident can request.
 * The request itself happens inside the resident portal: "Request this
 * document" sends the visitor to RequestDocument.php, whose guard redirects
 * guests to the login page first.
 */
require __DIR__ . '/../partials/bootstrap.php';

// A signed-in resident goes straight to the in-portal request form.
if (current_username() && current_role() === 'resident') {
    header('Location: ' . page_url('RequestDocument.php'));
    exit;
}

$certs = db()->query('SELECT id, certificate_name, requirements FROM certificates ORDER BY certificate_name')->fetchAll();

$requestUrl = page_url('RequestDocument.php');   // guard redirects guests to Login

$page_title = 'Services';
$active     = 'services';
require __DIR__ . '/../partials/public_top.php';
?>

<header class="hero" style="min-height:34vh;--hero-img:linear-gradient(#0b2f6e,#123f92)">
    <div class="container">
        <p class="section__eyebrow text-white-50">Barangay Services</p>
        <h1>Request a certificate</h1>
        <p>Barangay Paule 1 residents can request the documents below from their resident portal.</p>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill"></i>
            You need a resident account to request a document.
            <a class="ms-auto btn btn-sm btn-primary" href="<?= page_url('Login.php') ?>">Sign in</a>
        </div>

        <?php if (!$certs): ?>
            <p class="text-center text-muted-2">No certificate templates are available right now.</p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($certs as $c):
                $reqs = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $c['requirements']))); ?>
            <div class="col-md-6 col-lg-4">
                <div class="info-card h-100 d-flex flex-column">
                    <div class="icon-circle"><i class="bi bi-award"></i></div>
                    <h3 class="h5"><?= e($c['certificate_name']) ?></h3>
                    <?php if ($reqs): ?>
                        <p class="text-caption mb-1">Requirements</p>
                        <ul class="text-muted-2 small ps-3">
                            <?php foreach ($reqs as $q): ?><li><?= e($q) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a class="btn btn-primary mt-auto" href="<?= $requestUrl ?>?cert=<?= (int) $c['id'] ?>">
                        Request this document
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
