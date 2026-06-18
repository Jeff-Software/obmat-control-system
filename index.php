<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OBMAT CONTROL - Inicio de Sesión</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2 class="titulo-sistema">OBMAT CONTROL</h2>
        <h3>INICIO DE SESIÓN</h3>
        <p>Introduce tu usuario y contraseña</p>
        <?php if(isset($_GET['error'])): ?>

    <div class="mensaje-error">

        <?php
        switch($_GET['error']){

            case 'inactivo':
                echo "⛔ Tu cuenta se encuentra desactivada. Contacta al administrador.";
                break;

            case 'password':
                echo "❌ Contraseña incorrecta.";
                break;

            case 'usuario':
                echo "❌ Usuario no encontrado.";
                break;

            case 'campos_vacios':
                echo "⚠️ Completa todos los campos.";
                break;
        }
        ?>

    </div>

<?php endif; ?>
        <form action="modulos/validar_login.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>