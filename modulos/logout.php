<?php
require_once('../config/conexion.php');
require_once('../config/logs.php');
session_start();

if(isset($_SESSION['id'])){

    registrarLog(
        $conexion,
        $_SESSION['id'],
        'Cierre de sesión'
    );

}

// 1. Limpia todas las variables de sesión
$_SESSION = array(); 

// 2. Borra la cookie de sesión del navegador para invalidar el token
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destruye la sesión en el servidor
session_destroy();

// 4. Redirige al login
header("Location: ../index.php");
exit();
?>