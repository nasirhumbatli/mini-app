<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Ancaq Post sorğularına icazə verilir', [], 405);
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    json_error('CSRF token doğru deyil', [], 419);
}

logout();
header('Location: login.php');
exit;