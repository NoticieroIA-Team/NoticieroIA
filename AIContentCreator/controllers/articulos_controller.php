<?php
// controllers/articulos_controller.php

require_once __DIR__ . '/../db/db.php';

$pdo = Database::conectar();

$idGenero = isset($_GET['id_genero']) ? (int) $_GET['id_genero'] : 0;

if ($idGenero <= 0) {
    die('id_genero no válido');
}

// 1) Llamar al webhook de n8n para pedir artículos
// ⚠️ IMPORTANTE: Sustituye ESTA URL por la Production URL completa de tu Webhook1 en n8n
$webhookUrl = 'https://digital-n8n.owolqd.easypanel.host/webhook/from-php-noticiero';

$payload = [
    'tipo_llamada' => 'articulos',
    'id_genero'    => $idGenero,
];

$ch = curl_init($webhookUrl);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
]);

// Ejecutar la petición
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// 📌 BLOQUE DE DEPURACIÓN — MUESTRA EL ERROR REAL
if ($httpCode !== 200 || $response === false) {
    echo "<pre>";
    echo "ERROR al llamar a n8n\n\n";
    echo "URL usada: $webhookUrl\n\n";
    echo "HTTP CODE: $httpCode\n\n";
    echo "cURL ERROR: $curlError\n\n";
    echo "RESPUESTA RAW:\n";
    var_dump($response);
    echo "</pre>";
    exit;
}

// Si OK, decodificar JSON normalmente
$data = json_decode($response, true);
$noticias = $data['noticias'] ?? [];

// 3) Montar la estructura $genero para la vista
$genero = [
    'tema'        => 'Género ' . $idGenero,
    'descripcion' => 'Artículos generados desde n8n para este género.',
];

// 4) Cargar la vista
require __DIR__ . '/../views/articulos_view.phtml';
