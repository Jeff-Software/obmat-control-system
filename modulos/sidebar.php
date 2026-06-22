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

    <span class="menu-title"><?= __('menu_admin') ?></span>

    <nav class="sidebar-menu">
        <a href="../admin/dashboard_admin.php" class="menu-item">
            <i class="fas fa-th-large"></i> <?= __('dashboard') ?>
        </a>
        <a href="../admin/ventas.php" class="menu-item">
            <i class="fas fa-shopping-cart"></i> <?= __('ventas') ?>
        </a>
        
        <a href="../admin/analisis.php" class="menu-item">
            <i class="fas fa-chart-line"></i> <?= __('analisis') ?>
        </a>

        <a href="../admin/movimientos_stock.php" class="menu-item">
            <i class="fas fa-exchange-alt"></i> <?= __('movimientos') ?>
        </a>
        <a href="../admin/articulos.php" class="menu-item">
            <i class="fas fa-box"></i> <?= __('articulos') ?>
        </a>
        <a href="../admin/reportes.php" class="menu-item">
            <i class="fas fa-file-alt"></i> <?= __('reportes') ?>
        </a>

        <a href="../admin/logs.php" class="menu-item">
            <i class="fas fa-clipboard-list"></i> <?= __('auditoria') ?>
        </a>
        <a href="../admin/usuarios.php" class="menu-item">
            <i class="fas fa-users"></i> <?= __('usuarios') ?>
        </a>
        <a href="../admin/configuracion.php" class="menu-item">
            <i class="fas fa-cog"></i> <?= __('configuracion') ?>
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
            <span class="status-dot"></span> <?= __('en_linea') ?>
        </div>
        <div class="profile-access">
            <?= __('ultimo_acceso') ?>:<br>
            <span>
                <?= htmlspecialchars($_SESSION['ultimo_acceso'] ?? __('primer_acceso')) ?>
            </span>
        </div>
        <a href="../modulos/logout.php" class="btn-logout-sidebar">
            <i class="fas fa-sign-out-alt"></i> <?= __('cerrar_sesion') ?>
        </a>
    
    </div>
</div>