<?php
// controllers/recibir_noticias.php

require_once __DIR__ . '/../db/db.php';

$pdo = Database::conectar();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST requerido']);
    exit;
}

// Recibir JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

$idGenero = (int) ($data['id_genero'] ?? 0);
$noticias = $data['noticias'] ?? [];

if ($idGenero <= 0 || empty($noticias)) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$sql = "INSERT INTO noticias 
        (id_genero, titulo, descripcion, imagen, fecha_creacion)
        VALUES (:id_genero, :titulo, :descripcion, :imagen, NOW())";

$stmt = $pdo->prepare($sql);

$insertadas = 0;

foreach ($noticias as $n) {
    $stmt->execute([
        ':id_genero' => $idGenero,
        ':titulo' => $n['titulo'] ?? '',
        ':descripcion' => $n['descripcion'] ?? '',
        ':imagen' => $n['imagen'] ?? ''
    ]);
    $insertadas++;
}

echo json_encode([
    'status' => 'ok',
    'insertadas' => $insertadas
]);




// HOLA RUBEN