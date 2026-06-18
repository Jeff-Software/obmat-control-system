<?php
session_start();
//si n existe la variable desesión 'usuario',significa que no se ha logueado
if (!isset($_SESSION['usuario'])) {
    //Lo enviamos de vuelta al login
    header("Location: ../index.php");
    exit(); //detener la ejecución
}
?>