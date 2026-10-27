<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $n  = trim($_POST['name'] ?? '');
    $s  = (int)($_POST['sort_order'] ?? 0);
    $a  = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare('UPDATE cities SET name=?,sort_order=?,is_active=? WHERE id=?')->execute([$n, $s, $a, $id]);
        flash('success', 'تم التعديل');
    } else {
        $pdo->prepare('INSERT INTO cities (name,sort_order,is_active) VALUES (?,?,?)')->execute([$n, $s, $a]);
        flash('success', 'تمت الإضافة');
    }
    header('Location: ' . filterUrl('cities.php')); exit;
}

if (!empty($_GET['delete'])) {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_GET['csrf'] ?? '')) { flash('error', 'طلب غير صالح'); header('Location: cities.php'); exit; }
    $pdo->prepare('DELETE FROM cities WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success', 'تم الحذف');
    header('Location: ' . filterUrl('cities.php')); exit;
}

$q = trim($_GET['q'] ?? '');
$status = in_array($_GET['status'] ?? '', ['all', 'active', 'inactive'], true) ? ($_GET['status'] ?? 'all') : 'all';
$where = []; $params = [];
if ($q !== '') { $where[] = 'name LIKE ?'; $params[] = "%$q%"; }
if ($status === 'active')   $where[] = 'is_active = 1';
if ($status === 'inactive') $where[] = 'is_active = 0';
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM cities $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pg = buildPagination($total);

$stmt = $pdo->prepare("SELECT * FROM cities $whereSQL ORDER BY sort_order, id LIMIT {$pg['perPage']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$globalStats = $pdo->query("SELECT COUNT(*) total,
    COALESCE(SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END),0) active,
    COALESCE(SUM(CASE WHEN is_active=0 THEN 1 ELSE 0 END),0) inactive
    FROM cities")->fetch();

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM cities WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$pageTitle = 'المدن';
include __DIR__ . '/includes/header.php';
?>

<div class="list-stats">
    <div class="list-stat"><div class="list-stat-icon total"><i data-lucide="map-pin"></i></div><div><div class="list-stat-label">إجمالي المدن</div><div class="list-stat-value"><?= (int)$globalStats['total'] ?></div></div></div>
    <div class="list-stat"><div class="list-stat-icon active"><i data-lucide="check-circle-2"></i></div><div><div class="list-stat-label">مفعّلة</div><div class="list-stat-value"><?= (int)$globalStats['active'] ?></div></div></div>
    <div class="list-stat"><div class="list-stat-icon inactive"><i data-lucide="eye-off"></i></div><div><div class="list-stat-label">معطّلة</div><div class="list-stat-value"><?= (int)$globalStats['inactive'] ?></div></div></div>
</div>

<div class="card">
    <h3 class="card-title"><i data-lucide="<?= $edit ? 'pencil' : 'plus-circle' ?>" style="width:18px;height:18px"></i> <?= $edit ? 'تعديل مدينة' : 'إضافة مدينة جديدة' ?></h3>
    <form method="post">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="form-grid">
            <label>اسم المدينة<input type="text" name="name" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></label>
            <label>ترتيب الظهور<input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></label>
            <label style="flex-direction:row;align-items:center;gap:8px"><input type="checkbox" name="is_active" <?= (!isset($edit['is_active']) || $edit['is_active']) ? 'checked' : '' ?>> مفعّلة</label>
        </div>
        <div style="margin-top:22px;display:flex;gap:10px">
            <button class="btn btn-primary" type="submit"><i data-lucide="save" style="width:16px;height:16px"></i> <?= $edit ? 'حفظ التعديلات' : 'إضافة' ?></button>
            <?php if ($edit): ?><a href="<?= filterUrl('cities.php', ['edit' => null]) ?>" class="btn" style="background:#f0f0f0;color:#555">إلغاء</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card" style="padding:18px">
    <form method="get" class="list-filters">
        <div class="filter-group" style="flex:1;min-width:220px">
            <label for="fq">بحث</label>
            <input type="text" id="fq" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ابحث باسم المدينة...">
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
            <?php if ($q !== '' || $status !== 'all'): ?><a href="cities.php" class="btn" style="background:#f0f0f0;color:#555"><i data-lucide="x" style="width:15px;height:15px"></i> مسح</a><?php endif; ?>
        </div>
    </form>
</div>

<?php if (empty($rows)): ?>
    <div class="empty-state">
        <i data-lucide="inbox"></i>
        <h3>لا توجد مدن</h3>
        <p><?= ($q !== '' || $status !== 'all') ? 'جرّب تغيير الفلاتر أو <a href="cities.php" style="color:var(--maroon);font-weight:700">مسح التصفية</a>' : 'ابدأ بإضافة أول مدينة من النموذج أعلاه' ?></p>
    </div>
<?php else: ?>

    <!-- City Chips (10 items) -->
    <div class="card">
        <h3 class="card-title"><i data-lucide="layout-grid" style="width:18px;height:18px"></i> المدن — الصفحة الحالية (<?= count($rows) ?>)</h3>
        <div style="display:flex;flex-wrap:wrap;gap:8px">
            <?php foreach ($rows as $r): ?>
                <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:<?= $r['is_active'] ? 'rgba(176,141,47,.1)' : 'rgba(42,27,28,.05)' ?>;border:1px solid <?= $r['is_active'] ? 'rgba(176,141,47,.3)' : 'rgba(42,27,28,.15)' ?>;border-radius:999px;font-size:.85rem;font-weight:600;color:<?= $r['is_active'] ? 'var(--maroon)' : 'rgba(42,27,28,.5)' ?>">
                    <i data-lucide="map-pin" style="width:14px;height:14px"></i>
                    <span><?= htmlspecialchars($r['name']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php renderPagination('cities.php', $pg); ?>

    <!-- Table -->
    <div class="card">
        <h3 class="card-title"><i data-lucide="list" style="width:18px;height:18px"></i> إدارة المدن — الصفحة الحالية (<?= count($rows) ?>)</h3>
        <div class="table-wrap" style="border:0;box-shadow:none">
            <table>
                <thead><tr>
                    <th style="width:50px">#</th>
                    <th>اسم المدينة</th>
                    <th style="width:80px">الترتيب</th>
                    <th style="width:90px">الحالة</th>
                    <th style="width:130px">إجراءات</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><span class="badge badge-<?= $r['is_active'] ? 'active' : 'inactive' ?>"><?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?></span></td>
                        <td class="actions">
                            <a href="<?= filterUrl('cities.php', ['edit' => $r['id']]) ?>" class="btn btn-sm btn-gold"><i data-lucide="pencil" style="width:13px;height:13px"></i></a>
                            <a href="<?= filterUrl('cities.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>" class="btn btn-sm btn-danger"
                               onclick="return confirmDelete(this.href, 'حذف هذه المدينة نهائيًا؟')"><i data-lucide="trash-2" style="width:13px;height:13px"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>