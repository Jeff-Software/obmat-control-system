<?php

require_once('../config/conexion.php');
require_once('../config/auth.php'); 

$limite = 10; // usuarios por página

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $limite;


// Consulta ajustada a las columnas que REALMENTE existen en tu tabla
$sql_kpis = "SELECT 
    COUNT(*) as total_usuarios,
    SUM(CASE WHEN rol = 'admin' THEN 1 ELSE 0 END) as total_admin,
    SUM(CASE WHEN rol = 'cajero' THEN 1 ELSE 0 END) as total_cajeros
    FROM usuarios";

$result_kpis = $conexion->query($sql_kpis);
$kpi = $result_kpis->fetch_assoc();

// Consulta para obtener los usuarios
$sql_usuarios = "SELECT * FROM usuarios ORDER BY id ASC LIMIT $inicio, $limite";
$result_usuarios = $conexion->query($sql_usuarios);

$sql_total = "SELECT COUNT(*) as total FROM usuarios";
$result_total = $conexion->query($sql_total);
$total_usuarios = $result_total->fetch_assoc()['total'];

$total_paginas = ceil($total_usuarios / $limite);

// ... resto de tu código
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | InkaDigital</title>
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/paginacion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="pagina-usuarios">

    <?php include('../modulos/sidebar.php'); ?>

    <main class="main-content">
        <header class="dashboard-header">
            <div class="dashboard-header-title-block">
                <h2>Gestión de Usuarios</h2>
                <p>Administra los usuarios del sistema</p>
            </div>

            <?php if(isset($_GET['error'])): ?>

            <div class="mensaje-error">

                <?php
                switch($_GET['error']){

                    case 'propia_cuenta':
                        echo "⚠️ No puedes desactivar tu propia cuenta.";
                        break;

                }
                ?>

            </div>

        <?php endif; ?>


        </header>
        <section class="kpi-container-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
            <div class="kpi-card">
                <i class="fas fa-users" style="color: #3b82f6;"></i>
                <div><h3>Total Usuarios</h3><p><?php echo $kpi['total_usuarios']; ?></p></div>
            </div>
            <div class="kpi-card">
                <i class="fas fa-user-shield" style="color: #8b5cf6;"></i>
                <div><h3>Administradores</h3><p><?php echo $kpi['total_admin']; ?></p></div>
            </div>
            <div class="kpi-card">
                <i class="fas fa-cash-register" style="color: #10b981;"></i>
                <div><h3>Cajeros</h3><p><?php echo $kpi['total_cajeros']; ?></p></div>
            </div>
        </section>

        <div class="table-container-card" style="background: #fff; padding: 20px; border-radius: 12px; margin-top: 20px;">
            
            <div class="table-toolbar" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <input
                type="text"
                id="buscadorUsuarios"
                placeholder="Buscar usuario por nombre, correo o rol..."
                style="padding: 10px; width: 400px; border: 1px solid #ddd; border-radius: 8px;">
                <a href="../modulos/agregar_usuario.php" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Nuevo Usuario
                </a>
            </div>

            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid #eee;">
                        <th style="padding: 15px;">ID</th>
                        <th style="padding: 15px;">NOMBRE COMPLETO</th>
                        <th style="padding: 15px;">USUARIO</th>
                        <th style="padding: 15px;">ROL</th>
                        <th style="padding: 15px;">ESTADO</th>
                        <th style="padding: 15px;">ÚLTIMO ACCESO</th>
                        <th style="padding: 15px;">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_usuarios->num_rows > 0): ?>
                    <?php while ($row = $result_usuarios->fetch_assoc()): ?>
                        <tr class="fila-usuario" style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 15px;"><?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td style="padding: 15px;"><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td style="padding: 15px;"><?php echo htmlspecialchars($row['usuario']); ?></td>
                            <td style="padding: 15px;"><span class="badge-rol"><?php echo ucfirst($row['rol']); ?></span></td>
                            <td style="padding: 15px;">

                                <span
                                    style="
                                    padding:4px 10px;
                                    border-radius:20px;
                                    font-weight:bold;

                                    background:
                                    <?php echo $row['estado']=='Activo'
                                        ? '#dcfce7'
                                        : '#fee2e2'; ?>;

                                    color:
                                    <?php echo $row['estado']=='Activo'
                                        ? '#15803d'
                                        : '#dc2626'; ?>;
                                    "
                                >
                                    <?php echo $row['estado']; ?>
                                </span>

                            </td>
                            <td style="padding: 15px;">
                                <?php 
                                if (!empty($row['ultimo_acceso'])) {
                                    echo date('d/m/Y - h:i A', strtotime($row['ultimo_acceso'])); 
                                } else {
                                    echo '<span style="color: #94a3b8; font-style: italic;">Nunca</span>';
                                }
                                ?>
                            </td>
                            
                        <td style="padding: 15px;">

                            <a href="../modulos/editar_usuario.php?id=<?php echo $row['id']; ?>"
                            style="color: #666; text-decoration: none;">
                                <i class="fas fa-pencil-alt"
                                style="margin-right:10px;"></i>
                            </a>

                            <?php if($row['estado'] == 'Activo'): ?>

                                <a href="../admin/cambiar_estado_usuario.php?id=<?php echo $row['id']; ?>&estado=Inactivo"
                                onclick="return confirm('¿Desactivar usuario?');"
                                style="color:#10b981; text-decoration:none;">

                                    <i class="fas fa-user-slash"></i>

                                </a>

                            <?php else: ?>

                                <a href="../admin/cambiar_estado_usuario.php?id=<?php echo $row['id']; ?>&estado=Activo"
                                onclick="return confirm('¿Activar usuario?');"
                                style="color:#ef4444; text-decoration:none;">

                                    <i class="fas fa-user-check"></i>

                                </a>

                            <?php endif; ?>

                        </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center;">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>

        <div class="paginacion">

            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?>">«</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>"
                class="<?php echo $i == $pagina ? 'activa' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?>">»</a>
            <?php endif; ?>

        </div>

        </div>

        <div class="info-footer" style="margin-top: 20px; padding: 15px; background: #f0f7ff; color: #0066cc; border-radius: 8px; font-size: 14px;">
            <i class="fas fa-info-circle"></i> Los usuarios con rol Cajero solo pueden realizar ventas y consultar productos.
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const buscador = document.getElementById('buscadorUsuarios');

    buscador.addEventListener('keyup', function () {

        let textoBusqueda = this.value.toLowerCase();
        let filas = document.querySelectorAll('.fila-usuario');

        filas.forEach(function (fila) {

            let nombre = fila.cells[1].textContent.toLowerCase();
            let correo = fila.cells[2].textContent.toLowerCase();
            let rol = fila.cells[3].textContent.toLowerCase();

            if (
                nombre.includes(textoBusqueda) ||
                correo.includes(textoBusqueda) ||
                rol.includes(textoBusqueda)
            ) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }

        });

    });

});
</script>
</body>
</html>