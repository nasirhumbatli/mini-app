<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

require_auth();

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

$pdo = DB::getInstance()->pdo();

$search = trim($_GET['search']['value'] ?? ($_GET['search[value]'] ?? ''));
$columns = ['id', 'full_name', 'email', 'company', 'created_at'];
$orderColIdx = (int)($_GET['order']['0']['column'] ?? ($_GET['order[column]'] ?? 0));
$orderBy = $columns[$orderColIdx] ?? 'id';
$orderDir = strtolower($_GET['order']['0']['dir'] ?? ($_GET['order[dir]'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

$start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : null;
$length = isset($_GET['length']) ? max(1, min(1000, (int)$_GET['length'])) : null;

$where = '';
$params = [];
if ($search !== '') {
    $like = "%{$search}%";
    $where = "WHERE (full_name LIKE :q1 OR email LIKE :q2 OR COALESCE(company,'') LIKE :q3)";
    $params[':q1'] = $like;
    $params[':q2'] = $like;
    $params[':q3'] = $like;
}

$limitSql = $length !== null ? "LIMIT :start, :len" : "LIMIT 10000";

$sql = "SELECT id, full_name, email, company, created_at
        FROM registrations
        {$where}
        ORDER BY {$orderBy} {$orderDir}
         {$limitSql}";
$st = $pdo->prepare($sql);
foreach ($params as $k => $v) $st->bindValue($k, $v, PDO::PARAM_STR);
if ($length !== null) {
    $st->bindValue(':start', $start, PDO::PARAM_INT);
    $st->bindValue(':len', $length, PDO::PARAM_INT);
}
$st->execute();
$rows = $st->fetchAll();

ob_start();
?>
    <!doctype html>
    <html lang="az">
    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: DejaVu Sans, Arial, sans-serif;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 6px;
                font-size: 12px;
            }

            th {
                background: #f1f1f1;
            }

            h2 {
                margin: 0 0 10px;
            }
        </style>
    </head>
    <body>
    <h2>Registrations</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= protect_xss((string)$row['id']) ?></td>
                <td><?= protect_xss($row['full_name']) ?></td>
                <td><?= protect_xss($row['email']) ?></td>
                <td><?= protect_xss((string)$row['company']) ?></td>
                <td><?= protect_xss($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </body>
    </html>
<?php
$html = ob_get_clean();

$pdf = new Dompdf();
$pdf->loadHtml($html, 'UTF-8');
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream('registrations.pdf', ['Attachment' => true]);
