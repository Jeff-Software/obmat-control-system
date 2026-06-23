<?php

require_once('../config/config_global.php');
function traducirAccionLog($accion){


    // Login
    if(str_contains($accion, 'Inicio de sesión')){

        return __('accion_login');

    }



    // Logout
    if(str_contains($accion, 'Cierre de sesión')){

        return __('accion_logout');

    }



    // Crear usuario
    if(str_contains($accion, 'Creó usuario')){


        return str_replace(
            'Creó usuario',
            __('accion_crear'),
            $accion
        );


    }




    // Editar usuario
    if(str_contains($accion, 'Editó usuario')){


        return str_replace(
            'Editó usuario',
            __('accion_editar'),
            $accion
        );


    }

    if(str_contains($accion, 'Activo')){
    $accion = str_replace(
            'Activo',
            __('estado_activo'),
            $accion
        );
    }


    if(str_contains($accion, 'Inactivo')){
        $accion = str_replace(
            'Inactivo',
            __('estado_inactivo'),
            $accion
        );
    }





    // Cambio de estado
    if(str_contains($accion, 'Cambió estado del usuario')){


        $accion = str_replace(
            'Cambió estado del usuario',
            __('accion_estado'),
            $accion
        );


        $accion = str_replace(
            'Activo',
            __('estado_activo'),
            $accion
        );


        $accion = str_replace(
            'Inactivo',
            __('estado_inactivo'),
            $accion
        );


        $accion = str_replace(
            ' a ',
            ' to ',
            $accion
        );


        return $accion;

    }



    return $accion;

}

?>