<?php
// controllers/articulos_controller.php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=start");
    exit;
}

$usuario = $_SESSION['usuario'];

require_once __DIR__ . '/../db/db.php';

$pdo = Database::conectar();

// Leer id_genero desde la URL
$id_genero = isset($_GET['id_genero']) ? (int)$_GET['id_genero'] : 0;
if ($id_genero <= 0) {
    die("Género inválido.");
}

// 1) Obtenemos datos del género (para mostrar el tema en la cabecera)
$sqlGenero = "SELECT id_genero, tema, descripcion 
              FROM planificacioncontenido
              WHERE id_genero = :id_genero";

$stmtGenero = $pdo->prepare($sqlGenero);
$stmtGenero->execute([':id_genero' => $id_genero]);
$genero = $stmtGenero->fetch(PDO::FETCH_ASSOC);

if (!$genero) {
    die("No se encontró el género seleccionado.");
}

// 2) Obtenemos todas las noticias asociadas a ese género
$sqlNoticias = "SELECT 
                    id,
                    id_genero,
                    titulo,
                    descripcion,
                    imagen,
                    noticia_revisada,
                    imagen_revisada,
                    publicado,
                    fecha_publicacion,
                    fecha_creacion
                FROM noticias
                WHERE id_genero = :id_genero
                ORDER BY id DESC";

$stmtNoticias = $pdo->prepare($sqlNoticias);
$stmtNoticias->execute([':id_genero' => $id_genero]);
$noticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

// Normalizamos estados a minúsculas (para que cuadren con los <option value="pendiente"... etc.)
foreach ($noticias as &$n) {
    $n['noticia_revisada'] = $n['noticia_revisada'] !== null ? strtolower(trim($n['noticia_revisada'])) : null;
    $n['imagen_revisada']  = $n['imagen_revisada']  !== null ? strtolower(trim($n['imagen_revisada']))  : null;
    $n['publicado']        = $n['publicado']        !== null ? strtolower(trim($n['publicado']))        : null;
}
unset($n);

// Cargar la vista específica por género
require __DIR__ . '/../views/articulos_view.phtml';
