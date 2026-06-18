<?php

require_once('../config/conexion.php');


// ======================
// DATOS DEL NEGOCIO
// ======================

$nombre_negocio = trim($_POST['nombre_negocio'] ?? '');
$ruc            = trim($_POST['ruc'] ?? '');
$direccion      = trim($_POST['direccion'] ?? '');
$telefono       = trim($_POST['telefono'] ?? '');
$correo         = trim($_POST['correo'] ?? '');
$sitio_web      = trim($_POST['sitio_web'] ?? '');
$descripcion    = trim($_POST['descripcion'] ?? '');

// ======================
// CONFIGURACIÓN REGIONAL
// ======================

$pais           = $_POST['pais'] ?? 'PE';
$zona_horaria   = $_POST['zona_horaria'] ?? 'America/Lima';
$moneda         = $_POST['moneda'] ?? 'PEN';
switch ($moneda) {

    case 'USD':
        $simbolo_moneda = '$';
        break;

    case 'EUR':
        $simbolo_moneda = '€';
        break;

    default:
        $simbolo_moneda = 'S/';
        break;
}
$idioma         = $_POST['idioma'] ?? 'es';

// ======================
// PREFERENCIAS
// ======================

$stock_cero         = isset($_POST['stock_cero']) ? 1 : 0;
$confirmar_eliminar = isset($_POST['confirmar_eliminar']) ? 1 : 0;
$sonido_ventas      = isset($_POST['sonido_ventas']) ? 1 : 0;
$redondeo_totales   = isset($_POST['redondeo_totales']) ? 1 : 0;
$confirmar_cancelar_venta = isset($_POST['confirmar_cancelar_venta']) ? 1 : 0;

// ======================
// UPDATE
// ======================

$sql = "UPDATE configuracion SET

    nombre_negocio = ?,
    ruc = ?,
    direccion = ?,
    telefono = ?,
    correo = ?,
    sitio_web = ?,
    descripcion = ?,

    pais = ?,
    zona_horaria = ?,
    moneda = ?,
    simbolo_moneda = ?,
    idioma = ?,

    stock_cero = ?,
    confirmar_eliminar = ?,
    sonido_ventas = ?,
    redondeo_totales = ?,
    confirmar_cancelar_venta = ?

WHERE id = 1";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    echo "error: " . $conexion->error;
    exit;

}

$stmt->bind_param(
    "ssssssssssssiiiii",

    $nombre_negocio,
    $ruc,
    $direccion,
    $telefono,
    $correo,
    $sitio_web,
    $descripcion,

    $pais,
    $zona_horaria,
    $moneda,
    $simbolo_moneda,
    $idioma,

    $stock_cero,
    $confirmar_eliminar,
    $sonido_ventas,
    $redondeo_totales,
    $confirmar_cancelar_venta
);

if ($stmt->execute()) {

    echo "exito";

} else {

    echo "error: " . $stmt->error;

}

$stmt->close();
$conexion->close();