<?php
require_once __DIR__ . '/db/db.php';

$pdo = Database::conectar();

if ($pdo) {
    echo "Conexión OK";
}
