<?php
declare(strict_types=1);

require_once __DIR__.'/helpers.php';
require_once __DIR__.'/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Ancaq Post sorğularına icazə verilir', [], 405);
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    json_error('CSRF token doğru deyil', [], 419);
}

[$email, $password, $errors] = validate_login_form($_POST);
if ($errors) {
    json_error('Doğrulama xətası', $errors, 422);
}

if (!attempt_login($email, $password)) {

    json_error('Login məlumatları səhvdir.', [
        'email' => 'Düzgün email və ya şifrə daxil edin'
    ], 401);
}

json_success('Giriş uğurludur.', ['redirect' => 'list.php']);
