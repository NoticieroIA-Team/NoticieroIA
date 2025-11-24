<?php
// controllers/noticias_controller.php

// Endpoint del workflow en n8n (PRODUCTION URL del Webhook)
// Webhook node:
//   HTTP Method: POST
//   Path: from-php-generos
$endpoint = 'https://digital-n8n.owolqd.easypanel.host/webhook/from-php-generos';

// (Opcional) Payload por si el workflow lo quiere usar para decidir la acción
$payload = [
    'accion' => 'listar_noticias'
];

// ---- Llamada HTTP POST a n8n ----
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die('Error al conectar con el servicio de noticias (n8n): ' . htmlspecialchars($error));
}

curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
    die('Error al obtener noticias desde n8n. Código HTTP: ' . (int) $httpCode);
}

// ---- Decodificar JSON devuelto por n8n ----
$noticias = json_decode($response, true);
if (!is_array($noticias)) {
    die('Respuesta inválida desde n8n al obtener las noticias.');
}

// ---- Normalizar campos y generar estados como hacía el SQL original ----
foreach ($noticias as &$fila) {
    $fila['id'] = $fila['id'] ?? null;
    $fila['titulo'] = $fila['titulo'] ?? '';
    $fila['descripcion'] = $fila['descripcion'] ?? '';
    $fila['imagen'] = $fila['imagen'] ?? null;
    $fila['noticia_revisada'] = $fila['noticia_revisada'] ?? '';
    $fila['imagen_revisada'] = $fila['imagen_revisada'] ?? '';
    $fila['publicado'] = $fila['publicado'] ?? '';
    $fila['fecha_publicacion'] = $fila['fecha_publicacion'] ?? null;

    // noticia_estado
    switch ($fila['noticia_revisada']) {
        case 'Pendiente':
            $fila['noticia_estado'] = 'Pendiente';
            break;
        case 'Revisada':
            $fila['noticia_estado'] = 'Revisada';
            break;
        default:
            $fila['noticia_estado'] = '';
            break;
    }

    // imagen_estado
    switch ($fila['imagen_revisada']) {
        case 'Pendiente':
            $fila['imagen_estado'] = 'Pendiente';
            break;
        case 'Aprobada':
            $fila['imagen_estado'] = 'Aprobada';
            break;
        default:
            $fila['imagen_estado'] = '';
            break;
    }

    // publicado_estado (solo cuando noticia = Revisada e imagen = Aprobada)
    if ($fila['noticia_revisada'] === 'Revisada' && $fila['imagen_revisada'] === 'Aprobada') {
        $fila['publicado_estado'] = 'Publicado';
    } else {
        $fila['publicado_estado'] = '';
    }
}
unset($fila);

// Ordenar por id DESC como hacía el ORDER BY id DESC
usort($noticias, function ($a, $b) {
    return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
});

// ---- Adaptador para que la vista pueda seguir usando $result->fetch_assoc() ----
class ArrayResult
{
    private array $rows;
    private int $index = 0;

    public function __construct(array $rows)
    {
        // Reindexar por si vienen claves no numéricas
        $this->rows = array_values($rows);
    }

    public function fetch_assoc()
    {
        if ($this->index >= count($this->rows)) {
            return null;
        }
        return $this->rows[$this->index++];
    }
}

// $result se comporta como el resultado de mysqli->query()
$result = new ArrayResult($noticias);

// Cargamos la vista (sigue usando $result->fetch_assoc())
require_once __DIR__ . '/../views/noticias_view.phtml';
