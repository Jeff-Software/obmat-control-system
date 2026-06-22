<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once('../config/conexion.php');
require_once('../config/config_global.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$limite = 3;

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $limite;

if ($id <= 0) {
    die("ID de producto inválido");
}

/* ==========================
   PROCESAR INGRESO DE STOCK
========================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cantidad = intval($_POST['cantidad']);
    $motivo   = trim($_POST['motivo']);

    if ($cantidad > 0 && !empty($motivo)) {

        $conexion->begin_transaction();

        try {

            // Actualizar stock
            $stmt = $conexion->prepare("
                UPDATE productos
                SET stock = stock + ?
                WHERE id = ?
            ");

            $stmt->bind_param("ii", $cantidad, $id);
            $stmt->execute();

            // Registrar movimiento
            $stmt = $conexion->prepare("
                INSERT INTO movimientos_stock
                (producto_id, tipo, cantidad, motivo)
                VALUES (?, 'entrada', ?, ?)
            ");

            $stmt->bind_param(
                "iis",
                $id,
                $cantidad,
                $motivo
            );

            $stmt->execute();

            $conexion->commit();

            header("Location: stock_producto.php?id=$id&ok=1");
            exit();

        } catch (Exception $e) {

            $conexion->rollback();

            die("Error: " . $e->getMessage());
        }
    }
}

/* ==========================
   OBTENER PRODUCTO
========================== */

$stmt = $conexion->prepare("
    SELECT id, nombre, stock
    FROM productos
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$producto = $stmt->get_result()->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado");
}

/* ==========================
   HISTORIAL DE MOVIMIENTOS
========================== */

$stmtMov = $conexion->prepare("
    SELECT *
    FROM movimientos_stock
    WHERE producto_id = ?
    ORDER BY fecha DESC
    LIMIT ?, ?
");

$stmtMov->bind_param("iii", $id, $inicio, $limite);
$stmtMov->execute();

$resultMovimientos = $stmtMov->get_result();
$stmtCount = $conexion->prepare("
    SELECT COUNT(*) as total
    FROM movimientos_stock
    WHERE producto_id = ?
");

$stmtCount->bind_param("i", $id);
$stmtCount->execute();

$total = $stmtCount->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total / $limite);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= __('ingreso_stock') ?></title>

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/stock_producto.css">
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include('../modulos/sidebar.php'); ?>

<main class="main-content">

    <div class="stock-card">

    <?php if(isset($_GET['ok'])): ?>
    <div class="mensaje-ok">
        ✅ <?= __('stock_actualizado') ?>
    </div>
    <?php endif; ?>

        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>

        <div class="stock-actual">
            <?= __('stock_actual') ?>:
            <strong><?= $producto['stock'] ?></strong>
        </div>

        <form method="POST">

            <div class="form-group">
                <label><?= __('cantidad_ingresar') ?></label>

                <input
                    type="number"
                    name="cantidad"
                    min="1"
                    required
                >
            </div>

            <div class="form-group">
                <label><?= __('motivo') ?></label>

                <input
                    type="text"
                    name="motivo"
                    placeholder="Reposición proveedor"
                    required
                >
            </div>

            <button type="submit" class="btn-guardar">
                <?= __('guardar_movimiento') ?>
            </button>

        </form>
        <hr>

<div class="stock-history">
    <h3><?= __('historial_movimientos') ?></h3>

    <table class="tabla-movimientos">
        <thead>
            <tr>
            <th><?= __('fecha') ?></th>
            <th><?= __('tipo') ?></th>
            <th><?= __('cantidad') ?></th>
            <th><?= __('motivo') ?></th>
            </tr>
        </thead>

        <tbody>

        <?php while($mov = $resultMovimientos->fetch_assoc()): ?>

            <tr>
                <td><?= $mov['fecha'] ?></td>

                <td>
                    <?= $mov['tipo'] == 'entrada'
                    ? '🟢 ' . __('entrada')
                    : '🔴 ' . __('salida')
                    ?>
                </td>

                <td><?= $mov['cantidad'] ?></td>

                <td><?= htmlspecialchars($mov['motivo']) ?></td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

    <div class="paginacion">

    <?php if ($pagina > 1): ?>
        <a href="?id=<?= $id ?>&pagina=<?= $pagina - 1 ?>">«</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <a href="?id=<?= $id ?>&pagina=<?= $i ?>"
           class="<?= $i == $pagina ? 'activa' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina < $total_paginas): ?>
        <a href="?id=<?= $id ?>&pagina=<?= $pagina + 1 ?>">»</a>
    <?php endif; ?>

</div>
    </div>

    </div>

</main>

</body>
</html>