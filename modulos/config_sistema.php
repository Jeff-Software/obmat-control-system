<?php
?>

<div class="config-tabs">
    <button class="tab-btn active" data-target="info-negocio">
    <?= __('informacion_negocio') ?>
    </button>
</div>   

<div class="tab-content" id="info-negocio">

<form id="formConfig" class="grid-form" enctype="multipart/form-data">
        <div class="form-group"><label><?= __('nombre_negocio') ?></label><input type="text" name="nombre_negocio" value="<?php echo htmlspecialchars($configSistemaSistema['nombre_negocio'] ?? ''); ?>"></div>
        <div class="form-group"><label><?= __('ruc_dni') ?></label><input type="text" name="ruc" value="<?php echo htmlspecialchars($configSistema['ruc'] ?? ''); ?>"></div>
        <div class="form-group"><label><?= __('direccion') ?></label><input type="text" name="direccion" value="<?php echo htmlspecialchars($configSistema['direccion'] ?? ''); ?>"></div>
        <div class="form-group"><label><?= __('telefono') ?></label><input type="text" name="telefono" value="<?php echo htmlspecialchars($configSistema['telefono'] ?? ''); ?>"></div>
        <div class="form-group"><label><?= __('correo_electronico') ?></label><input type="email" name="correo" value="<?php echo htmlspecialchars($configSistema['correo'] ?? ''); ?>"></div>
        <div class="form-group"><label><?= __('sitio_web') ?></label><input type="text" name="sitio_web" value="<?php echo htmlspecialchars($configSistema['sitio_web'] ?? ''); ?>"></div>

        <div class="form-group-full">
            <label><?= __('logo_negocio') ?></label>
            <div class="logo-upload-container">
                <div class="logo-preview"><img 
                src="../assets/img/<?= 
                $configSistema['logo'] 
                ?? 'logo.png'
                ?>"></div>
                <div class="upload-controls">
                    <label for="logo" class="btn-secondary">

                    <i class="fas fa-upload"></i>

                    <?= __('cambiar_logo') ?>

                    </label>

                    <input 
                    type="file"
                    id="logo"
                    name="logo"
                    accept="image/png,image/jpeg"
                    style="display:none;">
                    <p class="file-hint"><?= __('formatos_logo') ?></p>
                </div>
            </div>
        </div>

        <div class="form-group-full">
            <label><label><?= __('descripcion_negocio') ?></label></label>
            <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($configSistemaSistema['descripcion'] ?? ''); ?></textarea>
        </div>
        

    </form>
    <div id="mensajeResultado" style="margin-top: 10px; display: none;"></div>
</div>

