<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once('../config/conexion.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
");

$stmtMov->bind_param("i", $id);
$stmtMov->execute();

$resultMovimientos = $stmtMov->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso de Stock</title>

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
        ✅ Stock actualizado correctamente
    </div>
    <?php endif; ?>

        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>

        <div class="stock-actual">
            Stock actual:
            <strong><?= $producto['stock'] ?></strong>
        </div>

        <form method="POST">

            <div class="form-group">
                <label>Cantidad a ingresar</label>

                <input
                    type="number"
                    name="cantidad"
                    min="1"
                    required
                >
            </div>

            <div class="form-group">
                <label>Motivo</label>

                <input
                    type="text"
                    name="motivo"
                    placeholder="Reposición proveedor"
                    required
                >
            </div>

            <button type="submit" class="btn-guardar">
                Guardar movimiento
            </button>

        </form>
        <hr>

    <h3>Historial de movimientos</h3>

    <table class="tabla-movimientos">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
            </tr>
        </thead>

        <tbody>

        <?php while($mov = $resultMovimientos->fetch_assoc()): ?>

            <tr>
                <td><?= $mov['fecha'] ?></td>

                <td>
                    <?= $mov['tipo'] == 'entrada'
                        ? '🟢 Entrada'
                        : '🔴 Salida'
                    ?>
                </td>

                <td><?= $mov['cantidad'] ?></td>

                <td><?= htmlspecialchars($mov['motivo']) ?></td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

    </div>

</main>

</body>
</html>