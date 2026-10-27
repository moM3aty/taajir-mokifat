<?php
// admin/profile.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// ============ Handle password change ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $current = $_POST['current'] ?? '';
    $new     = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id=?');
    $stmt->execute([$_SESSION['admin_user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password'])) {
        flash('error', 'كلمة المرور الحالية غير صحيحة');
    } elseif (mb_strlen($new) < 6) {
        flash('error', 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل');
    } elseif ($new !== $confirm) {
        flash('error', 'تأكيد كلمة المرور غير مطابق');
    } elseif ($new === $current) {
        flash('error', 'كلمة المرور الجديدة يجب أن تكون مختلفة عن الحالية');
    } else {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE admin_users SET password=? WHERE id=?')->execute([$hash, $user['id']]);
        flash('success', 'تم تغيير كلمة المرور بنجاح');
    }
    header('Location: profile.php');
    exit;
}

// ============ Load user info ============
$stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id=?');
$stmt->execute([$_SESSION['admin_user_id']]);
$user = $stmt->fetch();

$pageTitle = 'حسابي';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== Profile Page ===== */
.profile-hero {
    background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-deep) 60%, #2b0a0e 100%);
    border-radius: 18px;
    padding: 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(107,29,35,.4);
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: -60%; left: -10%;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(212,181,92,.25), transparent 65%);
    pointer-events: none;
}
.profile-hero-inner {
    position: relative; z-index: 2;
    display: flex; align-items: center; gap: 22px;
    flex-wrap: wrap;
}
.profile-avatar-large {
    width: 84px; height: 84px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
    border: 3px solid rgba(212,181,92,.5);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Cairo'; font-size: 2.2rem; font-weight: 800;
    flex-shrink: 0;
    text-transform: uppercase;
}
.profile-info { min-width: 0; }
.profile-info h2 {
    font-size: 1.5rem;
    color: #fff;
    margin: 0 0 4px;
    font-weight: 800;
}
.profile-info p {
    margin: 0;
    color: rgba(255,255,255,.72);
    font-size: .92rem;
}
.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    background: rgba(212,181,92,.15);
    border: 1px solid rgba(212,181,92,.35);
    color: var(--gold-light);
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 700;
    margin-top: 10px;
}
.profile-badge svg { width: 12px; height: 12px; }

/* ===== Password input toggles ===== */
.password-input-wrap { position: relative; }
.password-input-wrap input { padding-left: 44px; }
.password-toggle {
    position: absolute;
    top: 50%; left: 10px;
    transform: translateY(-50%);
    background: none; border: 0; cursor: pointer;
    color: rgba(42,27,28,.4);
    display: flex; padding: 6px;
    border-radius: 6px;
    transition: color .2s, background .2s;
}
.password-toggle:hover {
    color: var(--maroon);
    background: rgba(107,29,35,.06);
}
.password-toggle svg { width: 18px; height: 18px; }

/* ===== Strength meter ===== */
.strength-meter {
    display: flex;
    gap: 4px;
    margin-top: 8px;
}
.strength-bar {
    flex: 1;
    height: 4px;
    border-radius: 999px;
    background: rgba(42,27,28,.1);
    transition: background .3s;
}
.strength-bar.weak   { background: var(--red); }
.strength-bar.medium { background: #F9A825; }
.strength-bar.strong { background: var(--green); }
.strength-text {
    font-size: .75rem;
    font-weight: 600;
    margin-top: 6px;
    color: rgba(42,27,28,.55);
}

/* ===== Account info list ===== */
.account-info { display: flex; flex-direction: column; }
.account-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 13px 0;
    border-bottom: 1px solid rgba(107,29,35,.06);
    font-size: .9rem;
    gap: 12px;
}
.account-info-item:last-child { border-bottom: 0; }
.account-info-item strong {
    color: var(--ink);
    font-weight: 600;
    flex-shrink: 0;
}
.account-info-item span {
    color: rgba(42,27,28,.65);
    text-align: left;
}
.account-info-item span.maroon {
    color: var(--maroon);
    font-weight: 700;
}

/* ===== Tips ===== */
.tips-list { display: flex; flex-direction: column; gap: 10px; }
.tip-item {
    display: flex;
    gap: 10px;
    padding: 12px 14px;
    background: rgba(176,141,47,.06);
    border-radius: 10px;
    border-right: 3px solid var(--gold);
    font-size: .85rem;
    color: rgba(42,27,28,.75);
    line-height: 1.6;
}
.tip-item svg {
    width: 16px; height: 16px;
    color: var(--gold);
    flex-shrink: 0;
    margin-top: 3px;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 22px;
}
.profile-side { display: flex; flex-direction: column; gap: 22px; }

@media (max-width: 900px) {
    .profile-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .profile-hero { padding: 24px; }
    .profile-avatar-large { width: 64px; height: 64px; font-size: 1.7rem; }
    .profile-info h2 { font-size: 1.2rem; }
}
</style>

<!-- Hero -->
<div class="profile-hero">
    <div class="profile-hero-inner">
        <div class="profile-avatar-large">
            <?= htmlspecialchars(mb_substr($user['username'] ?? 'A', 0, 1)) ?>
        </div>
        <div class="profile-info">
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p>مرحباً بك في حسابك الشخصي بلوحة التحكم</p>
            <div class="profile-badge">
                <i data-lucide="shield-check"></i>
                <span>مدير الموقع</span>
            </div>
        </div>
    </div>
</div>

<!-- Two columns -->
<div class="profile-grid">

    <!-- Change password -->
    <div class="card">
        <h3 class="card-title">
            <i data-lucide="key-round" style="width:18px;height:18px"></i>
            تغيير كلمة المرور
        </h3>
        <form method="post" id="passwordForm">
            <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
            <div class="form-grid" style="grid-template-columns:1fr">
                <label>كلمة المرور الحالية
                    <div class="password-input-wrap">
                        <input type="password" name="current" id="currentPass" required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePass('currentPass', this)" aria-label="عرض/إخفاء">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </label>

                <label>كلمة المرور الجديدة
                    <div class="password-input-wrap">
                        <input type="password" name="new" id="newPass" required minlength="6" autocomplete="new-password" oninput="checkStrength(this.value)">
                        <button type="button" class="password-toggle" onclick="togglePass('newPass', this)" aria-label="عرض/إخفاء">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <div class="strength-meter" id="strengthMeter">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">6 أحرف على الأقل</div>
                </label>

                <label>تأكيد كلمة المرور
                    <div class="password-input-wrap">
                        <input type="password" name="confirm" id="confirmPass" required autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePass('confirmPass', this)" aria-label="عرض/إخفاء">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </label>
            </div>
            <div style="margin-top:22px">
                <button class="btn btn-primary" type="submit">
                    <i data-lucide="save" style="width:16px;height:16px"></i>
                    حفظ كلمة المرور الجديدة
                </button>
            </div>
        </form>
    </div>

    <!-- Side -->
    <div class="profile-side">

        <!-- Account info -->
        <div class="card">
            <h3 class="card-title">
                <i data-lucide="user-round" style="width:18px;height:18px"></i>
                معلومات الحساب
            </h3>
            <div class="account-info">
                <div class="account-info-item">
                    <strong>اسم المستخدم</strong>
                    <span><?= htmlspecialchars($user['username']) ?></span>
                </div>
                <div class="account-info-item">
                    <strong>نوع الحساب</strong>
                    <span class="maroon">مدير الموقع</span>
                </div>
                <div class="account-info-item">
                    <strong>تاريخ الإنشاء</strong>
                    <span><?= date('Y/m/d', strtotime($user['created_at'])) ?></span>
                </div>
                <div class="account-info-item">
                    <strong>رقم الحساب</strong>
                    <span>#<?= (int)$user['id'] ?></span>
                </div>
            </div>
        </div>

        <!-- Security tips -->
        <div class="card">
            <h3 class="card-title">
                <i data-lucide="shield" style="width:18px;height:18px"></i>
                نصائح الأمان
            </h3>
            <div class="tips-list">
                <div class="tip-item">
                    <i data-lucide="check-circle-2"></i>
                    <span>استخدم كلمة مرور قوية تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز</span>
                </div>
                <div class="tip-item">
                    <i data-lucide="check-circle-2"></i>
                    <span>لا تشارك كلمة المرور مع أي شخص آخر</span>
                </div>
                <div class="tip-item">
                    <i data-lucide="check-circle-2"></i>
                    <span>غيّر كلمة المرور بشكل دوري كل 3–6 أشهر</span>
                </div>
                <div class="tip-item">
                    <i data-lucide="check-circle-2"></i>
                    <span>سجّل الخروج دائمًا عند استخدام جهاز مشترك</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass ? '<i data-lucide="eye-off"></i>' : '<i data-lucide="eye"></i>';
    if (window.lucide) lucide.createIcons();
}

function checkStrength(pass) {
    const bars = document.querySelectorAll('#strengthMeter .strength-bar');
    const text = document.getElementById('strengthText');
    bars.forEach(b => b.className = 'strength-bar');

    if (!pass) {
        text.textContent = '6 أحرف على الأقل';
        text.style.color = 'rgba(42,27,28,.55)';
        return;
    }

    let score = 0;
    if (pass.length >= 6)  score++;
    if (pass.length >= 10) score++;
    if (/[A-Z]/.test(pass) && /[a-z]/.test(pass)) score++;
    if (/\d/.test(pass))   score++;
    if (/[^A-Za-z0-9]/.test(pass)) score++;

    if (score <= 2) {
        bars[0].classList.add('weak');
        text.textContent = 'كلمة مرور ضعيفة';
        text.style.color = 'var(--red)';
    } else if (score <= 3) {
        bars[0].classList.add('medium');
        bars[1].classList.add('medium');
        text.textContent = 'كلمة مرور متوسطة';
        text.style.color = '#F9A825';
    } else {
        bars.forEach(b => b.classList.add('strong'));
        text.textContent = 'كلمة مرور قوية جدًا';
        text.style.color = 'var(--green)';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>