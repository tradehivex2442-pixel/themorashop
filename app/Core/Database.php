<?php
// ============================================================
// THEMORA SHOP — Core Database (PDO Wrapper)
// ============================================================

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $driver = config('database.driver', 'mysql');
            $host   = config('database.host', 'localhost');
            $port   = config('database.port', $driver === 'pgsql' ? '5432' : '3306');
            $dbname = config('database.database', 'themora_shop');
            $user   = config('database.username', 'root');
            $pass   = config('database.password', '');

            if ($driver === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
            } else {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            }

            try {
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                
                if ($driver === 'mysql') {
                    $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4";
                }

                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                if (env('APP_DEBUG') === 'true') {
                    die('Database connection failed: ' . $e->getMessage());
                }
                die('Database connection error. Please try again later.');
            }
        }

        return self::$instance;
    }

    // Convenience: prepare & execute
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchOne(string $sql, array $params = []): array|false
    {
        return self::query($sql, $params)->fetch();
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $sql, array $params = []): string
    {
        self::query($sql, $params);
        return self::getInstance()->lastInsertId();
    }

    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }

    public static function commit(): void
    {
        self::getInstance()->commit();
    }

    public static function rollback(): void
    {
        self::getInstance()->rollBack();
    }
}
