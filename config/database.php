<?php
declare(strict_types=1);

final class Database
{
    private PDO $pdo;

    public function __construct(
        string $host = "127.0.0.1",
        string $dbName = "revieweo",
        string $user = "root",
        string $password = ""
    ) {
        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
