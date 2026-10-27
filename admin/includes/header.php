<?php
// admin/includes/header.php
require_once __DIR__ . '/../../includes/icons.php';
$__flash = getFlash();
$__iconList = [];
foreach (getIconPaths() as $name => $path) {
    $__iconList[] = ['name' => $name, 'path' => $path, 'label' => getIconLabels()[$name] ?? $name];
}
$__currentPage = basename($_SERVER['PHP_SELF']);
function nav($file, $label, $icon) {
    global $__currentPage;
    $active = $__currentPage === $file ? ' active' : '';
    return '<a href="'.$file.'" class="nav-link'.$active.'"><i data-lucide="'.$icon.'"></i><span>'.$label.'</span></a>';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'لوحة التحكم') ?> — تأجير مكيفات</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="mobile-overlay" id="mobileOverlay"></div>

<div class="admin-layout">

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <svg viewBox="0 0 40 40" width="30" height="30">
                <path d="M6 32 L20 8 L34 32 Z" fill="none" stroke="#D4B55C" stroke-width="2.3" stroke-linejoin="round"/>
                <path d="M20 32 L20 19" stroke="#D4B55C" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="brand-text">
            <strong>تأجير مكيفات</strong>
            <small>لوحة التحكم</small>
        </div>
    </div>

    <div class="sidebar-section-label">القائمة الرئيسية</div>
    <nav class="sidebar-nav">
        <?= nav('dashboard.php', 'الرئيسية', 'layout-dashboard') ?>
        <?= nav('settings.php', 'الإعدادات العامة', 'settings') ?>
        <?= nav('services.php', 'الخدمات', 'grid-3x3') ?>
        <?= nav('why-us.php', 'مميزاتنا', 'shield-check') ?>
        <?= nav('process.php', 'خطوات العمل', 'list-checks') ?>
        <?= nav('cities.php', 'المدن', 'map-pin') ?>
        <?= nav('gallery.php', 'معرض الصور', 'image') ?>
        <?= nav('faqs.php', 'الأسئلة الشائعة', 'help-circle') ?>
        <?= nav('intro.php', 'المقدمة', 'align-right') ?>
        <?= nav('occasions.php', 'المناسبات', 'calendar-heart') ?>
        <?= nav('city-seo.php', 'SEO المدن', 'search') ?>
        <div class="sidebar-section-label">الحساب</div>
        <?= nav('profile.php', 'حسابي', 'user-round') ?>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn" onclick="return confirmLogout(this.href)">
            <i data-lucide="log-out"></i><span>تسجيل الخروج</span>
        </a>
    </div>
</aside>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="mobile-toggle" id="mobileToggle" aria-label="القائمة">
            <i data-lucide="menu"></i>
        </button>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
        <div class="admin-user">
            <div class="user-avatar"><?= mb_substr($_SESSION['admin_username'] ?? 'A', 0, 1) ?></div>
            <div class="user-info">
                <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></strong>
                <small>مدير الموقع</small>
            </div>
        </div>
    </div>

    <div class="admin-content">