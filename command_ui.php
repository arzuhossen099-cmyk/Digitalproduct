<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$title = "Saved Search";
require_once __DIR__ . '/includes/header_user.php';
?>

<div class="lp-card mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-bold mb-1">Command Palette</h4>
        <p class="text-muted small mb-0">Press <kbd class="bg-secondary border-0">Ctrl + K</kbd> to quickly search anything.</p>
    </div>
    <div class="lp-badge lp-badge-info">Beta</div>
</div>

<div id="commandPalette" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-card border-secondary shadow-lg">
            <div class="modal-body p-0">
                <div class="p-3 border-bottom border-secondary">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-search text-muted me-3"></i>
                        <input type="text" class="form-control bg-transparent border-0 text-primary shadow-none" placeholder="Search leads, settings, or docs..." autofocus id="commandInput">
                    </div>
                </div>
                <div id="commandResults" class="p-2" style="max-height: 400px; overflow-y: auto;">
                    <div class="p-3 text-muted small">Type to search...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<script>
window.addEventListener('load', function() {
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        $('#commandPalette').modal('show');
    }
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.role = 'alert';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.getElementById('toastContainer').appendChild(toast);
    new bootstrap.Toast(toast).show();
}

$('#commandPalette').on('shown.bs.modal', function () {
    $('#commandInput').focus();
});
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
