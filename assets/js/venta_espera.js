let ventasEspera = JSON.parse(localStorage.getItem('ventas_espera')) || [];

// Guardar venta en espera
function ponerEnEspera(carrito) {
    if (carrito.length === 0) return;

    const venta = {
        id: Date.now(),
        carrito: carrito,
        hora: new Date().toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' })
    };

    ventasEspera.push(venta);
    localStorage.setItem('ventas_espera', JSON.stringify(ventasEspera));
    renderVentasEspera();
}

// Recuperar venta en espera
function recuperarVenta(id) {
    const venta = ventasEspera.find(v => v.id === id);
    if (!venta) return;

    // Guardar carrito a recuperar en localStorage
    localStorage.setItem('carrito_recuperado', JSON.stringify(venta.carrito));

    // Eliminar de espera
    ventasEspera = ventasEspera.filter(v => v.id !== id);
    localStorage.setItem('ventas_espera', JSON.stringify(ventasEspera));

    // Ir a nueva venta
    window.location.href = 'nueva_venta.php';
}

// Eliminar venta en espera
function eliminarEspera(id) {
    ventasEspera = ventasEspera.filter(v => v.id !== id);
    localStorage.setItem('ventas_espera', JSON.stringify(ventasEspera));
    renderVentasEspera();
}

// Mostrar ventas en espera
function renderVentasEspera() {
    const tbody = document.getElementById('carrito-body');

    if (ventasEspera.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="carrito-vacio">En espera....</td></tr>`;
        return;
    }

    tbody.innerHTML = ventasEspera.map(v => `
        <tr>
            <td>🕐 ${v.hora} — ${v.carrito.length} producto(s)</td>
            <td>
                <button onclick="recuperarVenta(${v.id})" 
                    style="padding:6px 12px;background:#0061f2;color:#fff;border:none;border-radius:6px;cursor:pointer;">
                    Recuperar
                </button>
                <button onclick="eliminarEspera(${v.id})" 
                    style="padding:6px 12px;background:#ef4444;color:#fff;border:none;border-radius:6px;cursor:pointer;margin-left:5px;">
                    Eliminar
                </button>
            </td>
        </tr>
    `).join('');
}

// Cargar al iniciar
renderVentasEspera();