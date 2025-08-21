<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

require_auth();

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

$sheet = new Spreadsheet();
$ws = $sheet->getActiveSheet();
$ws->fromArray(['ID', 'Full Name', 'Email', 'Company', 'Created At'], null, 'A1');

$r = 2;
foreach ($rows as $row) {
    $ws->setCellValue("A{$r}", $row['id']);
    $ws->setCellValue("B{$r}", $row['full_name']);
    $ws->setCellValue("C{$r}", $row['email']);
    $ws->setCellValue("D{$r}", $row['company']);
    $ws->setCellValue("E{$r}", $row['created_at']);
    $r++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="registrations.xlsx"');

$writer = new Xlsx($sheet);
$writer->save('php://output');
