<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function is_authed(): bool {
    return !empty($_SESSION['auth']) && $_SESSION['auth'] === true;
}

function require_auth(): void {
    if (!is_authed()) {
        header('Location: login.php');
        exit;
    }
}

function attempt_login(string $email, string $password): bool {
    if ($email === 'admin@example.com' && $password === '123456') {
        session_regenerate_id(true);
        $_SESSION['auth'] = true;
        $_SESSION['user'] = ['email' => $email];
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}