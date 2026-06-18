<?php
if (!isset($configSistema)) {
    $configSistema = [];
}
?>


<div class="card">
    <form id="form-regional" action="../modulos/procesar_regional.php" method="POST">
        
        <div class="card-header">
        <h3><i class="fas fa-globe icon-blue"></i> Configuración Regional</h3>            
        <p>Ajustes de región, moneda e idioma</p>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label>País</label>
                <select name="pais" class="form-control">
                    <option value="PE" <?= ($configSistema['pais'] == 'PE') ? 'selected' : ''; ?>>
                        Perú
                    </option>

                    <option value="MX" <?= ($configSistema['pais'] == 'MX') ? 'selected' : ''; ?>>
                        México
                    </option>

                    <option value="CO" <?= ($configSistema['pais'] == 'CO') ? 'selected' : ''; ?>>
                        Colombia
                    </option>

                    <option value="CL" <?= ($configSistema['pais'] == 'CL') ? 'selected' : ''; ?>>
                        Chile
                    </option>

                    <option value="US" <?= ($configSistema['pais'] == 'US') ? 'selected' : ''; ?>>
                        Estados Unidos
                    </option>

                    <option value="ES" <?= ($configSistema['pais'] == 'ES') ? 'selected' : ''; ?>>
                        España
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Zona Horaria</label>

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
                <label>Moneda</label>

                <select name="moneda" class="form-control">

                    <option value="PEN"
                        <?= ($configSistema['moneda'] == 'PEN') ? 'selected' : ''; ?>>
                        Soles (S/ PEN)
                    </option>

                    <option value="USD"
                        <?= ($configSistema['moneda'] == 'USD') ? 'selected' : ''; ?>>
                        Dólares ($ USD)
                    </option>

                    <option value="EUR"
                        <?= ($configSistema['moneda'] == 'EUR') ? 'selected' : ''; ?>>
                        Euros (€ EUR)
                    </option>

                </select>
            </div>
            <div class="form-group">
                <label>Idioma</label>
                <select name="idioma" class="form-control">
                    <option value="es" <?php echo ($configSistema['idioma'] == 'es') ? 'selected' : ''; ?>>Español</option>
                    <option value="en" <?php echo ($configSistema['idioma'] == 'en') ? 'selected' : ''; ?>>Inglés</option>
                </select>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

        <div class="card-header">
            <h3><i class="fas fa-cog icon-blue"></i> Preferencias Generales</h3>
            <p>Opciones generales del sistema</p>
        </div>
        
        <div class="preferences-list">
            <div class="pref-item">
                <span>Mostrar stock cero en ventas</span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="stock_cero"
                        <?= !empty($configSistema['stock_cero']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="pref-item">
                <span>Confirmar al eliminar registros</span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="confirmar_eliminar"
                        <?= !empty($configSistema['confirmar_eliminar']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="pref-item">
                <span>Sonido en ventas</span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="sonido_ventas"
                        <?= !empty($configSistema['sonido_ventas']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="pref-item">
                <span>Redondeo en totales</span>
                <label class="switch">
                    <input
                        type="checkbox"
                        name="redondeo_totales"
                        <?= !empty($configSistema['redondeo_totales']) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="pref-item">
                <span>Confirmar al cancelar venta</span>

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
            <h3><i class="fas fa-database icon-blue"></i> Mantenimiento del Sistema</h3>
            <p>Acciones para el mantenimiento del sistema</p>
        </div>

        <div class="maintenance-container">
        <div class="maint-card">
            <div class="maint-info">
                <strong>Limpiar caché del sistema</strong>
                <p>Elimina archivos temporales y mejora el rendimiento.</p>
            </div>
            <button
                type="button"
                id="btnLimpiarCache"
                class="btn-action btn-limpiar">
                Limpiar
            </button>
        </div>

        <div class="maint-card">
            <div class="maint-info">
                <strong>Restablecer configuraciones</strong>
                <p>Restaura las configuraciones por defecto.</p>
            </div>
            <button
                type="button"
                id="btnRestablecer"
                class="btn-action btn-restablecer">
                Restablecer
            </button>
        </div>
        </div>

    </form>
</div>