<?php
include 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

$id_lengua = $_GET['id_lengua'] ?? null;
$id_municipio = $_GET['id_municipio'] ?? null;

// CASO 1: Seleccionaron una lengua -> Traer municipios asociados mediante la tabla intermedia
if ($id_lengua) {
    $id_lengua = (int)$id_lengua;
    $sql = "SELECT DISTINCT m.id_municipio, m.nombre 
            FROM municipio m
            INNER JOIN variante_municipio vm ON m.id_municipio = vm.id_municipio
            INNER JOIN variante v ON vm.id_variante = v.id_variante
            WHERE v.id_lengua = $id_lengua
            ORDER BY m.nombre ASC";
            
    $result = mysqli_query($conn, $sql);
    $datos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $datos[] = $row;
    }
    echo json_encode($datos);
    exit;
}

// CASO 2: Seleccionaron un municipio -> Traer lenguas asociadas
if ($id_municipio) {
    $id_municipio = (int)$id_municipio;
    $sql = "SELECT DISTINCT l.id_lengua, l.nombre 
            FROM lengua l
            INNER JOIN variante v ON l.id_lengua = v.id_lengua
            INNER JOIN variante_municipio vm ON v.id_variante = vm.id_variante
            WHERE vm.id_municipio = $id_municipio
            ORDER BY l.nombre ASC";
            
    $result = mysqli_query($conn, $sql);
    $datos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $datos[] = $row;
    }
    echo json_encode($datos);
    exit;
}

// Si no se manda ningún parámetro, regresamos todos los municipios por defecto
$sqlAll = "SELECT id_municipio, nombre FROM municipio ORDER BY nombre ASC";
$resultAll = mysqli_query($conn, $sqlAll);
$datosAll = [];
while ($row = mysqli_fetch_assoc($resultAll)) {
    $datosAll[] = $row;
}
echo json_encode($datosAll);