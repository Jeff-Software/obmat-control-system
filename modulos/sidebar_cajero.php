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

    <span class="menu-title">MENÚ CAJERO</span>

    <nav class="sidebar-menu">
        <a href="../cajero/dashboard_cajero.php"
        class="menu-item <?php echo ($pagina_actual == 'dashboard_cajero.php') ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <a href="../cajero/nueva_venta.php"
        class="menu-item <?php echo ($pagina_actual == 'nueva_venta.php') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Nueva Venta
        </a>

        <a href="../cajero/venta_espera.php"
        class="menu-item <?php echo ($pagina_actual == 'venta_espera.php') ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Ventas en Espera
        </a>

        <a href="../cajero/articulos.php"
        class="menu-item <?php echo ($pagina_actual == 'articulos.php') ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Artículos
        </a>
    </nav>

    <div class="sidebar-profile">
        <div class="profile-flex">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>

            <span class="profile-name">
                <?php echo htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']); ?>
            </span>
        </div>

        <div class="profile-status">
            <span class="status-dot"></span> En línea
        </div>

        <div class="profile-access">
            Caja:<br>
            <span><?php echo htmlspecialchars($_SESSION['caja'] ?? 'Sin asignar'); ?></span>
        </div>

        <div class="profile-access">
            Rol:<br>
            <span><?php echo ucfirst($_SESSION['rol'] ?? 'Cajero'); ?></span>
        </div>

        <div class="profile-access">
            Fecha y hora:<br>
            <span id="sidebar-fecha">Cargando...</span>
        </div>

        <a href="../modulos/logout.php" class="btn-logout-sidebar">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>
</div>