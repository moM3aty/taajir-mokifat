<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id']   = $user['id'];
        $_SESSION['admin_username']  = $user['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>دخول لوحة التحكم — تأجير مكيفات</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<style>
/* ============================================================
   PREMIUM LOGIN — Split screen with animated background
   ============================================================ */
:root {
    --maroon: #6B1D23;
    --maroon-deep: #4A1317;
    --maroon-dark: #2A0A0F;
    --gold: #B08D2F;
    --gold-light: #D4B55C;
    --sand: #F3EADC;
    --ink: #2A1B1C;
}

* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body {
    font-family: 'Tajawal', Tahoma, sans-serif;
    background: var(--maroon-dark);
    color: #fff;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

/* ============ LAYOUT ============ */
.login-page {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    position: relative;
    overflow: hidden !important;
}

/* ============ LEFT SIDE — BRAND SHOWCASE ============ */
.brand-panel {
    position: relative;
    overflow: hidden !important;
    background: linear-gradient(160deg, var(--maroon) 0%, var(--maroon-deep) 55%, var(--maroon-dark) 100%);
    padding: 40px 60px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    z-index: 2;
}

/* Animated gradient blobs */
.brand-panel::before,
.brand-panel::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.5;
    pointer-events: none;
    animation: blobFloat 18s ease-in-out infinite;
}
.brand-panel::before {
    width: 480px;
    height: 480px;
    top: -180px;
    left: -140px;
    background: radial-gradient(circle, rgba(212,181,92,.55), transparent 70%);
}
.brand-panel::after {
    width: 420px;
    height: 420px;
    bottom: -180px;
    right: -100px;
    background: radial-gradient(circle, rgba(176,141,47,.4), transparent 70%);
    animation-delay: -9s;
    animation-duration: 22s;
}
@keyframes blobFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33%      { transform: translate(30px, -40px) scale(1.08); }
    66%      { transform: translate(-20px, 30px) scale(0.94); }
}

/* Grid pattern overlay */
.brand-panel .grid-overlay {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
    pointer-events: none;
}

/* Floating lanterns */
.lanterns {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}
.lantern {
    position: absolute;
    bottom: -30px;
    width: 9px;
    height: 12px;
    border-radius: 3px;
    background: radial-gradient(circle, #FFE082, #D4B55C);
    box-shadow:
        0 0 12px 3px rgba(212,181,92,.55),
        0 0 30px 8px rgba(212,181,92,.22);
    opacity: 0;
    animation: lanternRise linear infinite;
}
.lantern::before {
    content: '';
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 1px;
    height: 8px;
    background: rgba(212,181,92,.4);
}
@keyframes lanternRise {
    0%   { transform: translateY(0) translateX(0); opacity: 0; }
    10%  { opacity: .85; }
    85%  { opacity: .5; }
    100% { transform: translateY(-120vh) translateX(20px); opacity: 0; }
}

/* Lantern positions & timing */
.lantern:nth-child(1)  { right: 8%;  animation-duration: 14s; animation-delay: 0s;   width: 8px;  height: 11px; }
.lantern:nth-child(2)  { right: 22%; animation-duration: 18s; animation-delay: 2.5s; width: 10px; height: 13px; }
.lantern:nth-child(3)  { right: 35%; animation-duration: 16s; animation-delay: 5s;   width: 7px;  height: 10px; }
.lantern:nth-child(4)  { right: 48%; animation-duration: 20s; animation-delay: 1.5s; width: 9px;  height: 12px; }
.lantern:nth-child(5)  { right: 62%; animation-duration: 15s; animation-delay: 4s;   width: 11px; height: 14px; }
.lantern:nth-child(6)  { right: 76%; animation-duration: 19s; animation-delay: 7s;   width: 8px;  height: 11px; }
.lantern:nth-child(7)  { right: 88%; animation-duration: 13s; animation-delay: 3s;   width: 6px;  height: 9px; }
.lantern:nth-child(8)  { right: 15%; animation-duration: 21s; animation-delay: 9s;   width: 9px;  height: 12px; }
.lantern:nth-child(9)  { right: 55%; animation-duration: 17s; animation-delay: 11s;  width: 10px; height: 13px; }
.lantern:nth-child(10) { right: 42%; animation-duration: 22s; animation-delay: 6s;   width: 7px;  height: 10px; }

/* Brand content */
.brand-content {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    gap: 32px;
    animation: fadeInUp .9s cubic-bezier(.2,.8,.2,1) both;
}

.brand-logo {
    display: flex;
    align-items: center;
    gap: 14px;
}
.brand-logo-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(212,181,92,.22), rgba(212,181,92,.06));
    border: 1.5px solid rgba(212,181,92,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 30px -10px rgba(212,181,92,.5);
    animation: logoFloat 5s ease-in-out infinite;
}
.brand-logo-icon svg {
    width: 32px;
    height: 32px;
}
@keyframes logoFloat {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-4px); }
}
.brand-logo-text strong {
    display: block;
    font-family: 'Cairo';
    font-size: 1.3rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
}
.brand-logo-text small {
    font-size: .8rem;
    color: var(--gold-light);
    letter-spacing: .04em;
}

.brand-headline {
    font-family: 'Cairo';
    font-size: clamp(2rem, 3.4vw, 3rem);
    font-weight: 800;
    line-height: 1.25;
    color: #fff;
    letter-spacing: -.01em;
}
.brand-headline .accent {
    background: linear-gradient(120deg, var(--gold-light) 0%, #FFF0C0 50%, var(--gold-light) 100%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shimmerText 3s linear infinite;
}
@keyframes shimmerText {
    0%   { background-position: 200% center; }
    100% { background-position: 0% center; }
}

.brand-description {
    font-size: 1rem;
    line-height: 1.85;
    color: rgba(255,255,255,.72);
    max-width: 46ch;
}

.brand-features {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 8px;
}
.brand-feature {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    backdrop-filter: blur(6px);
    transition: transform .3s cubic-bezier(.2,.8,.2,1), border-color .3s, background .3s;
}
.brand-feature:hover {
    transform: translateX(-6px);
    border-color: rgba(212,181,92,.35);
    background: rgba(255,255,255,.06);
}
.feature-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(212,181,92,.2), rgba(212,181,92,.06));
    border: 1px solid rgba(212,181,92,.3);
    color: var(--gold-light);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.feature-icon svg { width: 20px; height: 20px; }
.feature-text strong {
    display: block;
    font-size: .92rem;
    color: #fff;
    font-weight: 700;
    margin-bottom: 2px;
}
.feature-text small {
    font-size: .78rem;
    color: rgba(255,255,255,.55);
}

.brand-footer {
    position: relative;
    z-index: 3;
    font-size: .8rem;
    color: rgba(255,255,255,.4);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    animation: fadeInUp 1s cubic-bezier(.2,.8,.2,1) .35s both;
}
.brand-footer .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--gold-light);
    box-shadow: 0 0 8px var(--gold-light);
    display: inline-block;
    margin-left: 6px;
    animation: pulseDot 2s ease-in-out infinite;
}
@keyframes pulseDot {
    0%, 100% { opacity: 1; }
    50%      { opacity: .3; }
}

/* ============ RIGHT SIDE — FORM ============ */
.form-panel {
    position: relative;
    background: var(--sand);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 40px;
    overflow: hidden;
}
.form-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 20%, rgba(176,141,47,.08), transparent 50%),
        radial-gradient(ellipse at 80% 80%, rgba(107,29,35,.05), transparent 50%);
    pointer-events: none;
}

.login-card {
    width: 100%;
    max-width: 400px;
    position: relative;
    z-index: 2;
    animation: fadeInUp .9s cubic-bezier(.2,.8,.2,1) .15s both;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.login-header {
    margin-bottom: 36px;
    text-align: right;
}
.login-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: rgba(176,141,47,.12);
    border: 1px solid rgba(176,141,47,.28);
    color: var(--maroon);
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .04em;
    margin-bottom: 16px;
}
.login-eyebrow svg { width: 12px; height: 12px; }

.login-header h1 {
    font-family: 'Cairo';
    font-size: 2rem;
    font-weight: 800;
    color: var(--maroon);
    line-height: 1.2;
    margin-bottom: 8px;
}
.login-header p {
    font-size: .94rem;
    color: rgba(42,27,28,.6);
    line-height: 1.65;
}

/* Error */
.login-error {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #FFEBEE;
    color: #B71C1C;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid rgba(198,40,40,.2);
    border-right: 4px solid #C62828;
    font-size: .88rem;
    font-weight: 600;
    margin-bottom: 22px;
    animation: shakeError .5s cubic-bezier(.36,.07,.19,.97);
}
.login-error svg { width: 18px; height: 18px; flex-shrink: 0; }
@keyframes shakeError {
    10%, 90% { transform: translateX(-1px); }
    20%, 80% { transform: translateX(2px); }
    30%, 50%, 70% { transform: translateX(-4px); }
    40%, 60% { transform: translateX(4px); }
}

/* Form */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.login-field { display: flex; flex-direction: column; gap: 8px; }
.login-label {
    font-size: .85rem;
    font-weight: 700;
    color: rgba(42,27,28,.8);
    display: flex;
    align-items: center;
    gap: 6px;
}
.login-label svg { width: 14px; height: 14px; color: var(--gold); }

.input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1.5px solid rgba(107,29,35,.14);
    border-radius: 12px;
    transition: border-color .2s, box-shadow .2s, background .2s;
    overflow: hidden;
}
.input-group:focus-within {
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(176,141,47,.12);
}
.input-group input {
    flex: 1;
    border: 0;
    outline: none;
    background: transparent;
    padding: 14px 16px;
    font-family: inherit;
    font-size: .94rem;
    color: var(--ink);
    font-weight: 500;
    width: 100%;
}
.input-group input::placeholder {
    color: rgba(42,27,28,.32);
    font-weight: 400;
}
.input-group input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 100px #fff inset;
    -webkit-text-fill-color: var(--ink);
}

.toggle-pass {
    background: transparent;
    border: 0;
    padding: 0 14px;
    height: 100%;
    cursor: pointer;
    color: rgba(42,27,28,.38);
    display: flex;
    align-items: center;
    transition: color .2s, background .2s;
}
.toggle-pass:hover {
    color: var(--maroon);
    background: rgba(107,29,35,.04);
}
.toggle-pass svg { width: 18px; height: 18px; }

/* Remember + forgot row */
.login-extras {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .85rem;
    gap: 12px;
    flex-wrap: wrap;
}
.remember {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: rgba(42,27,28,.7);
    font-weight: 500;
    user-select: none;
}
.remember input {
    appearance: none;
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    border: 1.5px solid rgba(107,29,35,.3);
    border-radius: 5px;
    background: #fff;
    cursor: pointer;
    position: relative;
    transition: background .2s, border-color .2s;
    flex-shrink: 0;
}
.remember input:checked {
    background: var(--maroon);
    border-color: var(--maroon);
}
.remember input:checked::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 5px;
    width: 5px;
    height: 9px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
.remember:hover input { border-color: var(--maroon); }

.forgot-link {
    color: var(--maroon);
    font-weight: 700;
    text-decoration: none;
    transition: color .2s;
}
.forgot-link:hover { color: var(--gold); }

/* Submit button */
.login-submit {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 24px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-deep) 100%);
    color: #fff;
    font-family: inherit;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 10px 24px -8px rgba(107,29,35,.55);
    margin-top: 4px;
}
.login-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212,181,92,.35), transparent);
    transition: left .6s;
}
.login-submit:hover::before {
    left: 100%;
}
.login-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 32px -10px rgba(107,29,35,.65);
}
.login-submit:active {
    transform: translateY(0);
}
.login-submit svg { width: 18px; height: 18px; }
.login-submit.loading {
    pointer-events: none;
    opacity: .85;
}
.login-submit.loading svg {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

/* Trust footer */
.login-trust {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgba(107,29,35,.08);
    font-size: .75rem;
    color: rgba(42,27,28,.5);
    flex-wrap: wrap;
}
.login-trust span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
}
.login-trust svg {
    width: 13px;
    height: 13px;
    color: var(--green, #2E7D32);
}

.login-help {
    text-align: center;
    margin-top: 24px;
    font-size: .8rem;
    color: rgba(42,27,28,.55);
}
.login-help a {
    color: var(--maroon);
    font-weight: 700;
    text-decoration: none;
    border-bottom: 1px dashed rgba(107,29,35,.3);
    padding-bottom: 1px;
    transition: color .2s, border-color .2s;
}
.login-help a:hover {
    color: var(--gold);
    border-color: var(--gold);
}

/* ============ RESPONSIVE ============ */
@media (max-width: 960px) {
    .login-page { grid-template-columns: 1fr; }
    .brand-panel {
        padding: 40px 32px;
        min-height: auto;
        justify-content: flex-start;
        gap: 24px;
    }
    .brand-content { gap: 20px; }
    .brand-description, .brand-features { display: none; }
    .brand-headline { font-size: 1.8rem; }
    .brand-footer { display: none; }
    .form-panel { padding: 40px 24px 60px; }
}

@media (max-width: 560px) {
    .brand-panel { padding: 32px 22px; }
    .brand-logo-icon { width: 48px; height: 48px; border-radius: 12px; }
    .brand-logo-icon svg { width: 26px; height: 26px; }
    .brand-logo-text strong { font-size: 1.1rem; }
    .brand-headline { font-size: 1.5rem; }
    .form-panel { padding: 28px 18px 50px; }
    .login-header h1 { font-size: 1.6rem; }
    .login-card { max-width: 100%; }
    .login-trust { gap: 12px; font-size: .7rem; }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .lantern,
    .brand-panel::before,
    .brand-panel::after,
    .brand-logo-icon,
    .brand-headline .accent,
    .login-submit.loading svg { animation: none; }
    .brand-content, .login-card, .brand-footer { animation: none; opacity: 1; transform: none; }
}
</style>
</head>
<body>

<div class="login-page">

    <!-- ============ LEFT: BRAND PANEL ============ -->
    <aside class="brand-panel">
        <div class="grid-overlay" aria-hidden="true"></div>

        <!-- Floating lanterns -->
        <div class="lanterns" aria-hidden="true">
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
            <span class="lantern"></span>
        </div>

        <div class="brand-content">
            <!-- Logo -->
            <div class="brand-logo">
                <div class="brand-logo-icon">
                    <svg viewBox="0 0 40 40" fill="none" stroke="#D4B55C" stroke-width="2.4" stroke-linejoin="round" stroke-linecap="round">
                        <path d="M6 32 L20 8 L34 32 Z" />
                        <path d="M20 32 L20 19" />
                    </svg>
                </div>
                <div class="brand-logo-text">
                    <strong>تأجير مكيفات</strong>
                    <small>لوحة التحكم</small>
                </div>
            </div>

            <!-- Headline -->
            <h1 class="brand-headline">
                أدر موقعك <span class="accent">باحترافية</span><br>
                من مكان واحد
            </h1>

            <!-- Description -->
            <p class="brand-description">
                تحكم كامل في الخدمات، المميزات، معرض الصور، الأسئلة الشائعة، والمدن — كل شيء في متناول يدك بتصميم عصري وسريع الاستجابة.
            </p>

            <!-- Features -->
            <div class="brand-features">
                <div class="brand-feature">
                    <div class="feature-icon"><i data-lucide="layout-dashboard"></i></div>
                    <div class="feature-text">
                        <strong>لوحة تحكم شاملة</strong>
                        <small>كل أقسام الموقع في مكان واحد</small>
                    </div>
                </div>
                <div class="brand-feature">
                    <div class="feature-icon"><i data-lucide="image"></i></div>
                    <div class="feature-text">
                        <strong>إدارة معرض الصور</strong>
                        <small>رفع وتنظيم سهل بمعاينة فورية</small>
                    </div>
                </div>
                <div class="brand-feature">
                    <div class="feature-icon"><i data-lucide="shield-check"></i></div>
                    <div class="feature-text">
                        <strong>حماية متقدمة</strong>
                        <small>CSRF + تشفير كلمات المرور</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            <span>
                <span class="dot"></span>
                جميع الأنظمة تعمل
            </span>
            <span>© <?= date('Y') ?> جميع الحقوق محفوظة</span>
        </div>
    </aside>

    <!-- ============ RIGHT: FORM PANEL ============ -->
    <main class="form-panel">
        <div class="login-card">

            <div class="login-header">
                <span class="login-eyebrow">
                    <i data-lucide="shield-check"></i>
                    منطقة آمنة
                </span>
                <h1>أهلاً بعودتك </h1>
                <p>سجّل الدخول للمتابعة إلى لوحة التحكم</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error">
                    <i data-lucide="alert-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form class="login-form" method="post" autocomplete="off" id="loginForm">

                <!-- Username -->
                <div class="login-field">
                    <label class="login-label" for="username">
                        <i data-lucide="user-round"></i>
                        اسم المستخدم
                    </label>
                    <div class="input-group">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            required
                            autofocus
                            placeholder="أدخل اسم المستخدم"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="login-field">
                    <label class="login-label" for="password">
                        <i data-lucide="lock"></i>
                        كلمة المرور
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="أدخل كلمة المرور">
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="عرض/إخفاء كلمة المرور">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember + forgot -->
                <div class="login-extras">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        تذكرني
                    </label>
                    <a href="#" class="forgot-link" onclick="forgotPass(event)">نسيت كلمة المرور؟</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="login-submit" id="submitBtn">
                    <i data-lucide="log-in" id="submitIcon"></i>
                    <span>تسجيل الدخول</span>
                </button>
            </form>

            <!-- Trust -->
            <div class="login-trust">
                <span><i data-lucide="check-circle-2"></i> اتصال آمن</span>
                <span><i data-lucide="check-circle-2"></i> تشفير BCrypt</span>
                <span><i data-lucide="check-circle-2"></i> CSRF محمي</span>
            </div>

        </div>
    </main>
</div>

<script>
// ===== Init Lucide =====
if (window.lucide) lucide.createIcons();

// ===== Toggle password visibility =====
(function () {
    const toggle = document.getElementById('togglePass');
    const input  = document.getElementById('password');
    if (!toggle || !input) return;

    toggle.addEventListener('click', function () {
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        toggle.innerHTML = isPass ? '<i data-lucide="eye-off"></i>' : '<i data-lucide="eye"></i>';
        if (window.lucide) lucide.createIcons();
        input.focus();
    });
})();

// ===== Loading state on submit =====
(function () {
    const form = document.getElementById('loginForm');
    const btn  = document.getElementById('submitBtn');
    if (!form || !btn) return;

    form.addEventListener('submit', function () {
        btn.classList.add('loading');
        btn.querySelector('span').textContent = 'جاري الدخول...';
        const icon = btn.querySelector('svg, i');
        if (icon) {
            icon.outerHTML = '<i data-lucide="loader-circle"></i>';
            if (window.lucide) lucide.createIcons();
        }
    });
})();

// ===== Forgot password info =====
function forgotPass(e) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'استعادة كلمة المرور',
            html: 'لأسباب أمنية، لا يمكن استعادة كلمة المرور تلقائيًا.<br>يرجى التواصل مع مطور الموقع.',
            confirmButtonText: 'حسنًا',
            confirmButtonColor: '#6B1D23'
        });
    } else {
        alert('لأسباب أمنية، يرجى التواصل مع مطور الموقع لاستعادة كلمة المرور.');
    }
}

// ===== Subtle parallax on mouse (desktop only) =====
if (window.matchMedia('(min-width: 961px)').matches) {
    const panel = document.querySelector('.brand-panel');
    const blobs = [];
    if (panel) {
        document.addEventListener('mousemove', function (e) {
            const x = (e.clientX / window.innerWidth - 0.5) * 2;
            const y = (e.clientY / window.innerHeight - 0.5) * 2;
            panel.style.setProperty('--mx', x);
            panel.style.setProperty('--my', y);
            panel.style.transform = `translate(${x * 6}px, ${y * 4}px)`;
        });
    }
}
</script>
</body>
</html>