<?php
// db/db.php

class Database
{
    private static $host = 'localhost';
    private static $db   = '';
    private static $user = '';
    private static $pass = '';
    private static $charset = 'utf8mb4';

    public static function conectar()
    {
        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;

        try {
            $pdo = new PDO($dsn, self::$user, self::$pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            return null;
        }
    }
}
