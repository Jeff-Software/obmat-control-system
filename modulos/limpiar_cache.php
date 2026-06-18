<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    exit('denegado');
}

echo "exito";