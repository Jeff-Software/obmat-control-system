<?php

if (!isset($configSistema)) {
    require_once(__DIR__ . '/../config/config_global.php');
}

if (!isset($pagina_actual)) {
    $pagina_actual = basename($_SERVER['PHP_SELF']);
}

?>

<div class="sidebar">
    <div class="logo-container">
        <img 
            src="../assets/img/<?php echo htmlspecialchars($configSistema['logo']); ?>" 
            alt="Logo del negocio"
            class="sidebar-logo">
    </div>

    <span class="menu-title">MENÚ ADMINISTRADOR</span>

    <nav class="sidebar-menu">
        <a href="../admin/dashboard_admin.php" class="menu-item">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="../admin/ventas.php" class="menu-item">
            <i class="fas fa-shopping-cart"></i> Ventas
        </a>
        
        <a href="../admin/analisis.php" class="menu-item">
            <i class="fas fa-chart-line"></i> Análisis
        </a>

        <a href="../admin/movimientos_stock.php" class="menu-item">
            <i class="fas fa-exchange-alt"></i> Movimientos
        </a>
        <a href="../admin/articulos.php" class="menu-item">
            <i class="fas fa-box"></i> Artículos
        </a>
        <a href="../admin/reportes.php" class="menu-item">
            <i class="fas fa-file-alt"></i> Reportes
        </a>

        <a href="../admin/logs.php" class="menu-item">
            <i class="fas fa-clipboard-list"></i> Auditoría
        </a>
        <a href="../admin/usuarios.php" class="menu-item">
            <i class="fas fa-users"></i> Usuarios
        </a>
        <a href="../admin/configuracion.php" class="menu-item">
            <i class="fas fa-cog"></i> Configuración
        </a>
    </nav>

    <div class="sidebar-profile">
        <div class="profile-flex">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <span class="profile-name">
    <?php 
    if (isset($_SESSION['nombre'])) {
        echo htmlspecialchars($_SESSION['nombre']);
    } elseif (isset($_SESSION['usuario'])) {
        echo htmlspecialchars($_SESSION['usuario']);
    } else {
        echo "Invitado"; // Valor por defecto si no hay sesión
    }
    ?>
</span>
        </div>
        <div class="profile-status">
            <span class="status-dot"></span> En línea
        </div>
        <div class="profile-access">
            Último acceso:<br>
            <span><?php echo $_SESSION['ultimo_acceso'] ?? date('d/m/Y - H:i A'); ?></span>
        </div>
        <a href="../modulos/logout.php" class="btn-logout-sidebar"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    
    </div>
</div>