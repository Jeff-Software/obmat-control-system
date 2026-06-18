<?php

function registrarLog($conexion, $usuario_id, $accion)
{
    $stmt = $conexion->prepare("
        INSERT INTO logs
        (
            usuario_id,
            accion
        )
        VALUES
        (?, ?)
    ");

    $stmt->bind_param(
        "is",
        $usuario_id,
        $accion
    );

    $stmt->execute();
}