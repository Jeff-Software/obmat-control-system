<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

require_once('../config/conexion.php');
require_once('../config/config_global.php');

// Paginación
$registrosPorPagina = 8;

$pagina = isset($_GET['pagina']) 
    ? intval($_GET['pagina']) 
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$where = [];

if (!empty($_GET['desde'])) {

    $desde = $_GET['desde'];

    $where[] = "DATE(m.fecha) >= '$desde'";
}

if (!empty($_GET['hasta'])) {

    $hasta = $_GET['hasta'];

    $where[] = "DATE(m.fecha) <= '$hasta'";
}

// Construir condición WHERE
$whereSQL = "";

if (!empty($where)) {
    $whereSQL = " WHERE " . implode(" AND ", $where);
}

// Contar registros
$queryTotal = "
SELECT COUNT(*) AS total
FROM movimientos_stock m
INNER JOIN productos p
ON m.producto_id = p.id
$whereSQL
";

$totalRegistros = $conexion
    ->query($queryTotal)
    ->fetch_assoc()['total'];

$totalPaginas = ceil($totalRegistros / $registrosPorPagina);


// Evitar páginas inexistentes
if ($pagina > $totalPaginas && $totalPaginas > 0) {
    $pagina = $totalPaginas;
}


// Offset
$inicio = ($pagina - 1) * $registrosPorPagina;

$query = "
SELECT
    m.id,
    p.nombre,
    m.tipo,
    m.cantidad,
    m.motivo,
    m.fecha
FROM movimientos_stock m
INNER JOIN productos p
    ON m.producto_id = p.id
";

if (!empty($where)) {

    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= "
ORDER BY m.fecha DESC
LIMIT $inicio, $registrosPorPagina
";

$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= __('movimientos_inventario') ?></title>

<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/movimientos_stock.css">
<link rel="stylesheet" href="../assets/css/paginacion.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include('../modulos/sidebar.php'); ?>

<main class="main-content">

    <div class="movimientos-card">

        <header class="dashboard-header">
            <h2><?= __('movimientos_inventario') ?></h2>
        </header>

        <form method="GET" class="filtro-fechas">

        <div class="campo-filtro">
            <label><?= __('desde') ?></label>
            <input
                type="date"
                name="desde"
                value="<?= $_GET['desde'] ?? '' ?>"
            >
        </div>

        <div class="campo-filtro">
            <label><?= __('hasta') ?></label>
            <input
                type="date"
                name="hasta"
                value="<?= $_GET['hasta'] ?? '' ?>"
            >
        </div>

        <button type="submit" class="btn-filtrar">
            <?= __('filtrar') ?>
        </button>

        <a href="movimientos_stock.php" class="btn-limpiar">
            <?= __('limpiar') ?>
        </a>

    </form>

        <table class="tabla-stock">

            <thead>
                <tr>
                <th><?= __('fecha') ?></th>
                <th><?= __('producto') ?></th>
                <th><?= __('tipo') ?></th>
                <th><?= __('cantidad') ?></th>
                <th><?= __('motivo') ?></th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = $resultado->fetch_assoc()): ?>

                <tr>

                    <td><?= $row['fecha'] ?></td>

                    <td><?= htmlspecialchars($row['nombre']) ?></td>

                    <td>
                        <?php if($row['tipo'] == 'entrada'): ?>
                            <span class="entrada">
                            🟢 <?= __('entrada') ?>
                            </span>
                        <?php else: ?>
                            <span class="salida">
                            🔴 <?= __('salida') ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td><?= $row['cantidad'] ?></td>

                    <td><?= htmlspecialchars($row['motivo']) ?></td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>
<?php if ($totalPaginas > 1): ?>

<div class="paginacion">

    <!-- Botón anterior -->
    <?php if ($pagina > 1): ?>
        <a href="?pagina=<?= $pagina - 1 ?>&desde=<?= $_GET['desde'] ?? '' ?>&hasta=<?= $_GET['hasta'] ?? '' ?>">
            «
        </a>
    <?php endif; ?>


    <!-- Números de página -->
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>

        <a 
            class="<?= $i == $pagina ? 'activa' : '' ?>"
            href="?pagina=<?= $i ?>&desde=<?= $_GET['desde'] ?? '' ?>&hasta=<?= $_GET['hasta'] ?? '' ?>">
            <?= $i ?>
        </a>

    <?php endfor; ?>


    <!-- Botón siguiente -->
    <?php if ($pagina < $totalPaginas): ?>
        <a href="?pagina=<?= $pagina + 1 ?>&desde=<?= $_GET['desde'] ?? '' ?>&hasta=<?= $_GET['hasta'] ?? '' ?>">
            »
        </a>
    <?php endif; ?>

</div>
<?php endif; ?>


    </div>

</main>

</body>
</html>