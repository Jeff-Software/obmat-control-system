<?php
?>

<div class="config-tabs">
    <button class="tab-btn active" data-target="info-negocio">Información del Negocio</button>
</div>   

<div class="tab-content" id="info-negocio">

<form id="formConfig" class="grid-form">
        <div class="form-group"><label>Nombre del Negocio</label><input type="text" name="nombre_negocio" value="<?php echo htmlspecialchars($configSistemaSistema['nombre_negocio'] ?? ''); ?>"></div>
        <div class="form-group"><label>RUC / DNI</label><input type="text" name="ruc" value="<?php echo htmlspecialchars($configSistema['ruc'] ?? ''); ?>"></div>
        <div class="form-group"><label>Dirección</label><input type="text" name="direccion" value="<?php echo htmlspecialchars($configSistema['direccion'] ?? ''); ?>"></div>
        <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" value="<?php echo htmlspecialchars($configSistema['telefono'] ?? ''); ?>"></div>
        <div class="form-group"><label>Correo Electrónico</label><input type="email" name="correo" value="<?php echo htmlspecialchars($configSistema['correo'] ?? ''); ?>"></div>
        <div class="form-group"><label>Sitio Web</label><input type="text" name="sitio_web" value="<?php echo htmlspecialchars($configSistema['sitio_web'] ?? ''); ?>"></div>

        <div class="form-group-full">
            <label>Logo del Negocio</label>
            <div class="logo-upload-container">
                <div class="logo-preview"><img src="../assets/img/logo.png" alt="Logo"></div>
                <div class="upload-controls">
                    <button type="button" class="btn-secondary"><i class="fas fa-upload"></i> Cambiar Logo</button>
                    <p class="file-hint">Formatos: JPG, PNG. Máx. 2MB</p>
                </div>
            </div>
        </div>

        <div class="form-group-full">
            <label>Descripción del Negocio</label>
            <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($configSistemaSistema['descripcion'] ?? ''); ?></textarea>
        </div>
        

    </form>
    <div id="mensajeResultado" style="margin-top: 10px; display: none;"></div>
</div>

