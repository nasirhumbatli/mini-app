<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_auth();
?>
<!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <title>Qeydiyyat Siyahısı</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="m-0">Qeydiyyat Siyahısı</h1>
        <div class="d-flex gap-2">
            <button id="btnXlsx" class="btn btn-outline-success btn-sm">Export XLSX</button>
            <button id="btnPdf" class="btn btn-outline-danger  btn-sm">Export PDF</button>
            <form action="logout.php" class="col-6 m-auto" method="POST">
                <input type="hidden" name="csrf_token" value="<?= protect_xss(csrf_token()) ?>">
                <button type="submit" class="btn btn-dark">Çıxış</button>
            </form>
        </div>
    </div>

    <div class="card p-3 shadow-sm">
        <table id="datatable" class="table table-striped w-100">
            <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Created At</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script src="assets/js/ss-datatable.js"></script>
</body>
</html>

