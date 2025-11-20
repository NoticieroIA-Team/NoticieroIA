<?php
// controllers/noticias_controller.php

require_once "db/db.php";

$db = Database::conectar();                     // MongoDB\Database
$coleccion = $db->selectCollection('noticias');

// Obtenemos todas las noticias ordenadas por id DESC
$cursor = $coleccion->find(
    [],
    ['sort' => ['id' => -1]]
);

$noticias = [];

// Recorremos el cursor y montamos un array similar al fetch_assoc()
foreach ($cursor as $doc) {
    // Pasar BSONDocument a array asociativo
    $row = json_decode(json_encode($doc), true);

    // Aseguramos claves básicas (por si acaso)
    $id               = $row['id']               ?? null;
    $titulo           = $row['titulo']           ?? '';
    $descripcion      = $row['descripcion']      ?? '';
    $imagen           = $row['imagen']           ?? '';
    $noticia_revisada = $row['noticia_revisada'] ?? null; // puede ser bool o int
    $imagen_revisada  = $row['imagen_revisada']  ?? null;
    $publicado        = $row['publicado']        ?? null;
    $fecha_publicacion = $row['fecha_publicacion'] ?? null;

    // Normalizamos a algo comparable (0/1 o true/false)
    $nr = $noticia_revisada;
    $ir = $imagen_revisada;
    $pb = $publicado;

    // noticia_estado
    if ($nr === 0 || $nr === '0' || $nr === false) {
        $noticia_estado = 'Pendiente';
    } elseif ($nr === 1 || $nr === '1' || $nr === true) {
        $noticia_estado = 'Revisada';
    } else {
        $noticia_estado = '';
    }

    // imagen_estado
    if ($ir === 0 || $ir === '0' || $ir === false) {
        $imagen_estado = 'Pendiente';
    } elseif ($ir === 1 || $ir === '1' || $ir === true) {
        $imagen_estado = 'Aprobada';
    } else {
        $imagen_estado = '';
    }

    // publicado_estado
    if ($pb === 0 || $pb === '0' || $pb === false) {
        $publicado_estado = 'Borrador';
    } elseif ($pb === 1 || $pb === '1' || $pb === true) {
        $publicado_estado = 'Publicado';
    } else {
        $publicado_estado = '';
    }

    // Si fecha_publicacion es UTCDateTime, la convertimos a DateTime o string
    if ($fecha_publicacion instanceof MongoDB\BSON\UTCDateTime) {
        $fecha_publicacion_php = $fecha_publicacion->toDateTime();
    } else {
        $fecha_publicacion_php = $fecha_publicacion; // null o lo que venga
    }

    // Montamos el array como si fuera una fila de MySQL
    $noticias[] = [
        'id'                => $id,
        'titulo'            => $titulo,
        'descripcion'       => $descripcion,
        'imagen'            => $imagen,
        'noticia_revisada'  => $noticia_revisada,
        'imagen_revisada'   => $imagen_revisada,
        'publicado'         => $publicado,
        'fecha_publicacion' => $fecha_publicacion_php,
        'noticia_estado'    => $noticia_estado,
        'imagen_estado'     => $imagen_estado,
        'publicado_estado'  => $publicado_estado,
    ];
}

// Cargamos la vista
require_once "views/noticias_view.phtml";
