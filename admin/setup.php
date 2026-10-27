<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$count = (int)$pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

if ($count === 0) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO admin_users (username, password) VALUES (?, ?)');
    $stmt->execute(['admin', $hash]);
    echo '<h2 style="font-family:sans-serif;text-align:center;margin-top:80px">✅ تم إنشاء حساب المدير</h2>';
    echo '<p style="text-align:center;font-family:sans-serif">اسم المستخدم: <b>admin</b><br>كلمة المرور: <b>admin123</b><br><br><a href="login.php">اذهب لصفحة الدخول →</a></p>';
} else {
    echo '<h2 style="text-align:center;font-family:sans-serif;margin-top:80px">⚠️ يوجد مدير بالفعل. <a href="login.php">دخول</a></h2>';
}