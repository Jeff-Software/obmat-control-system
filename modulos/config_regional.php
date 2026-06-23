<?php
if (!isset($configSistema)) {
    $configSistema = [];
}
?>


<div class="card">
    <form id="form-regional" action="../modulos/procesar_regional.php" method="POST">
        
        <div class="card-header">
        <h3>
        <i class="fas fa-globe icon-blue"></i>
        <?= __('configuracion_regional') ?>
        </h3>         
        <p><?= __('ajustes_region') ?></p>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label><?= __('pais') ?></label>
                <select name="pais" class="form-control">
                    <option value="PE" <?= ($configSistema['pais'] == 'PE') ? 'selected' : ''; ?>>
                        <?= __('peru') ?>
                    </option>

                    <option value="MX" <?= ($configSistema['pais'] == 'MX') ? 'selected' : ''; ?>>
                        <?= __('mexico') ?>
                    </option>

                    <option value="CO" <?= ($configSistema['pais'] == 'CO') ? 'selected' : ''; ?>>
                        <?= __('colombia') ?>
                    </option>

                    <option value="CL" <?= ($configSistema['pais'] == 'CL') ? 'selected' : ''; ?>>
                        <?= __('chile') ?>
                    </option>

                    <option value="US" <?= ($configSistema['pais'] == 'US') ? 'selected' : ''; ?>>
                        <?= __('estados_unidos') ?>
                    </option>

                    <option value="ES" <?= ($configSistema['pais'] == 'ES') ? 'selected' : ''; ?>>
                        <?= __('espana') ?>
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label><?= __('zona_horaria') ?></label>

            <select name="zona_horaria" class="form-control">

                <option value="America/Lima"
                    <?= ($configSistema['zona_horaria'] == 'America/Lima') ? 'selected' : ''; ?>>
                    (GMT-05:00) Lima
                </option>

                <option value="America/Mexico_City"
                    <?= ($configSistema['zona_horaria'] == 'America/Mexico_City') ? 'selected' : ''; ?>>
                    (GMT-06:00) Ciudad de México
                </option>

                <option value="America/Bogota"
                    <?= ($configSistema['zona_horaria'] == 'America/Bogota') ? 'selected' : ''; ?>>
                    (GMT-05:00) Bogotá
                </option>

                <option value="America/Santiago"
                    <?= ($configSistema['zona_horaria'] == 'America/Santiago') ? 'selected' : ''; ?>>
                    (GMT-04:00) Santiago
                </option>

                <option value="America/New_York"
                    <?= ($configSistema['zona_horaria'] == 'America/New_York') ? 'selected' : ''; ?>>
                    (GMT-05:00) Nueva York
                </option>

                <option value="Europe/Madrid"
                    <?= ($configSistema['zona_horaria'] == 'Europe/Madrid') ? 'selected' : ''; ?>>
                    (GMT+01:00) Madrid
                </option>

            </select>
            </div>
            <div class="form-group">
                <label><?= __('moneda') ?></label>

                <select name="moneda" class="form-control">

                    <option value="PEN"
                        <?= ($configSistema['moneda'] == 'PEN') ? 'selected' : ''; ?>>
                        <?= __('soles') ?>
                    </option>

                    <option value="USD"
                        <?= ($configSistema['moneda'] == 'USD') ? 'selected' : ''; ?>>
                        <?= __('dolares') ?>
                    </option>

                    <option value="EUR"
                        <?= ($configSistema['moneda'] == 'EUR') ? 'selected' : ''; ?>>
                        <?= __('euros') ?>
                    </option>

                </select>
            </div>
            <div class="form-group">
                <label><?= __('idioma') ?></label>
                <select name="idioma" class="form-control">
                    <option value="es" <?php echo ($configSistema['idioma'] == 'es') ? 'selected' : ''; ?>><?= __('espanol') ?></option>
                    <option value="en" <?php echo ($configSistema['idioma'] == 'en') ? 'selected' : ''; ?>><?= __('ingles') ?></option>
                </select>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

        <div class="card-header">
            <h3><i class="fas fa-cog icon-blue"></i> <?= __('preferencias_generales') ?></h3>
            <p><?= __('opciones_generales') ?></p>
        </div>
        
        <div class="preferences-list">
            <div class="pref-item">
                <span><?= __('mostrar_stock_cero') ?></span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="stock_cero"
                        <?= !empty($configSistema['stock_cero']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="pref-item">
                <span><?= __('confirmar_eliminar') ?></span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="confirmar_eliminar"
                        <?= !empty($configSistema['confirmar_eliminar']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="pref-item">
                <span><?= __('sonido_ventas') ?></span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="sonido_ventas"
                        <?= !empty($configSistema['sonido_ventas']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="pref-item">
                <span><?= __('redondeo_totales') ?></span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="redondeo_totales"
                        <?= !empty($configSistema['redondeo_totales']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="pref-item">
                <span><?= __('confirmar_cancelar') ?></span>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="confirmar_cancelar_venta"
                        <?= !empty($configSistema['confirmar_cancelar_venta']) ? 'checked' : '' ?>>

                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

        <div class="card-header">
            <h3><i class="fas fa-database icon-blue"></i> <?= __('mantenimiento_sistema') ?></h3>
            <p><?= __('acciones_mantenimiento') ?></p>
        </div>

        <div class="maintenance-container">
        <div class="maint-card">
            <div class="maint-info">
                <strong>
                <?= __('limpiar_cache') ?>
                </strong>
                <p><?= __('limpiar_cache_desc') ?></p>
            </div>
            <button
                type="button"
                id="btnLimpiarCache"
                class="btn-action btn-limpiar">
                <?= __('limpiar') ?>
            </button>
        </div>

        <div class="maint-card">
            <div class="maint-info">
                <strong>
                <?= __('restablecer_config') ?>
                </strong>
                <p><?= __('restablecer_config_desc') ?></p>
            </div>
            <button
                type="button"
                id="btnRestablecer"
                class="btn-action btn-restablecer">
                <?= __('restablecer') ?>
            </button>
        </div>
        </div>

    </form>
</div>