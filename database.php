<?php
declare(strict_types=1);

class DB
{
    private PDO $pdo;
    private static $instance;

    public function __construct()
    {
        $env = parse_ini_file(__DIR__ . '/.env');

        $connection = $env['DB_CONNECTION'] ?? 'mysql';
        $host = $env['DB_HOST'] ?? 'localhost';
        $port = $env['DB_PORT'] ?? '3306';
        $db = $env['DB_DATABASE'] ?? '';
        $user = $env['DB_USERNAME'] ?? '';
        $password = $env['DB_PASSWORD'] ?? '';
        $charset = 'utf8mb4';

        $dataSourceNames = "{$connection}:host={$host};port={$port};dbname={$db};charset={$charset}";

        try {
            $this->pdo = new PDO($dataSourceNames, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            json_error('Database connection error', ['errors' => $e->getMessage()], 422);
            die("PDO Error: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function selectOne(string $sql, array $params = []): ?array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function insert(string $query, array $params = []): false|string
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $this->pdo->lastInsertId();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}