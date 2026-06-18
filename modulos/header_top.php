<header class="dashboard-header">
    
    <div class="welcome-text">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']); ?></p>
    </div>

    <div class="header-user-actions">
        
        <div class="header-icon-wrapper">
            <i class="far fa-bell"></i>
            <span class="notification-badge">3</span>
        </div>

        <div class="header-icon-wrapper">
            <i class="fas fa-cog"></i>
        </div>

        <div class="header-profile-box">
            <div class="header-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="header-profile-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']); ?></span>
                <span class="user-role">Administrador</span>
            </div>
            <i class="fas fa-chevron-down arrow-dropdown"></i>
        </div>

    </div>
</header>