<?php
require_once __DIR__ . '/includes/auth.php';

$counts = [
    'services'  => (int)$pdo->query('SELECT COUNT(*) FROM services')->fetchColumn(),
    'why_us'    => (int)$pdo->query('SELECT COUNT(*) FROM why_us')->fetchColumn(),
    'cities'    => (int)$pdo->query('SELECT COUNT(*) FROM cities')->fetchColumn(),
    'gallery'   => (int)$pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn(),
    'faqs'      => (int)$pdo->query('SELECT COUNT(*) FROM faqs')->fetchColumn(),
    'occasions' => (int)$pdo->query('SELECT COUNT(*) FROM occasions')->fetchColumn(),
];
$totalItems = array_sum($counts);

// حساب نسبة اكتمال الموقع (تقديري)
$checks = [
    'خدمات'    => min(1, $counts['services'] / 6),
    'مميزات'   => min(1, $counts['why_us'] / 4),
    'صور'      => min(1, $counts['gallery'] / 6),
    'أسئلة'    => min(1, $counts['faqs'] / 5),
    'مدن'      => min(1, $counts['cities'] / 10),
];
$completion = round(array_sum($checks) / count($checks) * 100);

$pageTitle = 'الرئيسية';
include __DIR__ . '/includes/header.php';
?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-inner">
        <div class="welcome-badge">
            <i data-lucide="sparkles"></i>
            <span>لوحة تحكم احترافية</span>
        </div>
        <h2>أهلاً <em><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></em>، عندك <em><?= $totalItems ?></em> عنصر جاهز</h2>
        <p>تحكم كامل في كل محتوى موقعك من مكان واحد. ابدأ من الإجراءات السريعة بالأسفل أو تنقّل بين الأقسام من القائمة الجانبية.</p>
        <div class="welcome-actions">
            <a href="../index.php" target="_blank" class="btn btn-gold">
                <i data-lucide="external-link" style="width:16px;height:16px"></i>
                معاينة الموقع
            </a>
            <a href="settings.php" class="btn" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2)">
                <i data-lucide="settings" style="width:16px;height:16px"></i>
                الإعدادات العامة
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <a href="services.php" class="stat-card">
        <div class="stat-body">
            <div class="stat-label">الخدمات</div>
            <div class="stat-value"><?= $counts['services'] ?></div>
            <div class="stat-trend"><i data-lucide="check-circle" style="width:12px;height:12px"></i> نشطة</div>
        </div>
        <div class="stat-icon"><i data-lucide="grid-3x3"></i></div>
    </a>
    <a href="why-us.php" class="stat-card">
        <div class="stat-body">
            <div class="stat-label">المميزات</div>
            <div class="stat-value"><?= $counts['why_us'] ?></div>
        </div>
        <div class="stat-icon"><i data-lucide="shield-check"></i></div>
    </a>
    <a href="gallery.php" class="stat-card">
        <div class="stat-body">
            <div class="stat-label">صور المعرض</div>
            <div class="stat-value"><?= $counts['gallery'] ?></div>
        </div>
        <div class="stat-icon"><i data-lucide="image"></i></div>
    </a>
    <a href="faqs.php" class="stat-card">
        <div class="stat-body">
            <div class="stat-label">الأسئلة الشائعة</div>
            <div class="stat-value"><?= $counts['faqs'] ?></div>
        </div>
        <div class="stat-icon"><i data-lucide="help-circle"></i></div>
    </a>
    <a href="cities.php" class="stat-card">
        <div class="stat-body">
            <div class="stat-label">المدن</div>
            <div class="stat-value"><?= $counts['cities'] ?></div>
        </div>
        <div class="stat-icon"><i data-lucide="map-pin"></i></div>
    </a>
    <a href="occasions.php" class="stat-card">
        <div class="stat-body">
            <div class="stat-label">المناسبات</div>
            <div class="stat-value"><?= $counts['occasions'] ?></div>
        </div>
        <div class="stat-icon"><i data-lucide="calendar-heart"></i></div>
    </a>
</div>

<!-- Quick Actions -->
<h3 style="font-family:'Cairo';color:var(--maroon);font-size:1.05rem;margin:0 0 14px">إجراءات سريعة</h3>
<div class="quick-actions">
    <a href="gallery.php" class="quick-action">
        <div class="quick-action-icon"><i data-lucide="upload"></i></div>
        <div class="quick-action-text"><strong>رفع صورة</strong><small>أضف صورة للمعرض</small></div>
    </a>
    <a href="services.php" class="quick-action">
        <div class="quick-action-icon"><i data-lucide="plus"></i></div>
        <div class="quick-action-text"><strong>إضافة خدمة</strong><small>خدمة جديدة</small></div>
    </a>
    <a href="faqs.php" class="quick-action">
        <div class="quick-action-icon"><i data-lucide="message-circle-question"></i></div>
        <div class="quick-action-text"><strong>سؤال جديد</strong><small>أضف سؤال شائع</small></div>
    </a>
    <a href="profile.php" class="quick-action">
        <div class="quick-action-icon"><i data-lucide="key-round"></i></div>
        <div class="quick-action-text"><strong>كلمة المرور</strong><small>غيّر كلمة المرور</small></div>
    </a>
</div>

<!-- Two Column Layout -->
<div class="dash-cols">
    <!-- Completion -->
    <div class="card">
        <h3 class="card-title">
            <i data-lucide="trending-up" style="width:18px;height:18px"></i>
            اكتمال الموقع — <?= $completion ?>%
        </h3>
        <?php foreach ($checks as $label => $val): ?>
        <div class="progress-item">
            <div class="progress-head">
                <span><?= htmlspecialchars($label) ?></span>
                <span><?= round($val * 100) ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= round($val * 100) ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tips -->
    <div class="card">
        <h3 class="card-title">
            <i data-lucide="lightbulb" style="width:18px;height:18px"></i>
            نصائح سريعة
        </h3>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <strong>أضف 8+ صور للمعرض</strong>
                <small>الصور الأكثر تحسّن معدل التحويل</small>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <strong>حدّث الأسئلة الشائعة</strong>
                <small>أسئلة أكثر = زيارات أكثر من جوجل</small>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <strong>راجع أرقام الاتصال</strong>
                <small>تأكد من صحة الأرقام في الإعدادات</small>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <strong>اضبط SEO المدن</strong>
                <small>نص فريد لكل مدينة يحسّن الترتيب</small>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>