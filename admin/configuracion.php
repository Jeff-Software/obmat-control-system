<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once('../config/conexion.php');
require_once('../config/config_global.php');

$nombre_usuario = $_SESSION['nombre']
    ?? $_SESSION['usuario']
    ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración del Sistema</title>
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/configuracion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <?php include('../modulos/sidebar.php'); ?>

    <main class="main-content">
        <header class="dashboard-header">
            <div class="dashboard-header-title-block">
                <h2>Configuración del Sistema</h2>
                <p>Personaliza los ajustes de tu minimarket</p>
            </div>
        </header>

        <!-- Botón flotante -->
        <div id="saveBar" class="save-bar">

        <div class="save-info">
            <i class="fas fa-exclamation-circle"></i>
            <span>Cambios pendientes</span>
        </div>

            <button id="btnGuardarTodo">
                <i class="fas fa-save"></i>
                Guardar Todo
            </button>

        </div>

        <!-- Toast independiente -->
        <div id="toast" class="toast">
            <i class="fas fa-check-circle"></i>
            <span>Configuración guardada correctamente</span>
        </div>

        <div class="config-grid-layout">

            <div class="col-main">
                <?php include('../modulos/config_sistema.php'); ?>
            </div>

            <div class="col-side">
                <?php include('../modulos/config_regional.php'); ?>
            </div>

        </div>

    <div class="alert-info">
    <i class="fas fa-info-circle"></i>
    <span>Realiza cambios de configuración con precaución. Algunos ajustes pueden afectar el funcionamiento del sistema.</span>
</div>

    </main> 
<script>

// =========================
// TABS
// =========================

document.querySelectorAll('.tab-btn').forEach(button => {

    button.addEventListener('click', function () {

        document.querySelectorAll('.tab-btn')
            .forEach(btn => btn.classList.remove('active'));

        this.classList.add('active');

        document.querySelectorAll('.tab-content')
            .forEach(content => {
                content.style.display = 'none';
            });

        const targetId = this.dataset.target;

        const target = document.getElementById(targetId);

        if(target){
            target.style.display = 'block';
        }

    });

});


// =========================
// CAMBIOS PENDIENTES
// =========================

const saveBar = document.getElementById('saveBar');

let cambiosPendientes = false;

document
.querySelectorAll('input, textarea, select')
.forEach(campo => {

    campo.addEventListener('change', () => {

        cambiosPendientes = true;

        if(saveBar){
            saveBar.classList.add('active');
        }

    });

});

// =========================
// TOAST
// =========================

function mostrarToast(mensaje, error = false){

    const toast = document.getElementById('toast');

    toast.innerHTML = error
        ? '<i class="fas fa-times-circle"></i> ' + mensaje
        : '<i class="fas fa-check-circle"></i> ' + mensaje;

    toast.style.background =
        error ? '#ef4444' : '#10b981';

    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);

}
// =========================
// GUARDAR TODO
// =========================

const btnGuardar = document.getElementById('btnGuardarTodo');

if(btnGuardar){

    btnGuardar.addEventListener('click', function(){

        const formConfig =
            document.getElementById('formConfig');

        const formRegional =
            document.getElementById('form-regional');

        if(!formConfig || !formRegional){
            mostrarToast(
            'No se encontraron los formularios',
            true
        );
            return;
        }

        const datos = new FormData(formConfig);

        const datosRegional = new FormData(formRegional);

        datosRegional.forEach((valor, clave) => {
            datos.append(clave, valor);
        });

        btnGuardar.disabled = true;
        btnGuardar.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        fetch('../modulos/guardar_config_completa.php', {

            method: 'POST',
            body: datos

        })
        .then(res => res.text())
        .then(resp => {

        if(resp.trim() === 'exito'){

            cambiosPendientes = false;

            saveBar.classList.remove('active');

            btnGuardar.innerHTML =
                '<i class="fas fa-check"></i> Guardado';

            mostrarToast(
                'Configuración guardada correctamente'
            );

            setTimeout(() => {

                btnGuardar.innerHTML =
                    '<i class="fas fa-save"></i> Guardar Todo';

                btnGuardar.disabled = false;

            },1500);

        }else{

                console.error(resp);

                mostrarToast(
                    'Error al guardar la configuración',
                    true
                );

                btnGuardar.innerHTML =
                    '<i class="fas fa-save"></i> Guardar Todo';

                btnGuardar.disabled = false;

            }

        })
        .catch(error => {

            console.error(error);

            mostrarToast(
                'Error de conexión',
                true
            );

            btnGuardar.innerHTML =
                '<i class="fas fa-save"></i> Guardar Todo';

            btnGuardar.disabled = false;

        });

    });

}

// =========================
// LIMPIAR CACHE
// =========================

const btnLimpiarCache =
document.getElementById('btnLimpiarCache');

if(btnLimpiarCache){

    btnLimpiarCache.addEventListener('click', () => {

        fetch('../modulos/limpiar_cache.php')
        .then(res => res.text())
        .then(resp => {

            if(resp.trim() === 'exito'){

                mostrarToast(
                    'Caché limpiada correctamente'
                );

            }else{

                mostrarToast(
                    'Error al limpiar caché',
                    true
                );

            }

        });

    });

}

// =========================
// RESTABLECER CONFIG
// =========================

const btnRestablecer =
document.getElementById('btnRestablecer');

if(btnRestablecer){

    btnRestablecer.addEventListener('click', () => {

        if(!confirm(
            '¿Deseas restablecer todas las configuraciones?'
        )){
            return;
        }

        fetch('../modulos/restablecer_config.php')
        .then(res => res.text())
        .then(resp => {

            if(resp.trim() === 'exito'){

                mostrarToast(
                    'Configuración restablecida'
                );

                setTimeout(() => {
                    location.reload();
                }, 1200);

            }else{

                mostrarToast(
                    'Error al restablecer',
                    true
                );

            }

        });

    });

}

</script>
</body>
</html>