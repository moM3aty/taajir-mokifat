<?php
// admin/settings.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// ============ Field definitions grouped by tabs ============
$fields = [
    // ---- Identity ----
    'site_name'          => ['label' => 'اسم الموقع', 'type' => 'text', 'tab' => 'identity'],
    'site_title'         => ['label' => 'عنوان الصفحة (Title)', 'type' => 'text', 'tab' => 'identity', 'counter' => 60, 'help' => 'يظهر في تبويب المتصفح ونتائج جوجل — يُفضّل أقل من 60 حرفًا'],
    'site_description'   => ['label' => 'وصف الموقع (Meta Description)', 'type' => 'textarea', 'tab' => 'identity', 'counter' => 160, 'help' => 'يظهر في نتائج البحث — يُفضّل 150–160 حرفًا'],
    'site_keywords'      => ['label' => 'الكلمات المفتاحية (Meta Keywords)', 'type' => 'textarea', 'tab' => 'identity', 'help' => 'افصل بين كل كلمة والأخرى بفاصلة (, )'],

    // ---- SEO ----
    'og_title'           => ['label' => 'OG Title', 'type' => 'text', 'tab' => 'seo', 'counter' => 60, 'help' => 'العنوان الذي يظهر عند مشاركة الموقع على فيسبوك/تويتر'],
    'og_description'     => ['label' => 'OG Description', 'type' => 'textarea', 'tab' => 'seo', 'counter' => 200, 'help' => 'الوصف الذي يظهر عند مشاركة الموقع'],
    'schema_description' => ['label' => 'وصف Schema (لمحركات البحث)', 'type' => 'textarea', 'tab' => 'seo', 'help' => 'يُستخدم في بيانات Schema.org لتحسين الفهم من جوجل'],

    // ---- Contact ----
    'phone_display'      => ['label' => 'رقم الهاتف (للعرض)', 'type' => 'text', 'tab' => 'contact', 'dir' => 'ltr', 'help' => 'الصيغة الظاهرة للزائر، مثال: 0503 513 824'],
    'phone_tel'          => ['label' => 'رقم الاتصال (tel:)', 'type' => 'text', 'tab' => 'contact', 'dir' => 'ltr', 'help' => 'يُستخدم في روابط الاتصال، يبدأ بـ +966، مثال: +966503513824'],
    'whatsapp'           => ['label' => 'رقم واتساب', 'type' => 'text', 'tab' => 'contact', 'dir' => 'ltr', 'help' => 'رقم بصيغة دولية بدون + أو مسافات، مثال: 966503513824'],

    // ---- Hero ----
    'hero_badge'         => ['label' => 'شارة الهيرو (فوق العنوان)', 'type' => 'text', 'tab' => 'hero'],
    'hero_line1'         => ['label' => 'السطر الأول من العنوان', 'type' => 'text', 'tab' => 'hero'],
    'hero_line2'         => ['label' => 'السطر الثاني من العنوان', 'type' => 'text', 'tab' => 'hero', 'help' => 'يمكن استخدام &lt;em&gt;لكلمة مميزة&lt;/em&gt; لتلوينها ذهبيًا'],
    'hero_sub'           => ['label' => 'وصف الهيرو', 'type' => 'textarea', 'tab' => 'hero', 'help' => 'يظهر تحت العنوان الرئيسي — يمكن استخدام Enter لسطر جديد'],

    // ---- Coverage ----
    'coverage_title'     => ['label' => 'عنوان نطاق التغطية', 'type' => 'text', 'tab' => 'coverage'],
    'coverage_sub'       => ['label' => 'وصف نطاق التغطية', 'type' => 'textarea', 'tab' => 'coverage'],

    // ---- CTA ----
    'cta_title'          => ['label' => 'عنوان دعوة الإجراء', 'type' => 'text', 'tab' => 'cta'],
    'cta_text'           => ['label' => 'وصف دعوة الإجراء', 'type' => 'textarea', 'tab' => 'cta'],
    'contact_note'       => ['label' => 'ملاحظة قسم التواصل', 'type' => 'textarea', 'tab' => 'cta'],

    // ---- Footer ----
    'footer_about'       => ['label' => 'نبذة الفوتر', 'type' => 'textarea', 'tab' => 'footer'],
    'footer_copyright'   => ['label' => 'سنة الحقوق', 'type' => 'text', 'tab' => 'footer', 'help' => 'مثال: 2026'],
];

$tabs = [
    'identity' => ['label' => 'الهوية والعلامة', 'icon' => 'fingerprint'],
    'seo'      => ['label' => 'SEO والمشاركة',   'icon' => 'search'],
    'contact'  => ['label' => 'الاتصال',          'icon' => 'phone'],
    'hero'     => ['label' => 'الواجهة (Hero)',  'icon' => 'layout-template'],
    'coverage' => ['label' => 'نطاق التغطية',    'icon' => 'map'],
    'cta'      => ['label' => 'دعوة الإجراء',    'icon' => 'megaphone'],
    'footer'   => ['label' => 'الفوتر',           'icon' => 'align-bottom'],
];

// ============ Save ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    foreach ($fields as $k => $meta) {
        $val = $_POST[$k] ?? '';
        $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?,?)
                               ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        $stmt->execute([$k, $val]);
    }
    flash('success', 'تم حفظ جميع الإعدادات بنجاح');
    header('Location: settings.php');
    exit;
}

$current = getAllSettings($pdo);
$pageTitle = 'الإعدادات العامة';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== Settings Page ===== */
.settings-tabs {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding: 6px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid rgba(107,29,35,.08);
    margin-bottom: 22px;
    scrollbar-width: thin;
    scrollbar-color: rgba(107,29,35,.15) transparent;
}
.settings-tabs::-webkit-scrollbar { height: 4px; }
.settings-tabs::-webkit-scrollbar-thumb { background: rgba(107,29,35,.15); border-radius: 2px; }

.settings-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    background: transparent;
    color: rgba(42,27,28,.65);
    border: 0;
    font-family: inherit;
    font-size: .85rem;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background .2s, color .2s;
    flex-shrink: 0;
}
.settings-tab svg { width: 16px; height: 16px; }
.settings-tab:hover { background: rgba(176,141,47,.08); color: var(--maroon); }
.settings-tab.active {
    background: var(--maroon);
    color: #fff;
    box-shadow: 0 4px 12px -4px rgba(107,29,35,.4);
}
.settings-tab.active svg { stroke: #fff; }

.settings-panel { display: none; }
.settings-panel.active { display: block; animation: fadeInPanel .3s ease; }
@keyframes fadeInPanel {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.field-help {
    font-size: .72rem;
    color: rgba(42,27,28,.5);
    font-weight: 500;
    margin-top: 5px;
    line-height: 1.5;
}
.char-counter {
    font-size: .72rem;
    color: rgba(42,27,28,.5);
    font-weight: 500;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.char-counter .current {
    font-weight: 800;
    color: var(--maroon);
    font-family: 'Cairo';
}
.char-counter.over .current { color: var(--red); }
.char-counter.over { color: var(--red); }

/* ===== Floating save bar ===== */
.settings-panels { padding-bottom: 100px; }

.settings-save-bar {
    position: fixed;
    bottom: 24px;
    left: calc(50% - 130px);
    transform: translateX(-50%);
    z-index: 90;
    background: linear-gradient(135deg, var(--maroon), var(--maroon-deep));
    color: #fff;
    border-radius: 14px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 20px 50px -15px rgba(107,29,35,.6);
    border: 1px solid rgba(212,181,92,.25);
    max-width: calc(100% - 32px);
    flex-wrap: wrap;
    justify-content: space-between;
    min-width: 340px;
}
.save-bar-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .82rem;
    color: rgba(255,255,255,.75);
    font-weight: 600;
}
.save-bar-status svg { width: 16px; height: 16px; color: var(--gold-light); }
.save-bar-status.dirty { color: #FFD54F; }
.save-bar-status.dirty svg { color: #FFD54F; }
.save-bar-actions { display: flex; gap: 8px; align-items: center; }

.save-bar-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 10px;
    background: var(--gold);
    color: #fff;
    border: 0;
    font-family: inherit;
    font-size: .85rem;
    font-weight: 800;
    cursor: pointer;
    transition: background .2s, color .2s, transform .15s;
}
.save-bar-btn:hover {
    background: var(--gold-light);
    color: var(--ink);
    transform: translateY(-1px);
}
.save-bar-btn svg { width: 15px; height: 15px; }

.save-bar-cancel {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: .8rem;
    color: rgba(255,255,255,.65);
    text-decoration: none;
    transition: background .2s, color .2s;
}
.save-bar-cancel:hover { background: rgba(255,255,255,.1); color: #fff; }
.save-bar-cancel svg { width: 13px; height: 13px; }

@media (max-width: 1024px) {
    .settings-save-bar { left: 50%; }
}
@media (max-width: 560px) {
    .settings-save-bar { min-width: 0; width: calc(100% - 24px); bottom: 12px; padding: 10px 12px; }
    .save-bar-status { font-size: .72rem; }
    .save-bar-btn { padding: 8px 12px; font-size: .78rem; }
}
</style>

<form id="settingsForm" method="post">
    <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">

    <!-- Tabs -->
    <div class="settings-tabs" role="tablist">
        <?php foreach ($tabs as $k => $t): ?>
            <button type="button" class="settings-tab" data-tab="<?= $k ?>" role="tab">
                <i data-lucide="<?= $t['icon'] ?>"></i>
                <span><?= htmlspecialchars($t['label']) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Panels -->
    <div class="settings-panels">
        <?php foreach ($tabs as $tabKey => $tab): ?>
            <div class="settings-panel" data-tab="<?= $tabKey ?>">
                <div class="card">
                    <h3 class="card-title">
                        <i data-lucide="<?= $tab['icon'] ?>" style="width:18px;height:18px"></i>
                        <?= htmlspecialchars($tab['label']) ?>
                    </h3>
                    <div class="form-grid">
                        <?php foreach ($fields as $key => $meta): ?>
                            <?php if ($meta['tab'] !== $tabKey) continue; ?>
                            <label class="<?= $meta['type'] === 'textarea' ? 'full' : '' ?>">
                                <?= htmlspecialchars($meta['label']) ?>

                                <?php if ($meta['type'] === 'textarea'): ?>
                                    <textarea name="<?= $key ?>" rows="3" <?= !empty($meta['dir']) ? 'dir="' . $meta['dir'] . '"' : '' ?>><?= htmlspecialchars($current[$key] ?? '') ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($current[$key] ?? '') ?>" <?= !empty($meta['dir']) ? 'dir="' . $meta['dir'] . '"' : '' ?>>
                                <?php endif; ?>

                                <?php if (!empty($meta['help'])): ?>
                                    <small class="field-help"><?= $meta['help'] ?></small>
                                <?php endif; ?>

                                <?php if (!empty($meta['counter'])): ?>
                                    <small class="char-counter" data-max="<?= (int)$meta['counter'] ?>">
                                        <span class="current"><?= mb_strlen($current[$key] ?? '') ?></span> / <?= (int)$meta['counter'] ?> حرف
                                    </small>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</form>

<!-- Floating save bar -->
<div class="settings-save-bar" id="saveBar">
    <div class="save-bar-status" id="saveBarStatus">
        <i data-lucide="check-circle-2"></i>
        <span>كل التغييرات محفوظة</span>
    </div>
    <div class="save-bar-actions">
        <a href="settings.php" class="save-bar-cancel" onclick="return confirmDiscard()">
            <i data-lucide="rotate-ccw"></i>
            إلغاء
        </a>
        <button type="submit" form="settingsForm" class="save-bar-btn">
            <i data-lucide="save"></i>
            حفظ التغييرات
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs   = document.querySelectorAll('.settings-tab');
    const panels = document.querySelectorAll('.settings-panel');
    const form   = document.getElementById('settingsForm');
    const saveBar = document.getElementById('saveBar');
    const status = document.getElementById('saveBarStatus');
    const statusText = status.querySelector('span');

    // ===== Tab switching =====
    function activateTab(key) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === key));
        panels.forEach(p => p.classList.toggle('active', p.dataset.tab === key));
        try { history.replaceState(null, '', '#' + key); } catch (e) {}
        if (window.lucide) lucide.createIcons();
    }
    tabs.forEach(tab => tab.addEventListener('click', () => activateTab(tab.dataset.tab)));

    const hash = location.hash.slice(1);
    if (hash && document.querySelector('.settings-tab[data-tab="' + hash + '"]')) {
        activateTab(hash);
    } else if (tabs.length) {
        activateTab(tabs[0].dataset.tab);
    }

    // ===== Unsaved changes detection =====
    function isDirty() {
        const inputs = form.querySelectorAll('input:not([type=hidden]), textarea');
        for (const i of inputs) {
            if (i.value !== i.defaultValue) return true;
        }
        return false;
    }

    function updateSaveStatus() {
        if (isDirty()) {
            saveBar.classList.add('dirty');
            statusText.textContent = 'لديك تغييرات غير محفوظة';
        } else {
            saveBar.classList.remove('dirty');
            statusText.textContent = 'كل التغييرات محفوظة';
        }
    }

    form.addEventListener('input', updateSaveStatus);
    form.addEventListener('change', updateSaveStatus);

    window.confirmDiscard = function () {
        if (!isDirty()) return true;
        return confirm('لديك تغييرات غير محفوظة. هل تريد تجاهلها والعودة؟');
    };

    // Warn on leaving with unsaved changes
    window.addEventListener('beforeunload', function (e) {
        if (isDirty()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Remove the warning on form submit
    form.addEventListener('submit', function () {
        window.onbeforeunload = null;
    });

    // ===== Character counters =====
    document.querySelectorAll('.char-counter').forEach(counter => {
        const max = parseInt(counter.dataset.max, 10);
        const label = counter.parentElement;
        const field = label.querySelector('input, textarea');
        if (!field || !max) return;
        const current = counter.querySelector('.current');

        function update() {
            const len = field.value.length;
            current.textContent = len;
            counter.classList.toggle('over', len > max);
        }
        field.addEventListener('input', update);
        update();
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>