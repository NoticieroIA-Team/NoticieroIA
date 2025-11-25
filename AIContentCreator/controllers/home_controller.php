<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=start");
    exit;
}

$usuario = $_SESSION['usuario'];

require_once __DIR__ . '/../db/db.php';

$pdo = Database::conectar();

$sql = "SELECT 
            id_genero,
            tema,
            descripcion,
            frecuencia,
            cantidad,
            idioma
        FROM planificacioncontenido
        ORDER BY id_genero DESC";

$stmt = $pdo->query($sql);
$generos = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

require "views/home_view.phtml";
