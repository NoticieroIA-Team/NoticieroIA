<?php
require_once 'db/db.php';

echo "<pre>";
try {
    $pdo = Database::conectar();
    echo "Conexión OK\n";
} catch (Exception $e) {
    echo "Fallo al conectar:\n";
    echo $e->getMessage();
}
echo "</pre>";
