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