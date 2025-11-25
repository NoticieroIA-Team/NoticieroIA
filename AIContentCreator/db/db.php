<?php

class Database {

    public static function conectar()
    {
        // Leer .env de la raíz del proyecto
        if (file_exists(__DIR__ . '/../.env')) {
            $env = parse_ini_file(__DIR__ . '/../.env');
        } else {
            $env = [];
        }

        $host   = $env['DB_HOST'] ?? 'localhost';
        $dbname = $env['DB_NAME'] ?? 'AIContentCreator';
        $user   = $env['DB_USER'] ?? 'root';
        $pass   = $env['DB_PASS'] ?? '';

        try {
            $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';

            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $pdo = new PDO($dsn, $user, $pass, $opciones);

            return $pdo;

        } catch (PDOException $e) {
            die('Error de conexión a MySQL: ' . $e->getMessage());
        }
    }
}
