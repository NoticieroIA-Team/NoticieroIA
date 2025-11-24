<?php
// controllers/guardar_genero.php

require_once __DIR__ . '/../db/db.php';

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?controller=home&action=index');
    exit;
}

// --------------------------------------------
// CONEXIÓN A LA BASE DE DATOS
// --------------------------------------------
$conexion = Database::conectar();

if (!$conexion) {
    die("Error de conexión a la base de datos.");
}

// --------------------------------------------
// RECIBIR DATOS DEL FORMULARIO
// --------------------------------------------
$tema        = isset($_POST['tema']) ? trim($_POST['tema']) : null;
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$frecuencia  = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : null;
$cantidad    = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : null;
$addSources  = isset($_POST['addSources']) ? trim($_POST['addSources']) : 'no';
$idioma      = isset($_POST['idioma']) ? trim($_POST['idioma']) : null;

// En el formulario el hidden se llama "fuentes"
$sources = isset($_POST['fuentes']) && $_POST['fuentes'] !== '' ? $_POST['fuentes'] : null;

// --------------------------------------------
// VALIDACIONES BÁSICAS
// --------------------------------------------

// Campos obligatorios
if (!$tema || !$descripcion || !$frecuencia || !$cantidad || !$idioma) {
    $conexion->close();
    die("Error: faltan campos obligatorios.");
}

// Validar cantidad > 0
if (!is_int($cantidad) || $cantidad <= 0) {
    $conexion->close();
    die("Error: la cantidad debe ser un número entero mayor que 0.");
}

// Validar JSON si existen fuentes (opcional)
if ($sources !== null) {
    json_decode($sources);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Si el JSON es inválido, lo ignoramos para no romper el flujo
        $sources = null;
    }
}

// --------------------------------------------
// INSERTAR EN LA BD (MYSQLI PREPARED STATEMENT)
// --------------------------------------------
// OJO: aquí la columna se llama `sources` (como en tu BBDD)
$query = "INSERT INTO planificacioncontenido 
          (tema, descripcion, frecuencia, cantidad, addSources, idioma, sources)
          VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($query);

if (!$stmt) {
    $error = $conexion->error;
    $conexion->close();
    die("Error en prepare: " . $error);
}

// Vincular parámetros: s = string, i = int
$stmt->bind_param(
    "sssisss",
    $tema,
    $descripcion,
    $frecuencia,
    $cantidad,
    $addSources,
    $idioma,
    $sources
);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    $conexion->close();
    die("Error al insertar: " . $error);
}

$stmt->close();
$conexion->close();

// --------------------------------------------
// ENVIAR GÉNERO A N8N
// --------------------------------------------
$payload = [
    'tema'        => $tema,
    'descripcion' => $descripcion,
    'frecuencia'  => $frecuencia,
    'cantidad'    => $cantidad,
    'addSources'  => $addSources,
    'idioma'      => $idioma,
    'sources'     => $sources,
    'created_at'  => date('Y-m-d H:i:s'),
];

// Usa la URL TEST del webhook (igual que en auth.php)
$n8n_url = 'https://digital-n8n.owolqd.easypanel.host/webhook-test/from-php-generos';

$ch = curl_init($n8n_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    // Si usas token en n8n, descomenta y pon el mismo valor que validas en el Webhook:
    // 'X-API-KEY: TU_TOKEN_SECRETO',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// LOG opcional de errores hacia n8n (no rompemos la app si falla)
if ($httpCode < 200 || $httpCode >= 300 || $curlError) {
    error_log("Error enviando genero a n8n: HTTP $httpCode — RESPUESTA: $response — cURL: $curlError");
}

// --------------------------------------------
// REDIRECCIÓN DESPUÉS DE INSERTAR
// --------------------------------------------
header("Location: ../index.php?controller=home&action=index");
exit;
