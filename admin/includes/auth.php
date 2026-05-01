<?php
/**
 * Auth Helpers
 */
function auth_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ADMIN_SESS');
        session_start();
    }
}

function auth_check(): void {
    auth_start();
    if (empty($_SESSION[SESSION_KEY])) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function auth_user(): ?array {
    auth_start();
    return $_SESSION[SESSION_KEY] ?? null;
}

function auth_login(string $username, string $password): bool {
    $user = db_row('SELECT * FROM admin_users WHERE username = ? AND status = 1 AND password = ?', [$username,$password]);
    if ($user) {
        unset($user['password']);
        $_SESSION[SESSION_KEY] = $user;
        return true;
    }
    return false;
}

function auth_logout(): void {
    auth_start();
    session_destroy();
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

function csrf_token(): string {
    auth_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): bool {
    return isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
