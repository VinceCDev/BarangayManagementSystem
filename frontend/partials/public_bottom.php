<?php
/** partials/public_bottom.php — footer + scripts for the public site. */
$foot_extra = $foot_extra ?? '';
?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="<?= asset('images/logo1.png') ?>" alt="" width="40" height="40">
                    <h4 class="mb-0">Barangay Paule 1</h4>
                </div>
                <p class="mb-0" style="max-width:26rem">
                    Rizal, Laguna. Serving the community with responsive and transparent public service.
                </p>
            </div>
            <div class="col-6 col-md-3">
                <h4>Explore</h4>
                <ul class="list-unstyled d-grid gap-1">
                    <li><a href="<?= page_url('GeneralInformation.php') ?>">General Information</a></li>
                    <li><a href="<?= page_url('History.php') ?>">History</a></li>
                    <li><a href="<?= page_url('Certificate.php') ?>">Services</a></li>
                    <li><a href="<?= page_url('FAQ.php') ?>">FAQ</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h4>Get in touch</h4>
                <ul class="list-unstyled d-grid gap-1">
                    <li><i class="bi bi-geo-alt me-2"></i>Barangay Hall, Paule 1, Rizal, Laguna</li>
                    <li><i class="bi bi-envelope me-2"></i><a href="<?= page_url('Contact.php') ?>">Send a message</a></li>
                </ul>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.15)">
        <p class="small mb-0">&copy; <?= date('Y') ?> Barangay Paule 1. All rights reserved.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $foot_extra ?>
</body>
</html>
