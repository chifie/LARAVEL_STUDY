<?php
declare(strict_types=1);

class Database
{
    private static $pdo = null;

    public static function pdo()
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $driver = db_config('driver', 'mysql');
        $host = db_config('host', '127.0.0.1');
        $port = db_config('port', '3307');
        $database = db_config('database', '');
        $username = db_config('username', 'root');
        $password = db_config('password', '');
        $charset = db_config('charset', 'utf8mb4');

        if ($driver !== 'mysql') {
            throw new RuntimeException('Only MySQL is supported.');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

        try {
            self::$pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (app_config('env', 'production') === 'local') {
                die('Database connection failed: ' . $e->getMessage());
            }

            die('Database connection error.');
        }

        return self::$pdo;
    }

    public static function query($sql, array $params = [])
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public static function beginTransaction()
    {
        return self::pdo()->beginTransaction();
    }

    public static function commit()
    {
        return self::pdo()->commit();
    }

    public static function rollBack()
    {
        return self::pdo()->rollBack();
    }

    public static function lastInsertId()
    {
        return self::pdo()->lastInsertId();
    }
}