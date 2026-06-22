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
        tbody.innerHTML = `<tr><td colspan="5" class="carrito-vacio">${LANG.enEspera}</td></tr>`;
        return;
    }

    tbody.innerHTML = ventasEspera.map(v => `
        <tr>
            <td>🕐 ${v.hora} — ${v.carrito.length} ${LANG.productos}</td>
            <td class="acciones">

            <button class="btn-recuperar" onclick="recuperarVenta(${v.id})">
                <i class="fa-solid fa-cart-shopping"></i>
                ${LANG.recuperar}
            </button>


            <button class="btn-eliminar" onclick="eliminarEspera(${v.id})">
                <i class="fa-solid fa-trash"></i>
                ${LANG.eliminar}
            </button>


            <button class="btn-imprimir" onclick="imprimirEspera(${v.id})">
                <i class="fa-solid fa-print"></i>
                ${LANG.imprimir}
            </button>

            </td>
        </tr>
    `).join('');
}

function imprimirEspera(id) {

    const venta = ventasEspera.find(v => v.id === id);

    if (!venta) {
        alert("Venta no encontrada");
        return;
    }


    const form = document.createElement("form");

    form.method = "POST";
    form.action = "exportar_ventas_espera.php";
    form.target = "_blank";


    const input = document.createElement("input");

    input.type = "hidden";
    input.name = "venta";
    input.value = JSON.stringify(venta);


    form.appendChild(input);

    document.body.appendChild(form);

    form.submit();

    form.remove();
}

// Cargar al iniciar
renderVentasEspera();