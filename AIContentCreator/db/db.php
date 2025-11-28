<?php
// db/db.php

class Database
{
    private static $host = 'digital_ia_content_generator';      // o 'localhost'
    private static $port = '3306';           // o el que veas en XAMPP
    private static $db   = 'dbgenerator';    // nombre de tu BD
    private static $user = 'dominion';           // <- USUARIO root
    private static $pass = 'Dominion@2411';               // <- CONTRASEÑA VACÍA en XAMPP por defecto
    private static $charset = 'utf8mb4';

    public static function conectar()
    {
        $dsn = "mysql:host=" . self::$host .
               ";port=" . self::$port .
               ";dbname=" . self::$db .
               ";charset=" . self::$charset;

        try {
            $pdo = new PDO($dsn, self::$user, self::$pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;

        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
