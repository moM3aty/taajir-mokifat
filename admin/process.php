<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $n  = trim($_POST['step_number'] ?? '');
    $t  = trim($_POST['title'] ?? '');
    $d  = trim($_POST['description'] ?? '');
    $s  = (int)($_POST['sort_order'] ?? 0);
    $a  = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare('UPDATE process_steps SET step_number=?,title=?,description=?,sort_order=?,is_active=? WHERE id=?')
            ->execute([$n, $t, $d, $s, $a, $id]);
        flash('success', 'تم التعديل');
    } else {
        $pdo->prepare('INSERT INTO process_steps (step_number,title,description,sort_order,is_active) VALUES (?,?,?,?,?)')
            ->execute([$n, $t, $d, $s, $a]);
        flash('success', 'تمت الإضافة');
    }
    header('Location: ' . filterUrl('process.php'));
    exit;
}

if (!empty($_GET['delete'])) {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_GET['csrf'] ?? '')) {
        flash('error', 'طلب غير صالح');
        header('Location: process.php');
        exit;
    }
    $pdo->prepare('DELETE FROM process_steps WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success', 'تم الحذف');
    header('Location: ' . filterUrl('process.php'));
    exit;
}

$q = trim($_GET['q'] ?? '');
$status = in_array($_GET['status'] ?? '', ['all', 'active', 'inactive'], true) ? ($_GET['status'] ?? 'all') : 'all';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(step_number LIKE ? OR title LIKE ? OR description LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($status === 'active')   $where[] = 'is_active = 1';
if ($status === 'inactive') $where[] = 'is_active = 0';
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM process_steps $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pg = buildPagination($total);

$stmt = $pdo->prepare("SELECT * FROM process_steps $whereSQL ORDER BY sort_order, id LIMIT {$pg['perPage']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$globalStats = $pdo->query("SELECT COUNT(*) total,
    COALESCE(SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END),0) active,
    COALESCE(SUM(CASE WHEN is_active=0 THEN 1 ELSE 0 END),0) inactive
    FROM process_steps")->fetch();

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM process_steps WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$pageTitle = 'خطوات العمل';
include __DIR__ . '/includes/header.php';
?>

<div class="list-stats">
    <div class="list-stat">
        <div class="list-stat-icon total"><i data-lucide="list-checks"></i></div>
        <div>
            <div class="list-stat-label">إجمالي الخطوات</div>
            <div class="list-stat-value"><?= (int)$globalStats['total'] ?></div>
        </div>
    </div>
    <div class="list-stat">
        <div class="list-stat-icon active"><i data-lucide="check-circle-2"></i></div>
        <div>
            <div class="list-stat-label">مفعّلة</div>
            <div class="list-stat-value"><?= (int)$globalStats['active'] ?></div>
        </div>
    </div>
    <div class="list-stat">
        <div class="list-stat-icon inactive"><i data-lucide="eye-off"></i></div>
        <div>
            <div class="list-stat-label">معطّلة</div>
            <div class="list-stat-value"><?= (int)$globalStats['inactive'] ?></div>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="card-title"><i data-lucide="<?= $edit ? 'pencil' : 'plus-circle' ?>" style="width:18px;height:18px"></i> <?= $edit ? 'تعديل خطوة' : 'إضافة خطوة جديدة' ?></h3>
    <form method="post">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="form-grid">
            <label>رقم الخطوة<input type="text" name="step_number" required value="<?= htmlspecialchars($edit['step_number'] ?? '') ?>" placeholder="01"></label>
            <label>ترتيب الظهور<input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></label>
            <label class="full">العنوان<input type="text" name="title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"></label>
            <label class="full">الوصف<textarea name="description" rows="3"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea></label>
            <label style="flex-direction:row;align-items:center;gap:8px"><input type="checkbox" name="is_active" <?= (!isset($edit['is_active']) || $edit['is_active']) ? 'checked' : '' ?>> مفعّلة</label>
        </div>
        <div style="margin-top:22px;display:flex;gap:10px">
            <button class="btn btn-primary" type="submit"><i data-lucide="save" style="width:16px;height:16px"></i> <?= $edit ? 'حفظ التعديلات' : 'إضافة' ?></button>
            <?php if ($edit): ?><a href="<?= filterUrl('process.php', ['edit' => null]) ?>" class="btn" style="background:#f0f0f0;color:#555">إلغاء</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card" style="padding:18px">
    <form method="get" class="list-filters">
        <div class="filter-group" style="flex:1;min-width:220px">
            <label for="fq">بحث</label>
            <input type="text" id="fq" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ابحث في الرقم أو العنوان أو الوصف...">
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
            <?php if ($q !== '' || $status !== 'all'): ?><a href="process.php" class="btn" style="background:#f0f0f0;color:#555"><i data-lucide="x" style="width:15px;height:15px"></i> مسح</a><?php endif; ?>
        </div>
    </form>
</div>

<?php if (empty($rows)): ?>
    <div class="empty-state">
        <i data-lucide="inbox"></i>
        <h3>لا توجد خطوات</h3>
        <p><?= ($q !== '' || $status !== 'all') ? 'جرّب تغيير الفلاتر أو <a href="process.php" style="color:var(--maroon);font-weight:700">مسح التصفية</a>' : 'ابدأ بإضافة أول خطوة من النموذج أعلاه' ?></p>
    </div>
<?php else: ?>

    <!-- Steps Cards -->
    <div class="cards-grid">
        <?php foreach ($rows as $r): ?>
            <div class="mini-card">
                <div class="mini-card-head">
                    <div class="mini-card-icon" style="font-family:'Cairo';font-weight:800;font-size:1rem"><?= htmlspecialchars($r['step_number']) ?></div>
                    <div style="flex:1;min-width:0">
                        <div class="mini-card-title"><?= htmlspecialchars($r['title']) ?></div>
                        <span class="mini-card-badge <?= $r['is_active'] ? 'active' : 'inactive' ?>"><?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?></span>
                    </div>
                </div>
                <div class="mini-card-desc"><?= htmlspecialchars($r['description']) ?></div>
                <div class="mini-card-actions">
                    <a href="<?= filterUrl('process.php', ['edit' => $r['id']]) ?>" class="btn btn-gold"><i data-lucide="pencil"></i> تعديل</a>
                    <a href="<?= filterUrl('process.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>" class="btn btn-danger"
                        onclick="return confirmDelete(this.href, 'حذف هذه الخطوة نهائيًا؟')"><i data-lucide="trash-2"></i> حذف</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php renderPagination('process.php', $pg); ?>

    <div class="card">
        <h3 class="card-title"><i data-lucide="list" style="width:18px;height:18px"></i> إدارة سريعة — الصفحة الحالية (<?= count($rows) ?>)</h3>
        <div class="table-wrap" style="border:0;box-shadow:none">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th style="width:70px">رقم</th>
                        <th>العنوان</th>
                        <th>الوصف</th>
                        <th style="width:80px">الترتيب</th>
                        <th style="width:90px">الحالة</th>
                        <th style="width:130px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><strong style="color:var(--maroon);font-family:'Cairo'"><?= htmlspecialchars($r['step_number']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                            <td><?= htmlspecialchars(mb_substr($r['description'], 0, 70)) ?><?= mb_strlen($r['description']) > 70 ? '...' : '' ?></td>
                            <td><?= $r['sort_order'] ?></td>
                            <td><span class="badge badge-<?= $r['is_active'] ? 'active' : 'inactive' ?>"><?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?></span></td>
                            <td class="actions">
                                <a href="<?= filterUrl('process.php', ['edit' => $r['id']]) ?>" class="btn btn-sm btn-gold"><i data-lucide="pencil" style="width:13px;height:13px"></i></a>
                                <a href="<?= filterUrl('process.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirmDelete(this.href, 'حذف هذه الخطوة نهائيًا؟')"><i data-lucide="trash-2" style="width:13px;height:13px"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>