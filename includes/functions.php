<?php
// includes/functions.php

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function getAllSettings(PDO $pdo): array {
    $rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
    $out = [];
    foreach ($rows as $r) $out[$r['setting_key']] = $r['setting_value'];
    return $out;
}

function getSetting(PDO $pdo, string $key, string $default = ''): string {
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function checkCsrf(): void {
    $token = $_POST['_csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        die('Invalid CSRF token');
    }
}
// ========== Pagination Helpers ==========

function filterUrl(string $base, array $override = []): string {
    $params = [];
    foreach (['q', 'status', 'page'] as $k) {
        if (isset($_GET[$k]) && $_GET[$k] !== '' && $_GET[$k] !== 'all') {
            $params[$k] = $_GET[$k];
        }
    }
    foreach ($override as $k => $v) {
        if ($v === null || $v === '') unset($params[$k]);
        else $params[$k] = $v;
    }
    return $base . ($params ? '?' . http_build_query($params) : '');
}

function currentFilters(array $allowed = ['q', 'status']): array {
    $out = [];
    foreach ($allowed as $k) {
        if (isset($_GET[$k]) && $_GET[$k] !== '' && $_GET[$k] !== 'all') {
            $out[$k] = $_GET[$k];
        }
    }
    return $out;
}

function paginationRange(int $current, int $total, int $window = 1): array {
    $pages = [];
    $start = max(1, $current - $window);
    $end   = min($total, $current + $window);
    if ($start > 1) { $pages[] = 1; if ($start > 2) $pages[] = '...'; }
    for ($i = $start; $i <= $end; $i++) $pages[] = $i;
    if ($end < $total) { if ($end < $total - 1) $pages[] = '...'; $pages[] = $total; }
    return $pages;
}

/**
 * يحسب pagination جاهز
 * @return array{total:int,page:int,totalPages:int,offset:int,perPage:int}
 */
function buildPagination(int $total, int $perPage = 10): array {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $totalPages = max(1, (int)ceil($total / $perPage));
    if ($page > $totalPages) $page = $totalPages;
    return [
        'total'      => $total,
        'page'       => $page,
        'totalPages' => $totalPages,
        'offset'     => ($page - 1) * $perPage,
        'perPage'    => $perPage,
    ];
}

function renderPagination(string $baseUrl, array $pg): void {
    if ($pg['totalPages'] <= 1) {
        echo '<div class="pagination-info" style="padding:14px 18px;background:#fff;border-radius:12px;border:1px solid rgba(107,29,35,.08);font-size:.85rem;color:rgba(42,27,28,.65);margin-bottom:22px">إجمالي <strong style="color:var(--maroon)">' . $pg['total'] . '</strong> عنصر</div>';
        return;
    }
    $filters = currentFilters();
    echo '<div class="pagination-wrap">';
    echo '<div class="pagination-info">عرض <strong>' . ($pg['offset'] + 1) . '</strong>–<strong>' . min($pg['offset'] + $pg['perPage'], $pg['total']) . '</strong> من أصل <strong>' . $pg['total'] . '</strong></div>';
    echo '<div class="pagination">';

    // Prev
    if ($pg['page'] > 1) {
        echo '<a href="' . htmlspecialchars(filterUrl($baseUrl, ['page' => $pg['page'] - 1])) . '" class="page-btn">« السابق</a>';
    } else {
        echo '<span class="page-btn disabled">« السابق</span>';
    }

    foreach (paginationRange($pg['page'], $pg['totalPages']) as $p) {
        if ($p === '...') {
            echo '<span class="page-ellipsis">…</span>';
        } elseif ($p === $pg['page']) {
            echo '<span class="page-num active">' . $p . '</span>';
        } else {
            echo '<a href="' . htmlspecialchars(filterUrl($baseUrl, ['page' => $p])) . '" class="page-num">' . $p . '</a>';
        }
    }

    // Next
    if ($pg['page'] < $pg['totalPages']) {
        echo '<a href="' . htmlspecialchars(filterUrl($baseUrl, ['page' => $pg['page'] + 1])) . '" class="page-btn">التالي »</a>';
    } else {
        echo '<span class="page-btn disabled">التالي »</span>';
    }

    echo '</div></div>';
}