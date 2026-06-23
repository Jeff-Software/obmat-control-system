<?php

require_once('../config/conexion.php');
require_once('../config/auth.php');
require_once('../config/config_global.php');
require_once('../config/traducir_logs.php');
$filtroUsuario = $_GET['usuario'] ?? '';
$filtroAccion = $_GET['accion'] ?? '';

$filtroDesde = $_GET['desde'] ?? '';
$filtroHasta = $_GET['hasta'] ?? '';

$where=[];


if(!empty($filtroUsuario)){
    $where[]="l.usuario_id=".(int)$filtroUsuario;
}


if($filtroAccion=='login'){
    $where[]="l.accion LIKE '%Inicio de sesión%'";
}

elseif($filtroAccion=='logout'){
    $where[]="l.accion LIKE '%Cierre de sesión%'";
}

elseif($filtroAccion=='crear'){
    $where[]="l.accion LIKE '%Creó usuario%'";
}

elseif($filtroAccion=='editar'){
    $where[]="l.accion LIKE '%Editó usuario%'";
}

elseif($filtroAccion=='estado'){
    $where[]="l.accion LIKE '%Cambió estado%'";
}


if(!empty($filtroDesde)){
    $where[]="DATE(l.fecha)>='$filtroDesde'";
}


if(!empty($filtroHasta)){
    $where[]="DATE(l.fecha)<='$filtroHasta'";
}

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=auditoria.xls");

echo "\xEF\xBB\xBF";


$sql = "
SELECT
    l.id,
    u.nombre,
    l.accion,
    l.fecha
FROM logs l

LEFT JOIN usuarios u
ON l.usuario_id = u.id
";


if(count($where)>0){

    $sql .= " WHERE ".implode(" AND ",$where);

}


$sql .= "
ORDER BY l.fecha DESC
";


$result = $conexion->query($sql);



echo "

<html>

<head>

<style>

body{
    font-family: Arial;
}


.titulo{

    background:#2563eb;
    color:white;
    font-size:18px;
    font-weight:bold;
    text-align:center;

}


.cabecera{

    background:#1d4ed8;
    color:white;
    font-weight:bold;
    text-align:center;

}


td{

    padding:8px;

}


.tabla{

    border-collapse:collapse;

}


tr:nth-child(even){

    background:#f1f5f9;

}


.fecha{

    text-align:center;

}


</style>


</head>


<body>


<table class='tabla' border='1'>

<tr>

<td colspan='4' class='titulo'>

OBMAT CONTROL - AUDITORÍA DEL SISTEMA

</td>

</tr>


<tr>

<td colspan='4'>

".__('registro_actividades')."

</td>

</tr>


<tr>

<td colspan='4'>

".__('reporte_generado').": ".date('d/m/Y H:i')."

</td>

</tr>


<tr>

<td colspan='4'>

</td>

</tr>


<tr class='cabecera'>

<th width='60'>
".__('id')."
</th>


<th width='150'>
".__('usuario')."
</th>


<th width='250'>
".__('accion')."
</th>


<th width='150'>
".__('fecha')."
</th>


</tr>
";



while($row = $result->fetch_assoc()){


echo "

<tr>


<td>

".$row['id']."

</td>


<td>

".htmlspecialchars($row['nombre'] ?? __('usuario_eliminado'))."

</td>


<td>

".htmlspecialchars(
    traducirAccionLog($row['accion'])
)."

</td>


<td class='fecha'>

".date(
    'd/m/Y H:i:s',
    strtotime($row['fecha'])
)."

</td>


</tr>


";


}


echo "

</table>


</body>


</html>

";