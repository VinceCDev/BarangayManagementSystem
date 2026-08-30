<?php
/**
 * partials/admin_bottom.php — closes an admin page opened by admin_top.php.
 *
 *   $foot_extra  string  raw HTML (page-specific <script>) before </body>
 */
$foot_extra = $foot_extra ?? '';
?>
  </div><!-- /.container-inner -->
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active nav item even when a page forgot to set $active_nav.
    (function () {
        var here = location.pathname.split('/').pop();
        document.querySelectorAll('.app-nav a').forEach(function (a) {
            if (a.getAttribute('href') && a.getAttribute('href').split('/').pop() === here) {
                a.classList.add('is-active');
            }
        });
    })();
</script>
<?= $foot_extra ?>
</body>
</html>
