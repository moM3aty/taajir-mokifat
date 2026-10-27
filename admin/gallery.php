<?php
// admin/gallery.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// ============ Setup ============
$uploadDir = __DIR__ . '/../uploads/gallery/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// ============ Add / Edit (POST) ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    $id        = (int)($_POST['id'] ?? 0);
    $alt       = trim($_POST['alt_text'] ?? '');
    $sort      = (int)($_POST['sort_order'] ?? 0);
    $active    = isset($_POST['is_active']) ? 1 : 0;
    $imagePath = $_POST['current_image'] ?? '';

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            flash('error', 'نوع الملف غير مسموح (jpg, png, webp, gif فقط)');
            header('Location: gallery.php');
            exit;
        }
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            flash('error', 'حجم الصورة كبير جدًا (الحد الأقصى 5MB)');
            header('Location: gallery.php');
            exit;
        }

        $filename = 'img_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            if ($id && $imagePath && strpos($imagePath, 'uploads/gallery/') === 0) {
                @unlink(__DIR__ . '/../' . $imagePath);
            }
            $imagePath = 'uploads/gallery/' . $filename;
        } else {
            flash('error', 'فشل رفع الصورة');
            header('Location: gallery.php');
            exit;
        }
    }

    if (!$imagePath) {
        flash('error', 'يجب اختيار صورة');
        header('Location: gallery.php');
        exit;
    }

    if ($id) {
        $pdo->prepare('UPDATE gallery SET image_path=?, alt_text=?, sort_order=?, is_active=? WHERE id=?')
            ->execute([$imagePath, $alt, $sort, $active, $id]);
        flash('success', 'تم تعديل الصورة بنجاح');
    } else {
        $pdo->prepare('INSERT INTO gallery (image_path, alt_text, sort_order, is_active) VALUES (?,?,?,?)')
            ->execute([$imagePath, $alt, $sort, $active]);
        flash('success', 'تمت إضافة الصورة بنجاح');
    }

    // نحذف edit من الـ URL بس، ونحتفظ بـ q/status/page
    $redirect = filterUrl('gallery.php', ['edit' => null]);
    header('Location: ' . $redirect);
    exit;
}

// ============ Delete (GET + CSRF) ============
if (!empty($_GET['delete'])) {
    $csrf = $_GET['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
        flash('error', 'طلب غير صالح');
        header('Location: gallery.php');
        exit;
    }

    $id   = (int)$_GET['delete'];
    $stmt = $pdo->prepare('SELECT image_path FROM gallery WHERE id=?');
    $stmt->execute([$id]);
    $row  = $stmt->fetch();

    if ($row && strpos($row['image_path'], 'uploads/gallery/') === 0) {
        @unlink(__DIR__ . '/../' . $row['image_path']);
    }
    $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    flash('success', 'تم حذف الصورة بنجاح');

    header('Location: ' . filterUrl('gallery.php'));
    exit;
}

// ============ Filters + Pagination ============
$q      = trim($_GET['q'] ?? '');
$status = in_array($_GET['status'] ?? '', ['all', 'active', 'inactive'], true)
    ? ($_GET['status'] ?? 'all') : 'all';

$where  = [];
$params = [];

if ($q !== '') {
    $where[]  = '(alt_text LIKE ? OR image_path LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($status === 'active')   $where[] = 'is_active = 1';
if ($status === 'inactive') $where[] = 'is_active = 0';
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM gallery $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

// Helpers من functions.php
$pg         = buildPagination($total);
$page       = $pg['page'];
$totalPages = $pg['totalPages'];
$offset     = $pg['offset'];
$perPage    = $pg['perPage'];

$stmt = $pdo->prepare("SELECT * FROM gallery $whereSQL ORDER BY sort_order, id LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Global stats
$globalStats = $pdo->query("SELECT
    COUNT(*) AS total,
    COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) AS active,
    COALESCE(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) AS inactive
FROM gallery")->fetch();

// Edit data
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$pageTitle = 'معرض الصور';
include __DIR__ . '/includes/header.php';
?>

<style>
    /* ===== Gallery-specific styles ===== */
    .gallery-admin {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .gallery-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(107, 29, 35, .08);
        box-shadow: 0 4px 14px -8px rgba(42, 27, 28, .15);
        transition: transform .2s, box-shadow .2s, border-color .2s;
        display: flex;
        flex-direction: column;
    }

    .gallery-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -12px rgba(42, 27, 28, .25);
        border-color: rgba(176, 141, 47, .3);
    }

    .gallery-card-img {
        position: relative;
        aspect-ratio: 4/3;
        background: var(--sand-dim);
        overflow: hidden;
    }

    .gallery-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s;
    }

    .gallery-card:hover .gallery-card-img img {
        transform: scale(1.05);
    }

    .gallery-card-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 700;
        backdrop-filter: blur(6px);
    }

    .gallery-card-badge.active {
        background: rgba(46, 125, 50, .9);
        color: #fff;
    }

    .gallery-card-badge.inactive {
        background: rgba(198, 40, 40, .9);
        color: #fff;
    }

    .gallery-card-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .gallery-card-title {
        font-size: .8rem;
        color: rgba(42, 27, 28, .75);
        line-height: 1.4;
        min-height: 2.4em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .gallery-card-actions {
        display: flex;
        gap: 6px;
        margin-top: auto;
    }

    .gallery-card-actions .btn {
        flex: 1;
        padding: 7px 8px;
        font-size: .75rem;
        justify-content: center;
        gap: 4px;
    }

    .gallery-card-actions .btn svg {
        width: 12px;
        height: 12px;
    }

    @media (max-width:700px) {
        .gallery-admin {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }

    @media (max-width:400px) {
        .gallery-admin {
            grid-template-columns: 1fr;
        }

        .gallery-card-actions {
            flex-direction: column;
        }
    }
</style>

<!-- Stats -->
<div class="list-stats">
    <div class="list-stat">
        <div class="list-stat-icon total"><i data-lucide="image"></i></div>
        <div>
            <div class="list-stat-label">إجمالي الصور</div>
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

<!-- Add / Edit Form -->
<div class="card">
    <h3 class="card-title">
        <i data-lucide="<?= $edit ? 'pencil' : 'plus-circle' ?>" style="width:18px;height:18px"></i>
        <?= $edit ? 'تعديل صورة' : 'إضافة صورة جديدة' ?>
    </h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <input type="hidden" name="current_image" value="<?= htmlspecialchars($edit['image_path'] ?? '') ?>">

        <div class="form-grid">
            <label class="full">
                الصورة <?= $edit ? '(اتركها فارغة للإبقاء على الصورة الحالية)' : '' ?>
                <input type="file" name="image" accept="image/*" <?= $edit ? '' : 'required' ?>>
            </label>

            <?php if ($edit): ?>
                <div class="full" style="display:flex;align-items:center;gap:14px;padding:12px;background:var(--sand);border-radius:10px">
                    <img src="../<?= htmlspecialchars($edit['image_path']) ?>" alt=""
                        style="width:80px;height:80px;border-radius:8px;object-fit:cover">
                    <div style="min-width:0">
                        <div style="font-size:.82rem;color:rgba(42,27,28,.6);margin-bottom:2px">الصورة الحالية</div>
                        <div style="font-size:.78rem;direction:ltr;color:rgba(42,27,28,.5);word-break:break-all">
                            <?= htmlspecialchars($edit['image_path']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <label class="full">النص البديل (Alt)
                <input type="text" name="alt_text" value="<?= htmlspecialchars($edit['alt_text'] ?? '') ?>"
                    placeholder="مثال: تأجير بيوت شعر للأفراح في الرياض">
            </label>

            <label>ترتيب الظهور
                <input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>">
            </label>

            <label style="flex-direction:row;align-items:center;gap:8px">
                <input type="checkbox" name="is_active" <?= (!isset($edit['is_active']) || $edit['is_active']) ? 'checked' : '' ?>>
                مفعّلة
            </label>
        </div>

        <div style="margin-top:22px;display:flex;gap:10px">
            <button class="btn btn-primary" type="submit">
                <i data-lucide="<?= $edit ? 'save' : 'upload' ?>" style="width:16px;height:16px"></i>
                <?= $edit ? 'حفظ التعديلات' : 'رفع الصورة' ?>
            </button>
            <?php if ($edit): ?>
                <a href="<?= filterUrl('gallery.php', ['edit' => null]) ?>" class="btn" style="background:#f0f0f0;color:#555">إلغاء</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Filter Bar -->
<div class="card" style="padding:18px">
    <form method="get" action="gallery.php" class="list-filters">
        <div class="filter-group" style="flex:1;min-width:220px">
            <label for="filter-q">بحث</label>
            <input type="text" id="filter-q" name="q" value="<?= htmlspecialchars($q) ?>"
                placeholder="ابحث في النص البديل أو المسار...">
        </div>
        <div class="filter-group">
            <label for="filter-status">الحالة</label>
            <select id="filter-status" name="status">
                <option value="all" <?= $status === 'all'      ? 'selected' : '' ?>>الكل</option>
                <option value="active" <?= $status === 'active'   ? 'selected' : '' ?>>مفعّلة</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>معطّلة</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="search" style="width:15px;height:15px"></i>
                تصفية
            </button>
            <?php if ($q !== '' || $status !== 'all'): ?>
                <a href="gallery.php" class="btn" style="background:#f0f0f0;color:#555">
                    <i data-lucide="x" style="width:15px;height:15px"></i>
                    مسح
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Results Header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
    <div style="font-size:.9rem;color:rgba(42,27,28,.7)">
        <?php if ($q !== '' || $status !== 'all'): ?>
            نتائج التصفية: <strong style="color:var(--maroon)"><?= $total ?></strong> صورة
        <?php else: ?>
            <strong style="color:var(--maroon)"><?= $total ?></strong> صورة في المعرض
        <?php endif; ?>
    </div>
</div>

<?php if (empty($rows)): ?>
    <div class="empty-state">
        <i data-lucide="image-off"></i>
        <h3>لا توجد صور لعرضها</h3>
        <p>
            <?php if ($q !== '' || $status !== 'all'): ?>
                لم نجد نتائج مطابقة للتصفية الحالية. جرّب تغيير كلمة البحث أو الفلتر.
                <br>
                <a href="gallery.php" style="color:var(--maroon);font-weight:700;margin-top:10px;display:inline-block">مسح التصفية</a>
            <?php else: ?>
                ابدأ بإضافة أول صورة من النموذج بالأعلى.
            <?php endif; ?>
        </p>
    </div>
<?php else: ?>

    <!-- Grid View -->
    <div class="gallery-admin">
        <?php foreach ($rows as $r): ?>
            <div class="gallery-card">
                <div class="gallery-card-img">
                    <img src="../<?= htmlspecialchars($r['image_path']) ?>"
                        alt="<?= htmlspecialchars($r['alt_text']) ?>" loading="lazy">
                    <span class="gallery-card-badge <?= $r['is_active'] ? 'active' : 'inactive' ?>">
                        <?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?>
                    </span>
                </div>
                <div class="gallery-card-body">
                    <div class="gallery-card-title">
                        <?= htmlspecialchars($r['alt_text'] ?: '— بدون نص بديل —') ?>
                    </div>
                    <div class="gallery-card-actions">
                        <a href="<?= filterUrl('gallery.php', ['edit' => $r['id']]) ?>" class="btn btn-gold" title="تعديل">
                            <i data-lucide="pencil"></i> تعديل
                        </a>
                        <a href="<?= filterUrl('gallery.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>"
                            class="btn btn-danger"
                            onclick="return confirmDelete(this.href, 'حذف هذه الصورة نهائيًا من المعرض؟')">
                            <i data-lucide="trash-2"></i> حذف
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination (from functions.php) -->
    <?php renderPagination('gallery.php', $pg); ?>

    <!-- Management Table — Current Page Items -->
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
                        <th style="width:70px">معاينة</th>
                        <th>النص البديل</th>
                        <th>المسار</th>
                        <th style="width:70px">الترتيب</th>
                        <th style="width:90px">الحالة</th>
                        <th style="width:130px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td>
                                <img src="../<?= htmlspecialchars($r['image_path']) ?>" alt=""
                                    style="width:44px;height:44px;border-radius:6px;object-fit:cover;display:block">
                            </td>
                            <td><?= htmlspecialchars($r['alt_text'] ?: '—') ?></td>
                            <td style="font-size:.78rem;direction:ltr;text-align:left;color:rgba(42,27,28,.55)">
                                <?= htmlspecialchars($r['image_path']) ?>
                            </td>
                            <td><?= (int)$r['sort_order'] ?></td>
                            <td>
                                <span class="badge badge-<?= $r['is_active'] ? 'active' : 'inactive' ?>">
                                    <?= $r['is_active'] ? 'مفعّلة' : 'معطّلة' ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="<?= filterUrl('gallery.php', ['edit' => $r['id']]) ?>"
                                    class="btn btn-sm btn-gold" title="تعديل">
                                    <i data-lucide="pencil" style="width:13px;height:13px"></i>
                                </a>
                                <a href="<?= filterUrl('gallery.php', ['delete' => $r['id'], 'csrf' => csrfToken()]) ?>"
                                    class="btn btn-sm btn-danger" title="حذف"
                                    onclick="return confirmDelete(this.href, 'حذف هذه الصورة نهائيًا من المعرض؟')">
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

<?php include __DIR__ . '/includes/footer.php'; ?>