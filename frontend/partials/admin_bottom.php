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

<!-- Shared read-only "View" modal (populated by view_button() / showView()) -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalTitle">Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <dl class="detail-list" id="viewModalBody"></dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

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

    // Populate + open the shared read-only View modal.
    var _viewModal;
    function showView(btn) {
        var data = JSON.parse(btn.dataset.view);
        document.getElementById('viewModalTitle').textContent = data.title || 'Details';
        var dl = document.getElementById('viewModalBody');
        dl.innerHTML = '';
        Object.keys(data.fields).forEach(function (label) {
            var val = data.fields[label];
            var dt = document.createElement('dt');  dt.textContent = label;
            var dd = document.createElement('dd');  dd.textContent = (val === '' || val == null) ? '—' : val;
            dl.appendChild(dt); dl.appendChild(dd);
        });
        _viewModal = _viewModal || new bootstrap.Modal('#viewModal');
        _viewModal.show();
    }

    // Confirm before signing out.
    function confirmLogout(e) {
        if (e) e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Sign out?',
            text: 'You will be returned to the login screen.',
            showCancelButton: true,
            confirmButtonText: 'Sign out',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#c0392b',
            reverseButtons: true
        }).then(function (r) { if (r.isConfirmed) location.href = '?logout=1'; });
        return false;
    }
</script>
<?= $foot_extra ?>
</body>
</html>
