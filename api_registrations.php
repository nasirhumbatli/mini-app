<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

header('Content-Type: application/json; charset=UTF-8');

if (!is_authed()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

ob_start();

try {
    $pdo = DB::getInstance()->pdo();

    $draw = $_GET['draw'] ?? 0;
    $start = max(0, $_GET['start'] ?? 0);
    $length = $_GET['length'] ?? 10;
    $length = $length > 0 ? min($length, 100) : 10;
    $search = trim($_GET['search']['value'] ?? ($_GET['search[value]'] ?? ''));

    $columns = ['id', 'full_name', 'email', 'company', 'created_at'];
    $orderColIdx = (int)($_GET['order']['0']['column'] ?? ($_GET['order[column]'] ?? 0));
    $orderBy = $columns[$orderColIdx] ?? 'id';
    $orderDir = strtolower($_GET['order']['0']['dir'] ?? ($_GET['order[dir]'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

    $where = '';
    $params = [];
    if ($search !== '') {
        $like = "%{$search}%";
        $where = "WHERE (full_name LIKE :q1 OR email LIKE :q2 OR COALESCE(company,'') LIKE :q3)";
        $params[':q1'] = $like;
        $params[':q2'] = $like;
        $params[':q3'] = $like;
    }

    $total = (int)$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

    if ($where !== '') {
        $st = $pdo->prepare("SELECT COUNT(*) FROM registrations {$where}");
        $st->execute($params);
        $filtered = (int)$st->fetchColumn();
    } else {
        $filtered = $total;
    }

    $sql = "SELECT id, full_name, email, company, created_at FROM registrations {$where} ORDER BY {$orderBy} {$orderDir} LIMIT :start, :length";
    $st = $pdo->prepare($sql);
    foreach ($params as $k => $v) $st->bindValue($k, $v, PDO::PARAM_STR);
    $st->bindValue(':start', $start, PDO::PARAM_INT);
    $st->bindValue(':length', $length, PDO::PARAM_INT);
    $st->execute();
    $rows = $st->fetchAll();

    $extra = ob_get_clean();
    if ($extra !== '') {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Non-JSON output detected', 'extra' => $extra]);
        exit;
    }

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $total,
        'recordsFiltered' => $filtered,
        'data' => $rows,
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
