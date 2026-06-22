<?php
require_once('../config/conexion.php');
require_once('../config/config_global.php'); 
/** @var array $configSistema */
// =====================================
// FILTROS
// =====================================

$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

// =====================================
// PAGINACIÓN
// =====================================

$registros_por_pagina = 10;

$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registros_por_pagina;

// =====================================
// TOTAL DE REGISTROS
// =====================================

$stmtTotal = $conexion->prepare("
    SELECT COUNT(*) AS total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
");

$stmtTotal->bind_param(
    "ss",
    $fecha_desde,
    $fecha_hasta
);

$stmtTotal->execute();

$totalVentas = $stmtTotal
    ->get_result()
    ->fetch_assoc()['total'];

$totalPaginas = max(
    1,
    ceil($totalVentas / $registros_por_pagina)
);

// =====================================
// CONSULTA PRINCIPAL
// =====================================

$stmt = $conexion->prepare("
    SELECT
        v.id,
        v.fecha,
        v.total,
        v.metodo_pago,
        u.nombre AS nombre_cajero
    FROM ventas v
    INNER JOIN usuarios u
        ON v.usuario_id = u.id
    WHERE DATE(v.fecha) BETWEEN ? AND ?
    ORDER BY v.fecha DESC
    LIMIT ?, ?
");

$stmt->bind_param(
    "ssii",
    $fecha_desde,
    $fecha_hasta,
    $inicio,
    $registros_por_pagina
);

$stmt->execute();

$resultado = $stmt->get_result();
?>

<div class="content-wrapper">
    <form method="GET" class="filter-form">
        <div class="input-group">
            <label><?= __('fecha_desde') ?></label>
            <input type="date" name="fecha_desde" value="<?php echo $fecha_desde; ?>">
        </div>
        <div class="input-group">
            <label><?= __('fecha_hasta') ?></label>
            <input type="date" name="fecha_hasta" value="<?php echo $fecha_hasta; ?>">
        </div>
        <button class="btn-filtrar">
            <i class="fas fa-filter"></i> <?= __('filtrar') ?>
        </button>
    </form>

    <div class="table-container">
        <table class="table-modern">
            <thead>
                <tr>
                    <th><?= __('id') ?></th>
                    <th><?= __('fecha') ?></th>
                    <th><?= __('cajero') ?></th>
                    <th><?= __('total') ?></th>
                    <th><?= __('metodo') ?></th>
                    <th><?= __('acciones') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td>
                        <?php echo date(
                            'd/m/Y H:i',
                            strtotime($fila['fecha'])
                        ); ?>
                    </td>
                    <td><?php echo $fila['nombre_cajero']; ?></td>
                    <td class="total-col">
                        <?php echo $simboloMoneda . ' ' . number_format($fila['total'], 2); ?>
                    </td>
                    <td>
                    <span class="badge">

                    <?php

                    $metodo = strtolower($fila['metodo_pago']);

                    echo __($metodo);

                    ?>

                    </span>
                    </td>
                    <td>
                        <button class="btn-detalle"
                            onclick="abrirDetalle(<?php echo $fila['id']; ?>)">
                            <?= __('ver_detalle') ?>
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="pagination">

    <?php if($pagina > 1){ ?>
        <a href="?pagina=<?php echo $pagina-1; ?>&fecha_desde=<?php echo $fecha_desde; ?>&fecha_hasta=<?php echo $fecha_hasta; ?>">
            ← <?= __('anterior') ?>
        </a>
    <?php } ?>

    <?php for($i=1; $i<=$totalPaginas; $i++){ ?>

        <a
            href="?pagina=<?php echo $i; ?>&fecha_desde=<?php echo $fecha_desde; ?>&fecha_hasta=<?php echo $fecha_hasta; ?>"
            class="<?php echo ($i == $pagina) ? 'active' : ''; ?>"
        >
            <?php echo $i; ?>
        </a>

    <?php } ?>

    <?php if($pagina < $totalPaginas){ ?>
        <a href="?pagina=<?php echo $pagina+1; ?>&fecha_desde=<?php echo $fecha_desde; ?>&fecha_hasta=<?php echo $fecha_hasta; ?>">
            <?= __('siguiente') ?> →
        </a>
    <?php } ?>

</div>
    </div>
</div>

<div id="modalDetalle" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalDetalle').style.display='none'">&times;</span>
        <div id="contenidoDetalle">Cargando...</div>
    </div>
</div>

<script>
const LANG = {
    total: "<?= __('total') ?>",
    imprimir_ticket: "<?= __('imprimir_ticket') ?>",
    vuelva_pronto: "<?= __('vuelva_pronto') ?>",
    conserve_comprobante: "<?= __('conserve_comprobante') ?>",
    producto: "<?= __('producto') ?>",
    cantidad: "<?= __('cantidad') ?>",
    precio_unitario: "<?= __('precio_unitario') ?>",
    subtotal: "<?= __('subtotal') ?>",
    cargando: "<?= __('cargando') ?>",
    gracias_compra: "<?= __('gracias_compra') ?>",
    fecha_ticket: "<?= __('fecha_ticket') ?>",
    cajero_ticket: "<?= __('cajero_ticket') ?>",
    pago_ticket: "<?= __('pago_ticket') ?>"

};

const SIMBOLO_MONEDA = "<?= $configSistema['simbolo'] ?>";
</script>

<script>
function abrirDetalle(id) {
    const modal = document.getElementById('modalDetalle');
    const container = document.getElementById('contenidoDetalle');
    modal.style.display = 'block';
    container.innerHTML = "Cargando...";

    fetch('../admin/detalle_venta.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                container.innerHTML = "<p>Error: " + data.error + "</p>";
                return;
            }
            
            // Inicio del diseño tipo Ticket
            let info = data.info;
            let html = `
                <div style="text-align: center; border-bottom: 2px dashed #000; margin-bottom: 10px;">
                    <h3>${info.negocio}</h3>
                    <p>${LANG.gracias_compra}</p>
                </div>
                <div style="font-size: 0.9em; margin-bottom: 10px;">
                    <p><strong>${LANG.fecha_ticket}:</strong> ${info.fecha}</p>
                    <p><strong>${LANG.cajero_ticket}:</strong> ${info.cajero}</p>
                    <p><strong>${LANG.pago_ticket}:</strong> ${info.metodo ? info.metodo.toUpperCase() : 'N/A'}</p>
                </div>
                <table style="width:100%; border-top: 1px solid #000; border-bottom: 1px solid #000; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #000;">
                            <th style="text-align:left; padding:5px;">Prod</th>
                            <th style="text-align:center; padding:5px;">Cant</th>
                            <th style="text-align:right; padding:5px;">Prec</th>
                            <th style="text-align:right; padding:5px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>`;

            // Listado de productos
            if (data.productos && data.productos.length > 0) {
                data.productos.forEach(p => {
                    html += `<tr>
                                <td style="padding:5px;">${p.nombre}</td>
                                <td style="text-align:center; padding:5px;">${p.cantidad}</td>
                                <td style="text-align:right; padding:5px;">${p.precio}</td>
                                <td style="text-align:right; padding:5px;">${p.subtotal}</td>
                             </tr>`;
                });
            } else {
                html += `<tr><td colspan="4" style="text-align:center; padding: 10px;">Sin detalles</td></tr>`;
            }

            // Pie del ticket con el botón de imprimir integrado
            html += `</tbody></table>
                    <div style="text-align: right; margin-top: 10px;">
                        <h3>${LANG.total}: ${SIMBOLO_MONEDA} ${data.total}</h3>
                    </div>

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="../modulos/generar_ticket.php?id=${id}" target="_blank"
                        style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                        <i class="fas fa-print"></i> ${LANG.imprimir_ticket}
                        </a>
                    </div>

                    <div style="text-align: center; margin-top: 20px; font-size: 0.8em;">
                        <p>${LANG.vuelva_pronto}</p>
                        <p>${LANG.conserve_comprobante}</p>
                    </div>`;
                        
            // Finalmente, actualiza el modal
            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = "Error al cargar los datos: " + err;
        });
}
</script>