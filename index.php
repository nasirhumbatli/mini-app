<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="col-6 m-auto">
                <div id="formAlert" class="d-none alert-danger" role="alert">
                    A simple success alert—check it out!
                </div>
            </div>
            <form id="registrationForm" class="col-6 m-auto" method="POST">
                <input type="hidden" name="csrf_token" value="<?= protect_xss(csrf_token()) ?>">
                <div class="form-group">
                    <label for="fullName" class="fw-bold">Full name</label>
                    <input type="text" class="form-control" id="fullName" name="full_name" required>
                    <small id="fullNameError" class="form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="email" class="fw-bold">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <small id="emailError" class="form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="company" class="fw-bold">Company
                        <span>(optional)</span>
                    </label>
                    <input type="text" class="form-control" id="company" name="company">
                    <small id="companyError" class="form-text text-danger"></small>
                </div>
                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-dark">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/registration.js"></script>
</body>
</html>