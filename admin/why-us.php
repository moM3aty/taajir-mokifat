<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/icons.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id     = (int)($_POST['id'] ?? 0);
    $title  = trim($_POST['title'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $icon   = $_POST['icon_svg'] ?? 'sparkles';
    $sort   = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare('UPDATE why_us SET icon_svg=?,title=?,description=?,sort_order=?,is_active=? WHERE id=?')
            ->execute([$icon, $title, $desc, $sort, $active, $id]);
        flash('success', 'تم تعديل الميزة بنجاح');
    } else {
        $pdo->prepare('INSERT INTO why_us (icon_svg,title,description,sort_order,is_active) VALUES (?,?,?,?,?)')
            ->execute([$icon, $title, $desc, $sort, $active]);
        flash('success', 'تمت إضافة الميزة بنجاح');
    }
    header('Location: ' . filterUrl('why-us.php')); exit;
}

if (!empty($_GET['delete'])) {
    $csrf = $_GET['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
        flash('error', 'طلب غير صالح');
        header('Location: why-us.php'); exit;
    }
    $pdo->prepare('DELETE FROM why_us WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success', 'تم حذف الميزة');
    header('Location: ' . filterUrl('why-us.php')); exit;
}

$q      = trim($_GET['q'] ?? '');
$status = in_array($_GET['status'] ?? '', ['all', 'active', 'inactive'], true) ? ($_GET['status'] ?? 'all') : 'all';

$where = []; $params = [];
if ($q !== '') { $where[] = '(title LIKE ? OR description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($status === 'active')   $where[] = 'is_active = 1';
if ($status === 'inactive') $where[] = 'is_active = 0';
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM why_us $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pg = buildPagination($total);

$stmt = $pdo->prepare("SELECT * FROM why_us $whereSQL ORDER BY sort_order, id LIMIT {$pg['perPage']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$globalStats = $pdo->query("SELECT COUNT(*) total,
    COALESCE(SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END),0) active,
    COALESCE(SUM(CASE WHEN is_active=0 THEN 1 ELSE 0 END),0) inactive
    FROM why_us")->fetch();

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM why_us WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$pageTitle = 'مميزاتنا';
include __DIR__ . '/includes/header.php';
?>

<div class="list-stats">
    <div class="list-stat">
        <div class="list-stat-icon total"><i data-lucide="shield-check"></i></div>
        <div><div class="list-stat-label">إجمالي المميزات</div><div class="list-stat-value"><?= (int)$globalStats['total'] ?></div></div>
    </div>
    <div class="list-stat">
        <div class="list-stat-icon active"><i data-lucide="check-circle-2"></i></div>
        <div><div class="list-stat-label">مفعّلة</div><div class="list-stat-value"><?= (int)$globalStats['active'] ?></div></div>
    </div>
    <div class="list-stat">
        <div class="list-stat-icon inactive"><i data-lucide="eye-off"></i></div>
        <div><div class="list-stat-label">معطّلة</div><div class="list-stat-value"><?= (int)$globalStats['inactive'] ?></div></div>
    </div>
</div>

<div class="card">
    <h3 class="card-title">
        <i data-lucide="<?= $edit ? 'pencil' : 'plus-circle' ?>" style="width:18px;height:18px"></i>
        <?= $edit ? 'تعديل ميزة' : 'إضافة ميزة جديدة' ?>
    </h3>
    <form method="post">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="form-grid">
            <label>عنوان الميزة<input type="text" name="title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"></label>
            <label>ترتيب الظهور<input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></label>
            <label class="full">الوصف<textarea name="description" rows="3" required><?= htmlspecialchars($edit['description'] ?? '') ?></textarea></label>
            <div class="full">
                <label style="margin-bottom:8px">أيقونة الميزة</label>
                <div class="icon-field">
                    <div class="icon-field-preview" id="iconPreview"><?= renderIcon($edit['icon_svg'] ?? 'sparkles', 28, 2) ?></div>
                    <div class="icon-field-actions">
                        <button type="button" class="btn btn-gold btn-sm" onclick="openIconPicker('iconInput','iconPreview')">
                            <i data-lucide="layout-grid" style="width:14px;height:14px"></i> اختر أيقونة
                        </button>
                        <button type="button" class="btn btn-sm" style="background:#f0f0f0;color:#333" onclick="useCustomSvg('iconInput','iconPreview')">
                            <i data-lucide="code" style="width:14px;height:14px"></i> SVG مخصص
                        </button>
                    </div>
                </div>
                <input type="hidden" name="icon_svg" id="iconInput" value="<?= htmlspecialchars($edit['icon_svg'] ?? 'sparkles') ?>">
            </div>
            <label style="flex-direction:row;align-items:center;gap:8px">
                <input type="checkbox" name="is_active" <?= (!isset($edit['is_active']) || $edit['is_active']) ? 'checked' : '' ?>> مفعّلة
            </label>
        </div>
        <div style="margin-top:22px;display:flex;gap:10px">
            <button class="btn btn-primary" type="submit"><i data-lucide="save" style="width:16px;height:16px"></i> <?= $edit ? 'حفظ التعديلات' : 'إضافة' ?></button>
            <?php if ($edit): ?><a href="<?= filterUrl('why-us.php', ['edit' => null]) ?>" class="btn" style="background:#f0f0f0;color:#555">إلغاء</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card" style="padding:18px">
    <form method="get" class="list-filters">
        <div class="filter-group" style="flex:1;min-width:220px">
            <label for="fq">بحث</label>
            <input type="text" id="fq" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ابحث في العنوان أو الوصف...">
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
            <button type="submit" class="btn btn-primary"><i data-lucide="search" style="width:15px;height:15px"></i> تصفية</button>
            <?php if ($q !== '' || $status !== 'all'): ?>
                <a href="why-us.php" class="btn" style="background:#f0f0f0;color:#555"><i data-lucide="x" style="width:15px;height:15px"></i> مسح</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if (empty($rows)): ?>
    <div class="empty-state">
        <i data-lucide="inbox"></i>
        <h3>لا توجد مميزات</h3>
        <p><?= ($q !== '' || $status !== 'all') ? 'جرّب تغيير الفلاتر أو <a href="why-us.php" style="color:var(--maroon);font-weight:700">مسح التصفية</a>' : 'ابدأ بإضافة أول ميزة من النموذج أعلاه' ?></p>
    </div>
<?php else: ?>

    <div class="cards-grid">
        <?php foreach ($rows as $r): ?>
            <div class="mini-card">
                <div class="mini-card-head">
                    <div class="mini-card-icon"><?= renderIcon($r['icon_svg'] ?: 'sparkles', 22, 2) ?></div>
                    <div style="flex:1;min-width:0">
                        <div class="mini-card-title"><?= htmlspecialchars($r['title']) ?></div>
                        <span class="mini-card-badge <?= $r['is_active'] ? 'active' : 'inactive' ?>"><?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?></span>
                    </div>
                </div>
                <div class="mini-card-desc"><?= htmlspecialchars($r['description']) ?></div>
                <div class="mini-card-actions">
                    <a href="<?= filterUrl('why-us.php', ['edit' => $r['id']]) ?>" class="btn btn-gold"><i data-lucide="pencil"></i> تعديل</a>
                    <a href="<?= filterUrl('why-us.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>" class="btn btn-danger"
                       onclick="return confirmDelete(this.href, 'حذف هذه الميزة نهائيًا؟')"><i data-lucide="trash-2"></i> حذف</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php renderPagination('why-us.php', $pg); ?>

    <div class="card">
        <h3 class="card-title"><i data-lucide="list" style="width:18px;height:18px"></i> إدارة سريعة — الصفحة الحالية (<?= count($rows) ?>)</h3>
        <div class="table-wrap" style="border:0;box-shadow:none">
            <table>
                <thead><tr>
                    <th style="width:50px">#</th>
                    <th style="width:70px">أيقونة</th>
                    <th>العنوان</th>
                    <th>الوصف</th>
                    <th style="width:80px">الترتيب</th>
                    <th style="width:90px">الحالة</th>
                    <th style="width:130px">إجراءات</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><div style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:rgba(176,141,47,.1);border-radius:8px;color:var(--gold)"><?= renderIcon($r['icon_svg'] ?: 'sparkles', 18, 2) ?></div></td>
                        <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                        <td><?= htmlspecialchars(mb_substr($r['description'], 0, 70)) ?><?= mb_strlen($r['description']) > 70 ? '...' : '' ?></td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><span class="badge badge-<?= $r['is_active'] ? 'active' : 'inactive' ?>"><?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?></span></td>
                        <td class="actions">
                            <a href="<?= filterUrl('why-us.php', ['edit' => $r['id']]) ?>" class="btn btn-sm btn-gold"><i data-lucide="pencil" style="width:13px;height:13px"></i></a>
                            <a href="<?= filterUrl('why-us.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>" class="btn btn-sm btn-danger"
                               onclick="return confirmDelete(this.href, 'حذف هذه الميزة نهائيًا؟')"><i data-lucide="trash-2" style="width:13px;height:13px"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>