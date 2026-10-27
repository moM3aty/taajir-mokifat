<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/icons.php'; 

$s = getAllSettings($pdo);

$services   = $pdo->query('SELECT * FROM services WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$whyUs      = $pdo->query('SELECT * FROM why_us WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$steps      = $pdo->query('SELECT * FROM process_steps WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$cities     = $pdo->query('SELECT * FROM cities WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$gallery    = $pdo->query('SELECT * FROM gallery WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$faqs       = $pdo->query('SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$intros     = $pdo->query('SELECT * FROM intro_paragraphs WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$occasions  = $pdo->query('SELECT * FROM occasions WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$citySeo    = $pdo->query('SELECT * FROM city_seo WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();

$phoneTel     = $s['phone_tel']     ?? '+966503513824';
$phoneDisplay = $s['phone_display'] ?? '0503 513 824';
$whatsapp     = $s['whatsapp']      ?? '966503513824';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($s['site_title'] ?? '') ?></title>
<meta name="description" content="<?= e($s['site_description'] ?? '') ?>">
<meta name="keywords" content="<?= e($s['site_keywords'] ?? '') ?>">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="author" content="<?= e($s['site_name'] ?? '') ?>">
<meta name="geo.region" content="SA-01">
<meta name="geo.placename" content="الرياض">
<meta property="og:type" content="website">
<meta property="og:locale" content="ar_SA">
<meta property="og:title" content="<?= e($s['og_title'] ?? '') ?>">
<meta property="og:description" content="<?= e($s['og_description'] ?? '') ?>">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": <?= json_encode($s['site_name'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
  "description": <?= json_encode($s['schema_description'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
  "telephone": <?= json_encode($phoneTel, JSON_UNESCAPED_UNICODE) ?>,
  "address": { "@type": "PostalAddress", "addressLocality": "الرياض", "addressCountry": "SA" },
  "areaServed": "المملكة العربية السعودية",
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "خدمات التأجير",
    "itemListElement": [
      <?php
      $offers = [];
      foreach ($services as $sv) {
        $offers[] = '{"@type":"Offer","itemOffered":{"@type":"Service","name":' . json_encode($sv['title'], JSON_UNESCAPED_UNICODE) . '}}';
      }
      echo implode(',', $offers);
      ?>
    ]
  }
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body>

<header class="site-header">
<div class="wrap header-inner">
<a href="#top" class="brand">
<span class="brand-mark" aria-hidden="true">
<svg viewBox="0 0 40 40" width="32" height="32">
<path d="M6 32 L20 8 L34 32 Z" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linejoin="round" />
<path d="M20 32 L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
</svg>
</span>
<span class="brand-name"> <?= e($s['site_name'] ?? '') ?> <em> اسبلت تأجير بيوت شعر</em></span>
</a>
<nav class="main-nav" id="mainNav" aria-label="التنقل الرئيسي">
<a href="#services">خدماتنا</a>
<a href="#why">مميزاتنا</a>
<a href="#coverage">نطاق التغطية</a>
<a href="#gallery">معرض الأعمال</a>
<a href="#contact">تواصل معنا</a>
</nav>
<div class="header-actions">
<a class="header-call" href="tel:<?= e($phoneTel) ?>">
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
<path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z" />
</svg>
<span>اتصل الآن</span>
</a>
<button class="nav-toggle" id="navToggle" type="button" aria-label="فتح القائمة" aria-expanded="false" aria-controls="mainNav">
<span></span><span></span><span></span>
</button>
</div>
</div>
</header>

<main id="top">

<section class="hero">
<div class="lanterns" aria-hidden="true">
<span></span><span></span><span></span><span></span><span></span><span></span>
</div>
<div class="wrap hero-inner">
<p class="hero-badge"><?= e($s['hero_badge'] ?? '') ?></p>
<h1 class="hero-title">
<span class="flap-line" data-flap><?= e($s['hero_line1'] ?? '') ?></span>
<span class="flap-line" data-flap><?= $s['hero_line2'] ?? '' ?></span>
</h1>
<p class="hero-sub">
<?= nl2br(e($s['hero_sub'] ?? '')) ?>
</p>
<div class="hero-actions">
<a class="btn btn-primary" href="tel:<?= e($phoneTel) ?>">
<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
<path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z" />
</svg>
<?= e($phoneDisplay) ?>
</a>
<a class="btn btn-ghost" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">تواصل واتساب</a>
</div>
</div>
</section>

<section class="intro">
<div class="wrap intro-inner">
<?php foreach ($intros as $p): ?>
<p><?= $p['content'] ?></p>
<?php endforeach; ?>
</div>
</section>

<section class="services" id="services">
<div class="wrap">
<h2 class="section-title">خدماتنا</h2>
<p class="section-sub">تجهيز كامل لمناسبتك: بيت الشعر والتبريد والتدفئة والجلسات مع بعض، أو كل واحد لوحده حسب احتياجك.</p>
<div class="services-grid">
<?php foreach ($services as $sv): ?>
<div class="service-card">
<span class="service-icon-badge"><?= renderIcon($sv['icon_svg'], 30, 2) ?></span>
<h3><?= e($sv['title']) ?></h3>
<p><?= e($sv['description']) ?></p>
</div>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="why" id="why">
<div class="wrap">
<h2 class="section-title light">ليه تأجير مكيفات؟</h2>
<div class="why-grid">
<?php foreach ($whyUs as $w): ?>
<div class="why-card">
<span class="why-icon"><?= renderIcon($w['icon_svg'], 28, 2) ?></span>
<h3><?= e($w['title']) ?></h3>
<p><?= e($w['description']) ?></p>
</div>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="process" id="process">
<div class="wrap">
<h2 class="section-title">إزاي بنجهز مناسبتك</h2>
<div class="process-grid">
<?php foreach ($steps as $st): ?>
<div class="process-step">
<span class="process-num"><?= e($st['step_number']) ?></span>
<h3><?= e($st['title']) ?></h3>
<p><?= e($st['description']) ?></p>
</div>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="intro" id="occasions">
<div class="wrap intro-inner">
<h2 class="section-title">تجهيز كل أنواع المناسبات</h2>
<?php foreach ($occasions as $o): ?>
<p><strong><?= e($o['title']) ?>:</strong> <?= e($o['content']) ?></p>
<?php endforeach; ?>
</div>
</section>

<section class="coverage" id="coverage">
<div class="wrap">
<h2 class="section-title light"><?= e($s['coverage_title'] ?? 'نطاق التغطية') ?></h2>
<p class="coverage-sub"><?= e($s['coverage_sub'] ?? '') ?></p>
</div>
<div class="ticker" aria-hidden="true">
<div class="ticker-track">
<?php for ($i = 0; $i < 2; $i++): foreach ($cities as $c): ?>
<span><?= e($c['name']) ?></span>
<?php endforeach; endfor; ?>
</div>
</div>
</section>

<section class="intro" id="cities">
<div class="wrap intro-inner">
<h2 class="section-title">تأجير مكيفات وبيوت شعر في مدن المملكة</h2>
<?php foreach ($citySeo as $cs): ?>
<p><strong><?= e($cs['title']) ?>:</strong> <?= e($cs['content']) ?></p>
<?php endforeach; ?>
</div>
</section>

<section class="gallery" id="gallery">
<div class="wrap">
<h2 class="section-title">معرض الأعمال</h2>
<p class="section-sub">نماذج من بيوت الشعر والخيم الشعبية والتجهيزات اللي سويناها لمناسبات عملائنا.</p>
<div class="gallery-grid">
<?php foreach ($gallery as $g): ?>
<figure class="gallery-item">
<img src="<?= e($g['image_path']) ?>" alt="<?= e($g['alt_text']) ?>" loading="lazy" onerror="this.style.display='none'; this.parentElement.classList.add('gallery-item--empty')">
<span class="gallery-placeholder" aria-hidden="true"><svg viewBox="0 0 24 24" width="28" height="28"><path d="M3 19 L12 5 L21 19 Z" fill="none" stroke="currentColor" stroke-width="1.6" /></svg></span>
</figure>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="faq" id="faq">
<div class="wrap">
<h2 class="section-title">أسئلة شائعة</h2>
<div class="faq-list">
<?php foreach ($faqs as $f): ?>
<div class="faq-item">
<button class="faq-question" type="button" aria-expanded="false"><span><?= e($f['question']) ?></span><span class="faq-icon" aria-hidden="true"></span></button>
<div class="faq-answer"><div class="faq-answer-inner"><p><?= e($f['answer']) ?></p></div></div>
</div>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="cta-banner">
<div class="wrap cta-inner">
<h2><?= e($s['cta_title'] ?? '') ?></h2>
<p><?= e($s['cta_text'] ?? '') ?></p>
<div class="cta-actions">
<a class="btn btn-dark" href="tel:<?= e($phoneTel) ?>"><?= e($phoneDisplay) ?></a>
<a class="btn btn-outline-dark" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">واتساب</a>
</div>
</div>
</section>

<section class="contact" id="contact">
<div class="wrap contact-grid-wrap">
<div class="contact-form-col">
<h2 class="section-title light">احجز مناسبتك الآن</h2>
<p class="contact-sub">املأ البيانات وهيوصلك طلبك على واتساب على طول.</p>
<form class="contact-form" id="contactForm">
<div class="form-row">
<label>الاسم
<input type="text" name="name" required placeholder="اسمك">
</label>
<label>رقم الجوال
<input type="tel" name="phone" required dir="ltr" placeholder="05XXXXXXXX">
</label>
</div>
<div class="form-row">
<label>نوع المناسبة
<select name="type">
<option>فرح</option>
<option>عزاء</option>
<option>تجمع عائلي</option>
<option>مناسبة أخرى</option>
</select>
</label>
<label>المدينة
<input type="text" name="city" placeholder="مثال: الرياض / جدة">
</label>
</div>
<label>تفاصيل المناسبة
<textarea name="details" rows="4" placeholder="تاريخ المناسبة، عدد الحضور التقريبي، هل تحتاج بيت شعر ومكيفات مع بعض..."></textarea>
</label>
<button type="submit" class="btn btn-primary">
<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2zm0 18.2c-1.6 0-3.2-.4-4.5-1.3l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.3-.6.8-.8 1-.1.2-.3.2-.5.1-.3-.1-1.2-.4-2.2-1.4-.8-.7-1.4-1.6-1.5-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.5 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.2-.3-.2-.6-.4z" /></svg>
إرسال عبر واتساب
</button>
</form>
</div>

<div class="contact-side">
<a class="contact-card contact-card--main" href="tel:<?= e($phoneTel) ?>">
<span class="contact-tag">اتصل الآن</span>
<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z" /></svg>
<span dir="ltr"><?= e($phoneDisplay) ?></span>
</a>
<a class="contact-card" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">
<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2zm0 18.2c-1.6 0-3.2-.4-4.5-1.3l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.3-.6.8-.8 1-.1.2-.3.2-.5.1-.3-.1-1.2-.4-2.2-1.4-.8-.7-1.4-1.6-1.5-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.5 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.2-.3-.2-.6-.4z" /></svg>
<span>واتساب مباشر</span>
</a>
<p class="contact-note"><?= e($s['contact_note'] ?? '') ?></p>
</div>
</div>
</section>

</main>

<a class="floating-call" href="tel:<?= e($phoneTel) ?>" aria-label="اتصل الآن">
<span class="ring"></span>
<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z" /></svg>
</a>
<a class="floating-whatsapp" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" aria-label="واتساب">
<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2zm0 18.2c-1.6 0-3.2-.4-4.5-1.3l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.3-.6.8-.8 1-.1.2-.3.2-.5.1-.3-.1-1.2-.4-2.2-1.4-.8-.7-1.4-1.6-1.5-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.5 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.2-.3-.2-.6-.4z" /></svg>
</a>

<footer class="site-footer">
<div class="wrap footer-grid">
<div class="footer-brand">
<a href="#top" class="brand">
<span class="brand-mark" aria-hidden="true">
<svg viewBox="0 0 40 40" width="28" height="28"><path d="M6 32 L20 8 L34 32 Z" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linejoin="round" /><path d="M20 32 L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
</span>
<span class="brand-name"><?= e($s['site_name'] ?? '') ?><em>بيوت شعر ومكيفات</em></span>
</a>
<p><?= e($s['footer_about'] ?? '') ?></p>
<div class="footer-social">
<a href="tel:<?= e($phoneTel) ?>" aria-label="اتصال">
<svg viewBox="0 0 24 24" width="17" height="17"><path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z" /></svg>
</a>
<a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" aria-label="واتساب">
<svg viewBox="0 0 24 24" width="17" height="17"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2zm0 18.2c-1.6 0-3.2-.4-4.5-1.3l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.3-.6.8-.8 1-.1.2-.3.2-.5.1-.3-.1-1.2-.4-2.2-1.4-.8-.7-1.4-1.6-1.5-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.5 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.2-.3-.2-.6-.4z" /></svg>
</a>
</div>
</div>
<div class="footer-col">
<h4>روابط سريعة</h4>
<a href="#services">خدماتنا</a>
<a href="#why">مميزاتنا</a>
<a href="#coverage">نطاق التغطية</a>
<a href="#gallery">معرض الأعمال</a>
<a href="#faq">الأسئلة الشائعة</a>
</div>
<div class="footer-col">
<h4>تواصل معنا</h4>
<a href="tel:<?= e($phoneTel) ?>" dir="ltr"><?= e($phoneDisplay) ?></a>
<a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">واتساب مباشر</a>
<p class="footer-note">الرياض — ونخدم جميع مناطق المملكة</p>
</div>
</div>
<div class="wrap footer-bottom">
<span>© <?= e($s['footer_copyright'] ?? '2026') ?> <?= e($s['site_name'] ?? '') ?>.</span>
<p class="designer-credit">
تصميم وتنفيذ
<a href="https://www.facebook.com/share/199H6ZiBeM/" target="_blank" style="text-decoration: none;">
<span class="gmt-text" style="color: var(--gold-light); font-weight: bold;">GMT Web</span>
</a>
</p>
</div>
</footer>

<script src="script.js"></script>
</body>

</html>