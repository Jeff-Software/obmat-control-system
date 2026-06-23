<?php
require_once('../config/conexion.php');
require_once('../config/auth.php');
require_once('../config/traducir_logs.php');

$limite = 10;
$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if($pagina < 1){
    $pagina = 1;
}

$inicio = ($pagina - 1) * $limite;

$filtroUsuario = $_GET['usuario'] ?? '';
$filtroAccion = $_GET['accion'] ?? '';

$filtroDesde = $_GET['desde'] ?? '';
$filtroHasta = $_GET['hasta'] ?? '';

$where = [];

/* filtro usuario */
if(!empty($filtroUsuario)){
    $where[] = "l.usuario_id = " . (int)$filtroUsuario;
}

/* filtro acción */
if($filtroAccion == 'login'){
    $where[] = "l.accion LIKE '%Inicio de sesión%'";
}
elseif($filtroAccion == 'logout'){
    $where[] = "l.accion LIKE '%Cierre de sesión%'";
}
elseif($filtroAccion == 'crear'){
    $where[] = "l.accion LIKE '%Creó usuario%'";
}
elseif($filtroAccion == 'editar'){
    $where[] = "l.accion LIKE '%Editó usuario%'";
}
elseif($filtroAccion == 'estado'){
    $where[] = "l.accion LIKE '%Cambió estado%'";
}

/* filtro fecha desde */
if(!empty($filtroDesde)){
    $where[] = "DATE(l.fecha) >= '$filtroDesde'";
}

/* filtro fecha hasta */
if(!empty($filtroHasta)){
    $where[] = "DATE(l.fecha) <= '$filtroHasta'";
}



/*
|--------------------------------------------------------------------------
| RESUMEN
|--------------------------------------------------------------------------
*/
$sqlResumen = "
SELECT

COUNT(*) AS total_logs,

SUM(
    CASE
    WHEN accion LIKE '%Inicio de sesión%'
    THEN 1
    ELSE 0
    END
) AS total_logins,

SUM(
    CASE
    WHEN accion LIKE '%Creó usuario%'
    THEN 1
    ELSE 0
    END
) AS total_creaciones,

SUM(
    CASE
    WHEN accion LIKE '%Editó usuario%'
    THEN 1
    ELSE 0
    END
) AS total_ediciones,

SUM(
CASE
WHEN accion LIKE '%Cierre de sesión%'
THEN 1
ELSE 0
END
) AS total_logouts ,

SUM(
    CASE
    WHEN accion LIKE '%Cambió estado%'
    THEN 1
    ELSE 0
    END
) AS total_estados

FROM logs l
";

if(count($where) > 0){
    $sqlResumen .= "
    WHERE " . implode(" AND ", $where);
}

$resumen = $conexion
    ->query($sqlResumen)
    ->fetch_assoc();


$sqlUsuarios = "
SELECT id, nombre
FROM usuarios
ORDER BY nombre
";

$usuarios = $conexion->query($sqlUsuarios);

/* conteo total */
$sqlTotal = "
SELECT COUNT(*) total
FROM logs l
";

if(count($where) > 0){
    $sqlTotal .= " WHERE " . implode(" AND ", $where);
}
$totalLogs = $conexion
    ->query($sqlTotal)
    ->fetch_assoc()['total'];

$totalPaginas = ceil(
    $totalLogs / $limite
);

$rowUltimo = $conexion
->query("
SELECT fecha
FROM logs
ORDER BY fecha DESC
LIMIT 1
")
->fetch_assoc();

/*
|--------------------------------------------------------------------------
| LOGS
|--------------------------------------------------------------------------
*/
$sql = "
SELECT
    l.id,
    u.nombre,
    l.accion,
    l.fecha
FROM logs l
LEFT JOIN usuarios u
    ON l.usuario_id = u.id
";

if(count($where) > 0){
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= "
ORDER BY l.fecha DESC
LIMIT $inicio, $limite
";

$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Auditoría</title>

<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/logs.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<?php include('../modulos/sidebar.php'); ?>

<main class="main-content">

    <div class="logs-header">

        <h2>
            <i class="fas fa-clipboard-list"></i>
            <?= __('auditoria_sistema') ?>
        </h2>

        <p>
            <?= __('registro_actividades') ?>
        </p>

    </div>

    <!-- KPI -->

<div class="logs-kpis">

    <!-- Total -->

    <div class="log-card total">

        <div class="card-icon">
            <i class="fas fa-database"></i>
        </div>

        <div>
            <span><?= __('total_eventos') ?></span>
            <h3><?= $resumen['total_logs'] ?></h3>
        </div>

    </div>

    <!-- Login -->

    <div class="log-card login">

        <div class="card-icon">
            <i class="fas fa-sign-in-alt"></i>
        </div>

        <div>
            <span><?= __('inicios_sesion') ?></span>
            <h3><?= $resumen['total_logins'] ?></h3>
        </div>

    </div>

    <div class="log-card logout">

        <div class="card-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>

        <div>
            <span><?= __('cierres_sesion') ?></span>
            <h3><?= $resumen['total_logouts'] ?></h3>
        </div>

    </div>

    <!-- Usuarios creados -->

    <div class="log-card create">

        <div class="card-icon">
            <i class="fas fa-user-plus"></i>
        </div>

        <div>
            <span><?= __('usuarios_creados') ?></span>
            <h3><?= $resumen['total_creaciones'] ?></h3>
        </div>

    </div>

    <!-- Usuarios editados -->

    <div class="log-card edit">

        <div class="card-icon">
            <i class="fas fa-user-edit"></i>
        </div>

        <div>
            <span><?= __('usuarios_editados') ?></span>
            <h3><?= $resumen['total_ediciones'] ?></h3>
        </div>

    </div>

    <!-- Cambios estado -->

    <div class="log-card update">

        <div class="card-icon">
            <i class="fas fa-toggle-on"></i>
        </div>

        <div>
            <span><?= __('cambios_estado') ?></span>
            <h3><?= $resumen['total_estados'] ?></h3>
        </div>

    </div>

</div>

    <p class="contador-logs">
        <?= $totalLogs ?> <?= __('registros_encontrados') ?>
    </p>


    <form method="GET" class="filtros-logs">


    <select name="usuario">

        <option value="">
            <?= __('todos_usuarios') ?>
        </option>

        <?php while($u = $usuarios->fetch_assoc()): ?>

            <option
                value="<?= $u['id'] ?>"
                <?= $filtroUsuario == $u['id'] ? 'selected' : '' ?>>

                <?= htmlspecialchars($u['nombre']) ?>

            </option>

        <?php endwhile; ?>

    </select>

    <select name="accion">

    <option value="">
        <?= __('todas_acciones') ?>
    </option>

    <option value="login"
        <?= $filtroAccion == 'login' ? 'selected' : '' ?>>
        <?= __('accion_login') ?>
    </option>

    <option value="logout"
        <?= $filtroAccion == 'logout' ? 'selected' : '' ?>>
        <?= __('accion_logout') ?>
    </option>

    <option value="crear"
        <?= $filtroAccion == 'crear' ? 'selected' : '' ?>>
        <?= __('accion_crear') ?>
    </option>

    <option value="editar"
        <?= $filtroAccion == 'editar' ? 'selected' : '' ?>>
        <?= __('accion_editar') ?>
    </option>

    <option value="estado"
        <?= $filtroAccion == 'estado' ? 'selected' : '' ?>>
        <?= __('accion_estado') ?>
    </option>

</select>

    <input
    type="date"
    name="desde"
    value="<?= $filtroDesde ?>">

    <input
    type="date"
    name="hasta"
    value="<?= $filtroHasta ?>">
    

    <button type="submit" class="btn-filtrar">
        <?= __('filtrar') ?>
    </button>


    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-limpiar">
        <?= __('limpiar') ?>
    </a>

    <a href="exportar_logs_excel.php?
    usuario=<?= $filtroUsuario ?>&
    accion=<?= $filtroAccion ?>&
    desde=<?= $filtroDesde ?>&
    hasta=<?= $filtroHasta ?>"
    class="btn-exportar">

    <i class="fas fa-file-excel"></i>

    <?= __('excel') ?>

    </a>
    
    <a href="exportar_logs_pdf.php?
    usuario=<?= $filtroUsuario ?>&
    accion=<?= $filtroAccion ?>&
    desde=<?= $filtroDesde ?>&
    hasta=<?= $filtroHasta ?>"
    class="btn-exportar-pdf">

    <i class="fas fa-file-pdf"></i>

    <?= __('pdf') ?>

    </a>
    
</form>

    <!-- BUSCADOR -->

    <div class="logs-toolbar">

        <input
            type="text"
            id="buscarLog"
            placeholder="<?= __('buscar_usuario_accion') ?>">

    </div>

        <p class="ultima-actividad">
            <?= __('ultimo_evento') ?>:
            <?= date('d/m/Y H:i', strtotime($rowUltimo['fecha'])) ?>
        </p>

    <!-- TABLA -->

    <div class="logs-table-container">

        <table class="logs-table">

            <thead>

                <tr>
                <th><?= __('id') ?></th>
                <th><?= __('usuario') ?></th>
                <th><?= __('accion') ?></th>
                <th><?= __('fecha') ?></th>
                </tr>

            </thead>

            <tbody>

            <?php while($row = $result->fetch_assoc()): ?>

                <?php

                $badge = '';

                if(str_contains($row['accion'], 'Inicio')){
                    $badge = 'badge-login';
                }
                elseif(str_contains($row['accion'], 'Creó')){
                    $badge = 'badge-create';
                }
                elseif(str_contains($row['accion'], 'Editó')){
                    $badge = 'badge-edit';
                }
                elseif(str_contains($row['accion'], 'Cambió')){
                    $badge = 'badge-update';
                }
                elseif(str_contains($row['accion'], 'Cierre')){
                    $badge = 'badge-logout';
                }

                ?>

                <tr class="fila-log">

                    <td>
                        <?= $row['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['nombre'] ?? 'Usuario eliminado') ?>
                    </td>

                    <td>

                        <span class="<?= $badge ?>">

                            <?= htmlspecialchars(
                                traducirAccionLog($row['accion'])
                            ) ?>

                        </span>

                    </td>

                    <td>

                        <?= date(
                            'd/m/Y H:i:s',
                            strtotime($row['fecha'])
                        ) ?>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

    <div class="paginacion">

    <?php if($pagina > 1): ?>

        <a href="?pagina=<?= $pagina - 1 ?>
        <?php if(!empty($filtroUsuario)): ?>
        &usuario=<?= $filtroUsuario ?>
        <?php endif; ?>
        <?php if(!empty($filtroAccion)): ?>
        &accion=<?= $filtroAccion ?>
        <?php endif; ?>
        <?php if(!empty($filtroDesde)): ?>
        &desde=<?= $filtroDesde ?>
        <?php endif; ?>
        <?php if(!empty($filtroHasta)): ?>
        &hasta=<?= $filtroHasta ?>
        <?php endif; ?>">
        ←
        </a>
        

    <?php endif; ?>


    <?php for($i = 1; $i <= $totalPaginas; $i++): ?>

    <a
    href="?pagina=<?= $i ?>
    <?php if(!empty($filtroUsuario)): ?>
    &usuario=<?= $filtroUsuario ?>
    <?php endif; ?>
    <?php if(!empty($filtroAccion)): ?>
    &accion=<?= $filtroAccion ?>
    <?php endif; ?>
    <?php if(!empty($filtroDesde)): ?>
    &desde=<?= $filtroDesde ?>
    <?php endif; ?>
    <?php if(!empty($filtroHasta)): ?>
    &hasta=<?= $filtroHasta ?>
    <?php endif; ?>"
    class="<?= $i == $pagina ? 'activo' : '' ?>">

    <?= $i ?>

    </a>

    <?php endfor; ?>

    <?php if($pagina < $totalPaginas): ?>

    <a href="?pagina=<?= $pagina + 1 ?>
    <?php if(!empty($filtroUsuario)): ?>
    &usuario=<?= $filtroUsuario ?>
    <?php endif; ?>
    <?php if(!empty($filtroAccion)): ?>
    &accion=<?= $filtroAccion ?>
    <?php endif; ?>
    <?php if(!empty($filtroDesde)): ?>
    &desde=<?= $filtroDesde ?>
    <?php endif; ?>
    <?php if(!empty($filtroHasta)): ?>
    &hasta=<?= $filtroHasta ?>
    <?php endif; ?>">

    →

    </a>

    <?php endif; ?>

</div>

</main>

<script>

const buscar = document.getElementById('buscarLog');

buscar.addEventListener('keyup', function(){

    let texto = this.value.toLowerCase();

    document.querySelectorAll('.fila-log')
    .forEach(fila => {

        if(
            fila.textContent.toLowerCase()
            .includes(texto)
        ){
            fila.style.display = '';
        }else{
            fila.style.display = 'none';
        }

    });

});

</script>

</body>
</html>