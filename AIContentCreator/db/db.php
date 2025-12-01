<?php
// db/db.php

class Database
{
    private static $host = 'localhost';
    private static $db   = 'AIContentCreator';   // NOMBRE DE TU BD
    private static $user = 'root';              // USUARIO (en XAMPP suele ser 'root')
    private static $pass = '';                  // PASSWORD (en XAMPP por defecto suele estar vacía)
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
            // Mejor que devolver null: paramos la ejecución con un mensaje claro
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }
}
