<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
?>
<!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <title>Admin Giriş</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Admin Giriş</h1>

    <div id="alert" class="alert d-none" role="alert"></div>

    <form id="loginForm" class="card p-4 shadow-sm" method="post">
        <input type="hidden" name="csrf_token" value="<?= protect_xss(csrf_token()) ?>">

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input name="email" id="email" type="email" class="form-control" required>
            <small id="emailError" class="form-text text-danger"></small>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input name="password" id="password" type="password" class="form-control" required placeholder="*******">
            <small id="passwordError" class="form-text text-danger"></small>
        </div>

        <button id="btnLogin" class="btn btn-primary" type="submit">Daxil ol</button>
    </form>
</div>
</body>
</html>
