<?php
// admin/includes/footer.php
?>
    </div><!-- /.admin-content -->
</main>
</div><!-- /.admin-layout -->

<script>
// Init Lucide
if (window.lucide) lucide.createIcons();

// مكتبة الأيقونات للـ Picker
const ICON_LIBRARY = <?= json_encode($__iconList ?? [], JSON_UNESCAPED_UNICODE) ?>;

// ============= Mobile Sidebar =============
(function () {
    const sidebar  = document.getElementById('adminSidebar');
    const toggle   = document.getElementById('mobileToggle');
    const overlay  = document.getElementById('mobileOverlay');
    if (!sidebar || !toggle) return;

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('show'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); document.body.style.overflow = ''; }

    toggle.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
})();

// ============= SweetAlert Flash =============
<?php if ($__flash): ?>
Swal.fire({
    toast: true,
    position: 'top-start',
    icon: <?= json_encode($__flash['type'] === 'error' ? 'error' : 'success') ?>,
    title: <?= json_encode($__flash['message'], JSON_UNESCAPED_UNICODE) ?>,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: { popup: 'swal-toast' }
});
<?php endif; ?>

// ============= SweetAlert Helpers =============
function confirmDelete(url, message) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: message || 'هل أنت متأكد من الحذف؟ لا يمكن التراجع.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#C62828',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then(function (r) { if (r.isConfirmed) window.location.href = url; });
    return false;
}

function confirmLogout(url) {
    Swal.fire({
        title: 'تسجيل الخروج',
        text: 'هل تريد تسجيل الخروج من لوحة التحكم؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'خروج',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#6B1D23',
        reverseButtons: true
    }).then(function (r) { if (r.isConfirmed) window.location.href = url; });
    return false;
}

// ============= Icon Picker (SweetAlert) =============
function openIconPicker(hiddenInputId, previewId) {
    const hidden  = document.getElementById(hiddenInputId);
    const preview = document.getElementById(previewId);

    let html = '<div class="icon-picker-grid">';
    ICON_LIBRARY.forEach(function (ic) {
        const svg = '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + ic.path + '</svg>';
        html += '<button type="button" class="icon-pick-item" data-icon="' + ic.name + '" data-svg="' + svg.replace(/"/g, '&quot;') + '">'
              + '<span class="icon-pick-preview">' + svg + '</span>'
              + '<span class="icon-pick-name">' + ic.label + '</span>'
              + '</button>';
    });
    html += '</div>';

    Swal.fire({
        title: 'اختر أيقونة',
        html: html,
        width: 680,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: { popup: 'icon-picker-popup' },
        didOpen: function () {
            document.querySelectorAll('.icon-pick-item').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    hidden.value = btn.dataset.icon;
                    if (preview) preview.innerHTML = btn.dataset.svg;
                    Swal.close();
                });
            });
        }
    });
}

function useCustomSvg(hiddenInputId, previewId) {
    Swal.fire({
        title: 'أدخل كود SVG مخصص',
        input: 'textarea',
        inputPlaceholder: '<svg viewBox="0 0 24 24">...</svg>',
        inputAttributes: { dir: 'ltr', style: 'font-family:monospace;font-size:12px' },
        showCancelButton: true,
        confirmButtonText: 'استخدام',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#B08D2F',
        preConfirm: function (v) {
            if (!v || !v.trim().startsWith('<svg')) {
                Swal.showValidationMessage('يجب أن يبدأ الكود بـ <svg');
                return false;
            }
            return v;
        }
    }).then(function (r) {
        if (r.isConfirmed) {
            document.getElementById(hiddenInputId).value = r.value;
            document.getElementById(previewId).innerHTML = r.value;
        }
    });
}
</script>
</body>
</html>