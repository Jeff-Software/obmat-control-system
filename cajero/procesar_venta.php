<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../config/conexion.php');

$config = $conexion
    ->query("
        SELECT
            sonido_ventas,
            redondeo_totales
        FROM configuracion
        WHERE id = 1
    ")
    ->fetch_assoc();

try {

    $datos = json_decode(file_get_contents('php://input'), true);

    $carrito = $datos['carrito'] ?? [];
    $descuento_pct = $datos['descuento'] ?? 0;
    $metodo_pago = $datos['metodo_pago'] ?? '';
    $usuario = $_SESSION['usuario'] ?? '';

    if (empty($carrito) || empty($metodo_pago)) {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Datos incompletos'
        ]);
        exit();
    }

    /* BUSCAR USUARIO */

    $stmt = $conexion->prepare("
        SELECT id
        FROM usuarios
        WHERE usuario = ?
    ");

    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Usuario no encontrado'
        ]);
        exit();
    }

    $usuario_id = $user['id'];

    /* VERIFICAR STOCK */

    foreach ($carrito as $item) {

        $stockStmt = $conexion->prepare("
            SELECT stock,nombre,estado
            FROM productos
            WHERE id = ?
            
            
        ");

        $stockStmt->bind_param(
            "i",
            $item['id']
        );

        $stockStmt->execute();

        $producto = $stockStmt->get_result()->fetch_assoc();

        if (!$producto) {
            throw new Exception(
                "Producto no encontrado"
            );
        }

        if ($producto['estado'] == 0) {
            throw new Exception(
                "El producto '" . $producto['nombre'] . "' está inactivo"
            );
        }

        if ($producto['stock'] < $item['cantidad']) {
            throw new Exception(
                "Stock insuficiente para: " . $producto['nombre']
            );
        }
    }

    $subtotal = array_sum(
        array_map(
            fn($p) => $p['precio'] * $p['cantidad'],
            $carrito
        )
    );

    $descuento = $subtotal * ($descuento_pct / 100);
    $total = $subtotal - $descuento;

    if (!empty($config['redondeo_totales'])) {
        $total = round($total);
}
    /* INICIAR TRANSACCIÓN */

    $conexion->begin_transaction();

    /* GUARDAR VENTA */

    $stmt = $conexion->prepare("
        INSERT INTO ventas
        (usuario_id, total, metodo_pago, fecha)
        VALUES (?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "ids",
        $usuario_id,
        $total,
        $metodo_pago
    );

    $stmt->execute();

    $venta_id = $conexion->insert_id;

    /* GUARDAR DETALLE Y DESCONTAR STOCK */

    foreach ($carrito as $item) {

        $stmt = $conexion->prepare("
            INSERT INTO detalle_ventas
            (id_venta, producto_id, cantidad, precio_unitario)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiid",
            $venta_id,
            $item['id'],
            $item['cantidad'],
            $item['precio']
        );

        $stmt->execute();

        $update = $conexion->prepare("
            UPDATE productos
            SET stock = stock - ?
            WHERE id = ?
        ");

        $mov = $conexion->prepare("
        INSERT INTO movimientos_stock
        (
            producto_id,
            tipo,
            cantidad,
            motivo
        )
        VALUES
        (
            ?,
            'salida',
            ?,
            ?
        )
    ");

    $motivo = "Venta #".$venta_id;

    $mov->bind_param(
        "iis",
        $item['id'],
        $item['cantidad'],
        $motivo
    );

    $mov->execute();

        $update->bind_param(
            "ii",
            $item['cantidad'],
            $item['id']
        );

        $update->execute();
    }

    /* CONFIRMAR TRANSACCIÓN */

    $conexion->commit();

    echo json_encode([
        'success' => true,
        'venta_id' => $venta_id,
        'sonido_ventas' => (bool)$config['sonido_ventas']
    ]);

} catch (Exception $e) {

    $conexion->rollback();

    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}