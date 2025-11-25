<?php
// controllers/noticias_controller.php

require_once __DIR__ . '/../db/db.php';

// -----------------------------
// Obtener todas las noticias desde MySQL (PDO)
// -----------------------------
$pdo = Database::conectar();

$sql = "SELECT 
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
        ORDER BY id DESC";

$stmt = $pdo->query($sql);

$noticias = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Normalizar estados a minúsculas
    $row['noticia_revisada'] = $row['noticia_revisada'] !== null ? strtolower($row['noticia_revisada']) : null;
    $row['imagen_revisada']  = $row['imagen_revisada']  !== null ? strtolower($row['imagen_revisada'])  : null;
    $row['publicado']        = $row['publicado']        !== null ? strtolower($row['publicado'])        : null;

    $noticias[] = $row;
}

// -----------------------------
// Adaptador tipo mysqli_result
// -----------------------------
class ArrayResult
{
    public $num_rows;
    private $rows;
    private $index = 0;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
        $this->num_rows = count($rows);
    }

    public function fetch_assoc()
    {
        if ($this->index >= $this->num_rows) {
            return null;
        }
        return $this->rows[$this->index++];
    }
}

$result = new ArrayResult($noticias);

// -----------------------------
// Cargar la vista
// -----------------------------
require_once __DIR__ . '/../views/noticias_view.phtml';
