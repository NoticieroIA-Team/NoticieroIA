<?php
// controllers/guardar_genero.php

require_once __DIR__ . '/../db/db.php';

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?controller=home');
    exit;
}

// --------------------------------------------
// CONEXIÓN A LA BASE DE DATOS (PDO)
// --------------------------------------------
$pdo = Database::conectar();

if (!$pdo) {
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

// NUEVO: tipo de llamada para el switch del webhook
$tipo_llamada = 'genero';

// En el formulario el hidden se llama "fuentes"
$sources = isset($_POST['fuentes']) && $_POST['fuentes'] !== '' ? $_POST['fuentes'] : null;

// --------------------------------------------
// VALIDACIONES BÁSICAS
// --------------------------------------------
if (!$tema || !$descripcion || !$frecuencia || !$cantidad || !$idioma) {
    die("Error: faltan campos obligatorios.");
}

if (!is_int($cantidad) || $cantidad <= 0) {
    die("Error: la cantidad debe ser un número entero mayor que 0.");
}

if ($sources !== null) {
    json_decode($sources);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $sources = null;
    }
}

// --------------------------------------------
// INSERTAR EN LA BD (PDO)
// + columna tipo_llamada
// --------------------------------------------
$sql = "INSERT INTO planificacioncontenido 
        (tema, descripcion, frecuencia, cantidad, addSources, idioma, sources, tipo_llamada)
        VALUES (:tema, :descripcion, :frecuencia, :cantidad, :addSources, :idioma, :sources, :tipo_llamada)";

try {
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':tema'         => $tema,
        ':descripcion'  => $descripcion,
        ':frecuencia'   => $frecuencia,
        ':cantidad'     => $cantidad,
        ':addSources'   => $addSources,
        ':idioma'       => $idioma,
        ':sources'      => $sources,
        ':tipo_llamada' => $tipo_llamada
    ]);
} catch (PDOException $e) {
    die("Error al insertar: " . $e->getMessage());
}

// --------------------------------------------
// OBTENER id_genero RECIÉN INSERTADO
// --------------------------------------------
$id_genero = $pdo->lastInsertId();

// --------------------------------------------
// ENVIAR GÉNERO A N8N
// Webhook ÚNICO + tipo_llamada
// --------------------------------------------
$payload = [
    'tipo_llamada' => $tipo_llamada,   // ← CLAVE PARA EL SWITCH EN N8N
    'id_genero'    => (int)$id_genero,
    'tema'         => $tema,
    'descripcion'  => $descripcion,
    'frecuencia'   => $frecuencia,
    'cantidad'     => $cantidad,
    'addSources'   => $addSources,
    'idioma'       => $idioma,
    'sources'      => $sources,
    'created_at'   => date('Y-m-d H:i:s'),
];

// PON AQUÍ EL WEBHOOK ÚNICO QUE USARÁS PARA TODO
// (cámbialo también en el controlador de artículos)
$n8n_url = 'https://digital-n8n.owolqd.easypanel.host/webhook-test/from-php-noticiero';

$ch = curl_init($n8n_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300 || $curlError) {
    error_log("Error enviando género a n8n: HTTP $httpCode — RESPUESTA: $response — cURL: $curlError");
}

// --------------------------------------------
// REDIRECCIÓN DESPUÉS DE INSERTAR
// --------------------------------------------
header("Location: ../index.php?controller=home");
exit;
