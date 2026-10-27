<?php
// admin/intro.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $c  = trim($_POST['content'] ?? '');
    $s  = (int)($_POST['sort_order'] ?? 0);
    $a  = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare('UPDATE intro_paragraphs SET content=?,sort_order=?,is_active=? WHERE id=?')
            ->execute([$c, $s, $a, $id]);
        flash('success', 'تم تعديل الفقرة بنجاح');
    } else {
        $pdo->prepare('INSERT INTO intro_paragraphs (content,sort_order,is_active) VALUES (?,?,?)')
            ->execute([$c, $s, $a]);
        flash('success', 'تمت إضافة الفقرة بنجاح');
    }
    header('Location: ' . filterUrl('intro.php')); exit;
}

if (!empty($_GET['delete'])) {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_GET['csrf'] ?? '')) {
        flash('error', 'طلب غير صالح');
        header('Location: intro.php'); exit;
    }
    $pdo->prepare('DELETE FROM intro_paragraphs WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success', 'تم حذف الفقرة');
    header('Location: ' . filterUrl('intro.php')); exit;
}

$q      = trim($_GET['q'] ?? '');
$status = in_array($_GET['status'] ?? '', ['all', 'active', 'inactive'], true) ? ($_GET['status'] ?? 'all') : 'all';
$where = []; $params = [];
if ($q !== '') { $where[] = 'content LIKE ?'; $params[] = "%$q%"; }
if ($status === 'active')   $where[] = 'is_active = 1';
if ($status === 'inactive') $where[] = 'is_active = 0';
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM intro_paragraphs $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pg = buildPagination($total);

$stmt = $pdo->prepare("SELECT * FROM intro_paragraphs $whereSQL ORDER BY sort_order, id LIMIT {$pg['perPage']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$globalStats = $pdo->query("SELECT COUNT(*) total,
    COALESCE(SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END),0) active,
    COALESCE(SUM(CASE WHEN is_active=0 THEN 1 ELSE 0 END),0) inactive
    FROM intro_paragraphs")->fetch();

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM intro_paragraphs WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$pageTitle = 'فقرات المقدمة';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== TinyMCE custom styling ===== */
.tox-tinymce {
    border-radius: 8px !important;
    border: 1px solid rgba(107,29,35,.18) !important;
    font-family: 'Tajawal', Tahoma, sans-serif !important;
    transition: border-color .2s, box-shadow .2s;
}
.tox-tinymce:focus-within {
    border-color: var(--gold) !important;
    box-shadow: 0 0 0 3px rgba(176,141,47,.15) !important;
}
.tox .tox-toolbar-overlord,
.tox .tox-toolbar__primary,
.tox .tox-toolbar__overflow {
    background: #FAF7F2 !important;
}
.tox .tox-edit-area__iframe {
    background: #fff !important;
}
.tox .tox-statusbar {
    background: #FAF7F2 !important;
    border-top: 1px solid rgba(107,29,35,.08) !important;
    color: rgba(42,27,28,.6) !important;
    font-family: 'Tajawal', Tahoma, sans-serif !important;
}
.tox .tox-tbtn:hover {
    background: rgba(176,141,47,.12) !important;
}
.tox .tox-tbtn--enabled {
    background: var(--gold) !important;
    color: #fff !important;
}
.tox .tox-tbtn--enabled svg {
    fill: #fff !important;
}

/* Hint text under editor */
.rich-editor-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .75rem;
    color: rgba(42,27,28,.55);
    margin-top: 8px;
    font-weight: 500;
}
.rich-editor-hint svg {
    width: 14px;
    height: 14px;
    color: var(--gold);
}

/* Rendered preview in cards */
.intro-preview-content {
    font-size: .92rem;
    line-height: 1.85;
    color: rgba(42,27,28,.78);
    direction: rtl;
    text-align: right;
}
.intro-preview-content strong,
.intro-preview-content b {
    color: var(--maroon);
    font-weight: 700;
}
.intro-preview-content em,
.intro-preview-content i {
    color: rgba(42,27,28,.85);
}
.intro-preview-content a {
    color: var(--gold);
    text-decoration: underline;
    font-weight: 600;
}
.intro-preview-content a:hover {
    color: var(--maroon);
}
.intro-preview-content ul,
.intro-preview-content ol {
    padding-right: 22px;
    margin: 8px 0;
}
.intro-preview-content h1,
.intro-preview-content h2,
.intro-preview-content h3 {
    font-family: 'Cairo';
    color: var(--maroon);
    margin: 12px 0 6px;
}
.intro-preview-content blockquote {
    border-right: 3px solid var(--gold);
    padding: 6px 14px;
    margin: 10px 0;
    background: rgba(176,141,47,.06);
    border-radius: 6px;
    color: rgba(42,27,28,.7);
}
</style>

<!-- Stats -->
<div class="list-stats">
    <div class="list-stat"><div class="list-stat-icon total"><i data-lucide="align-right"></i></div><div><div class="list-stat-label">إجمالي الفقرات</div><div class="list-stat-value"><?= (int)$globalStats['total'] ?></div></div></div>
    <div class="list-stat"><div class="list-stat-icon active"><i data-lucide="check-circle-2"></i></div><div><div class="list-stat-label">مفعّلة</div><div class="list-stat-value"><?= (int)$globalStats['active'] ?></div></div></div>
    <div class="list-stat"><div class="list-stat-icon inactive"><i data-lucide="eye-off"></i></div><div><div class="list-stat-label">معطّلة</div><div class="list-stat-value"><?= (int)$globalStats['inactive'] ?></div></div></div>
</div>

<!-- Add / Edit Form -->
<div class="card">
    <h3 class="card-title">
        <i data-lucide="<?= $edit ? 'pencil' : 'plus-circle' ?>" style="width:18px;height:18px"></i>
        <?= $edit ? 'تعديل فقرة' : 'إضافة فقرة جديدة' ?>
    </h3>
    <form method="post" id="introForm">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="form-grid">
            <div class="full">
                <label style="margin-bottom:8px">محتوى الفقرة</label>
                <textarea name="content" class="rich-editor" rows="8"><?= htmlspecialchars($edit['content'] ?? '') ?></textarea>
                <div class="rich-editor-hint">
                    <i data-lucide="info"></i>
                    <span>استخدم شريط الأدوات للتنسيق — يدعم العربية RTL، عناوين، قوائم، روابط، جداول، ألوان، وكتابة HTML مباشرة</span>
                </div>
            </div>
            <label>ترتيب الظهور
                <input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>">
            </label>
            <label style="flex-direction:row;align-items:center;gap:8px">
                <input type="checkbox" name="is_active" <?= (!isset($edit['is_active']) || $edit['is_active']) ? 'checked' : '' ?>> مفعّلة
            </label>
        </div>
        <div style="margin-top:22px;display:flex;gap:10px">
            <button class="btn btn-primary" type="submit">
                <i data-lucide="save" style="width:16px;height:16px"></i>
                <?= $edit ? 'حفظ التعديلات' : 'إضافة' ?>
            </button>
            <?php if ($edit): ?>
                <a href="<?= filterUrl('intro.php', ['edit' => null]) ?>" class="btn" style="background:#f0f0f0;color:#555">إلغاء</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Filter Bar -->
<div class="card" style="padding:18px">
    <form method="get" class="list-filters">
        <div class="filter-group" style="flex:1;min-width:220px">
            <label for="fq">بحث</label>
            <input type="text" id="fq" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ابحث في المحتوى...">
        </div>
        <div class="filter-group">
            <label for="fst">الحالة</label>
            <select id="fst" name="status">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>الكل</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>مفعّلة</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>معطّلة</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="search" style="width:15px;height:15px"></i> تصفية
            </button>
            <?php if ($q !== '' || $status !== 'all'): ?>
                <a href="intro.php" class="btn" style="background:#f0f0f0;color:#555">
                    <i data-lucide="x" style="width:15px;height:15px"></i> مسح
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if (empty($rows)): ?>
    <div class="empty-state">
        <i data-lucide="inbox"></i>
        <h3>لا توجد فقرات</h3>
        <p><?= ($q !== '' || $status !== 'all') ? 'جرّب تغيير الفلاتر أو <a href="intro.php" style="color:var(--maroon);font-weight:700">مسح التصفية</a>' : 'ابدأ بإضافة أول فقرة من النموذج أعلاه' ?></p>
    </div>
<?php else: ?>

    <!-- Preview Cards -->
    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:22px">
        <?php foreach ($rows as $r): ?>
            <div class="card" style="margin-bottom:0;padding:18px">
                <div style="display:flex;justify-content:space-between;gap:14px;align-items:flex-start;flex-wrap:wrap;margin-bottom:10px">
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <span class="mini-card-badge <?= $r['is_active'] ? 'active' : 'inactive' ?>" style="align-self:auto"><?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?></span>
                        <span style="font-size:.75rem;color:rgba(42,27,28,.5)">ترتيب #<?= $r['sort_order'] ?></span>
                        <span style="font-size:.75rem;color:rgba(42,27,28,.4)">•</span>
                        <span style="font-size:.75rem;color:rgba(42,27,28,.4)">ID #<?= $r['id'] ?></span>
                    </div>
                    <div style="display:flex;gap:6px">
                        <a href="<?= filterUrl('intro.php', ['edit' => $r['id']]) ?>" class="btn btn-sm btn-gold">
                            <i data-lucide="pencil" style="width:13px;height:13px"></i> تعديل
                        </a>
                        <a href="<?= filterUrl('intro.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirmDelete(this.href, 'حذف هذه الفقرة نهائيًا؟')">
                            <i data-lucide="trash-2" style="width:13px;height:13px"></i> حذف
                        </a>
                    </div>
                </div>
                <div class="intro-preview-content">
                    <?= $r['content'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php renderPagination('intro.php', $pg); ?>

    <!-- Management Table -->
    <div class="card">
        <h3 class="card-title">
            <i data-lucide="list" style="width:18px;height:18px"></i>
            إدارة سريعة — الصفحة الحالية (<?= count($rows) ?>)
        </h3>
        <div class="table-wrap" style="border:0;box-shadow:none">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>المحتوى</th>
                        <th style="width:80px">الترتيب</th>
                        <th style="width:90px">الحالة</th>
                        <th style="width:130px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars(mb_substr(strip_tags($r['content']), 0, 90)) ?><?= mb_strlen(strip_tags($r['content'])) > 90 ? '...' : '' ?></td>
                        <td><?= $r['sort_order'] ?></td>
                        <td>
                            <span class="badge badge-<?= $r['is_active'] ? 'active' : 'inactive' ?>">
                                <?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= filterUrl('intro.php', ['edit' => $r['id']]) ?>" class="btn btn-sm btn-gold">
                                <i data-lucide="pencil" style="width:13px;height:13px"></i>
                            </a>
                            <a href="<?= filterUrl('intro.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirmDelete(this.href, 'حذف هذه الفقرة نهائيًا؟')">
                                <i data-lucide="trash-2" style="width:13px;height:13px"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<!-- TinyMCE 5 — Rich Text Editor (free, RTL-ready) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@5.10.9/tinymce.min.js"></script>
<script>
(function () {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE failed to load');
        return;
    }
    tinymce.init({
        selector: 'textarea.rich-editor',
        directionality: 'rtl',
        height: 400,
        menubar: false,
        branding: false,
        promotion: false,
        resize: true,
        statusbar: true,
        plugins: 'lists link table code fullscreen autoresize',
        toolbar: [
            'undo redo | styleselect | bold italic underline strikethrough',
            'forecolor backcolor removeformat | alignright aligncenter alignleft alignjustify',
            'bullist numlist outdent indent | link table',
            'code fullscreen'
        ],
        style_formats: [
            { title: 'عنوان رئيسي', block: 'h2' },
            { title: 'عنوان فرعي', block: 'h3' },
            { title: 'نص عادي', block: 'p' },
            { title: 'اقتباس', block: 'blockquote' }
        ],
        content_style: 'body { font-family: "Tajawal", Tahoma, sans-serif; font-size: 15px; line-height: 1.85; direction: rtl; color: #2A1B1C; padding: 10px 14px; }' +
                       'body strong, body b { color: #6B1D23; font-weight: 700; }' +
                       'body a { color: #B08D2F; text-decoration: underline; font-weight: 600; }' +
                       'body h2, body h3 { font-family: "Cairo", Tahoma, sans-serif; color: #6B1D23; margin: 14px 0 8px; }' +
                       'body blockquote { border-right: 3px solid #B08D2F; padding: 6px 14px; margin: 10px 0; background: rgba(176,141,47,.06); border-radius: 6px; color: #4A1317; }' +
                       'body ul, body ol { padding-right: 22px; }',
        setup: function (editor) {
            editor.on('change keyup undo redo', function () {
                editor.save();
            });
        },
        // Sync to textarea right before form submit (safety net)
        init_instance_callback: function (editor) {
            const form = document.getElementById('introForm');
            if (form) {
                form.addEventListener('submit', function () {
                    tinymce.triggerSave();
                });
            }
        }
    });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>