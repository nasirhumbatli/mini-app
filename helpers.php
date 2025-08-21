<?php
declare(strict_types=1);

session_start();

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function csrf_verify(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function protect_xss(?string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES);
}

function json_success(string $message): void
{
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $message, array $fields = [], int $code = 400): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $message, 'fields' => $fields], JSON_UNESCAPED_UNICODE);
    exit;
}

function validate_registration_form(array $data): array
{
    $fullName = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $company = trim($data['company'] ?? '');

    $errors = [];

    if ($fullName === '' || mb_strlen($fullName) > 120) $errors['full_name'] = 'Full name qeyd olunmalıdır və maksimum 120 simvoldan ibarət ola bilər.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) $errors['email'] = 'E-poçt doğru şəkildə qeyd olunmalıdır və maksimum 120 simvoldan ibarət olar bilər.';
    if ($company !== '' && mb_strlen($company) > 120) $errors['company'] = 'Company maksimum 120 simvoldan ibarət ola bilər.';

    return [$fullName, $email, $company, $errors];
}