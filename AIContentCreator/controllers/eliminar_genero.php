<?php
require_once __DIR__ . '/../db/db.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?controller=home&action=index");
    exit;
}

if (!isset($_POST['id'])) {
    die("Error: falta el ID.");
}

$id = intval($_POST['id']);

$pdo = Database::conectar();

try {
    // Eliminar registro por id_genero
    $stmt = $pdo->prepare("DELETE FROM planificacioncontenido WHERE id_genero = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        die("Error: no existe un registro con ese ID.");
    }

} catch (Exception $e) {
    die("Error al eliminar: " . $e->getMessage());
}

header("Location: ../index.php?controller=home&action=index&deleted=1");
exit;
