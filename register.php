<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Ancaq Post sorğularına icazə verilir', [], 405);
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    json_error('CSRF token doğru deyil', [], 419);
}

[$fullName, $email, $company, $errors] = validate_registration_form($_POST);
if ($errors) {
    json_error('Doğrulama xətası', $errors, 422);
}

$db = DB::getInstance();

try {
    $exists = $db->selectOne('SELECT id FROM registrations WHERE email = :email', [':email' => $email]);

    if ($exists) {
        json_error('Bu email artıq mövcuddur.', ['email' => 'Email unikal olmalıdır'], 422);
    }

    $db->insert('INSERT INTO registrations (full_name,email,company) VALUES (:fullname,:email,:company)',
        [':fullname' => $fullName, ':email' => $email, ':company' => $company]);

    Mailer::notifyAdmin($fullName, $email, $company);

    json_success('Qeydiyyat tamamlandı');

} catch (Throwable $e) {
    json_error('Operation error.', ['errors' => $e->getMessage()], 422);
}