<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    exit('denegado');
}

require_once('../config/conexion.php');


$sql = "

UPDATE configuracion SET


nombre_negocio = '',
ruc = '',
direccion = '',
telefono = '',
correo = '',
sitio_web = '',
descripcion = '',


pais = 'PE',

zona_horaria = 'America/Lima',

moneda = 'PEN',

simbolo_moneda = 'S/',

idioma = 'es',


stock_cero = 0,

confirmar_eliminar = 0,

sonido_ventas = 0,

redondeo_totales = 0,

confirmar_cancelar_venta = 0


WHERE id = 1

";


if($conexion->query($sql)){

    echo "exito";

}else{

    echo $conexion->error;

}

?>