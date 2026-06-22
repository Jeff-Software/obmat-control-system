<?php

require_once(__DIR__ . '/conexion.php');

$configSistema = $conexion
    ->query("
        SELECT *
        FROM configuracion
        WHERE id = 1
    ")
    ->fetch_assoc();


// Zona horaria del sistema
if (!empty($configSistema['zona_horaria'])) {
    date_default_timezone_set($configSistema['zona_horaria']);
}


// Símbolo de moneda
// Símbolo de moneda
$simboloMoneda = 'S/';

if (!empty($configSistema['moneda'])) {

    switch ($configSistema['moneda']) {

        case 'USD':
            $simboloMoneda = '$';
            break;

        case 'EUR':
            $simboloMoneda = '€';
            break;

        case 'PEN':
        default:
            $simboloMoneda = 'S/';
            break;
    }

}

$configSistema['simbolo'] = $simboloMoneda;

$idioma = $configSistema['idioma'] ?? 'es';

$archivoIdioma = __DIR__ . "/../idiomas/$idioma.php";

if (file_exists($archivoIdioma)) {

    $lang = require $archivoIdioma;

} else {

    $lang = require __DIR__ . "/../idiomas/es.php";

}

function __($key, $replace = [])
{
    global $lang;

    $text = $lang[$key] ?? $key;

    foreach ($replace as $k => $v) {
        $text = str_replace(":$k", $v, $text);
    }

    return $text;
}