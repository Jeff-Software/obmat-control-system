<?php

require_once('../config/conexion.php');
require_once('../config/auth.php');

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
ORDER BY l.fecha DESC
";

$result = $conexion->query($sql);

echo "
<table border='1'>
<tr>
<th>ID</th>
<th>Usuario</th>
<th>Acción</th>
<th>Fecha</th>
</tr>
";

while($row = $result->fetch_assoc()){

    echo "<tr>";
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['nombre']."</td>";
    echo "<td>".$row['accion']."</td>";
    echo "<td>".$row['fecha']."</td>";
    echo "</tr>";
}

echo "</table>";