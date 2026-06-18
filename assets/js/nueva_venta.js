let carrito = [];
let metodoPago = '';
const MONEDA = CONFIG_SISTEMA.simboloMoneda;

const buscador = document.getElementById('buscador');
const listaResultados = document.getElementById('lista-resultados');

buscador.addEventListener('input', function () {
    const query = this.value.trim();
    if (query.length < 2) { listaResultados.style.display = 'none'; return; }

    fetch(`buscar_producto.php?q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(productos => {
            if (productos.length === 0) {
                listaResultados.innerHTML = '<div style="padding:15px;color:#94a3b8;text-align:center;">No se encontraron productos</div>';
            } else {
                listaResultados.innerHTML = productos.map(p => `
                    <div class="item-resultado" onclick="agregarAlCarrito(${p.id}, '${escapar(p.nombre)}', ${p.precio}, '${escapar(p.imagen)}')">
                        <span style="font-size:28px;">🛍️</span>
                        <div class="info-producto">
                            <div class="nombre">${p.nombre}</div>
                            <div class="precio">
                            ${MONEDA} ${parseFloat(p.precio).toFixed(2)}
                            | Stock: ${p.stock}
                        </div>
                        </div>
                    </div>
                `).join('');
            }
            listaResultados.style.display = 'block';
        });
});

// Cargar carrito recuperado si existe
const carritoRecuperado = localStorage.getItem('carrito_recuperado');
if (carritoRecuperado) {
    carrito = JSON.parse(carritoRecuperado);
    localStorage.removeItem('carrito_recuperado');
    renderCarrito();
}

function escapar(str) {
    if (!str) return '';

    return String(str)
        .replace(/'/g, "\\'")
        .replace(/"/g, '\\"');
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.buscador-box') && !e.target.closest('.lista-resultados')) {
        listaResultados.style.display = 'none';
    }
});

function agregarAlCarrito(id, nombre, precio, imagen) {
    buscador.value = '';
    listaResultados.style.display = 'none';
    const existe = carrito.find(p => p.id === id);
    if (existe) { existe.cantidad += 1; }
    else { carrito.push({ id, nombre, precio: parseFloat(precio), cantidad: 1, imagen }); }
    renderCarrito();
}

function renderCarrito() {
    const tbody = document.getElementById('carrito-body');
    if (carrito.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="carrito-vacio">Busca un producto para agregarlo</td></tr>`;
        document.getElementById('btn-cobrar').disabled = true;
    } else {
        tbody.innerHTML = carrito.map((p, i) => `
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:28px;">🛍️</span>
                        <span style="font-weight:600;">${p.nombre}</span>
                    </div>
                </td>
                <td>${MONEDA} ${p.precio.toFixed(2)}</td>
                <td>
                    <div class="cantidad-control">
                        <button onclick="cambiarCantidad(${i}, -1)">−</button>
                        <span>${p.cantidad}</span>
                        <button onclick="cambiarCantidad(${i}, 1)">+</button>
                    </div>
                </td>
                <td>${MONEDA} ${(p.precio * p.cantidad).toFixed(2)}</td>
                <td><button class="btn-eliminar" onclick="eliminarProducto(${i})">🗑</button></td>
            </tr>
        `).join('');
        document.getElementById('btn-cobrar').disabled = false;
    }
    actualizarResumen();
}

function cambiarCantidad(i, delta) {
    carrito[i].cantidad += delta;
    if (carrito[i].cantidad <= 0) carrito.splice(i, 1);
    renderCarrito();
}

function eliminarProducto(i) {
    carrito.splice(i, 1);
    renderCarrito();
}

function actualizarResumen() {
    const subtotal = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);
    const descPct = parseFloat(document.getElementById('descuento-input').value) || 0;
    const descuento = subtotal * (descPct / 100);
    let total = subtotal - descuento;

    if (CONFIG_SISTEMA.redondeoTotales) {
        total = Math.round(total);
    }
    const totalArticulos = carrito.reduce((sum, p) => sum + p.cantidad, 0);

    document.getElementById('total-articulos').textContent = totalArticulos;
    document.getElementById('resumen-articulos').textContent = totalArticulos;
    document.getElementById('resumen-subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('resumen-subtotal2').textContent = subtotal.toFixed(2);
    document.getElementById('resumen-descuento').textContent = descuento.toFixed(2);
    document.getElementById('total-final').textContent = total.toFixed(2);
    
    const ahorroBadge = document.getElementById('ahorro-badge');
    if (descuento > 0) {
        document.getElementById('ahorro-valor').textContent = descuento.toFixed(2);
        ahorroBadge.style.display = 'block';
    } else {
        ahorroBadge.style.display = 'none';
    }
}

document.getElementById('descuento-input').addEventListener('input', actualizarResumen);

function cancelarVenta() {

    if (
        CONFIG_SISTEMA.confirmarCancelarVenta &&
        carrito.length > 0 &&
        !confirm('¿Seguro que deseas cancelar la venta?')
    ) {
        return;
    }

    carrito = [];
    document.getElementById('descuento-input').value = 0;
    renderCarrito();
}

function abrirModalPago() {
    document.getElementById('modal-total').textContent = document.getElementById('total-final').textContent;
    document.getElementById('modal-pago').classList.add('visible');
    metodoPago = '';
    document.getElementById('btn-confirmar').disabled = true;
    document.querySelectorAll('.metodo-btn').forEach(b => b.classList.remove('seleccionado'));
}

function cerrarModalPago() {
    document.getElementById('modal-pago').classList.remove('visible');
}

function seleccionarMetodo(metodo, btn) {
    metodoPago = metodo;
    document.querySelectorAll('.metodo-btn').forEach(b => b.classList.remove('seleccionado'));
    btn.classList.add('seleccionado');
    document.getElementById('btn-confirmar').disabled = false;
}

let procesando = false;

function confirmarVenta() {
    if (procesando) return;
    procesando = true;

    if (!metodoPago) {
        alert('Por favor selecciona un método de pago');
        procesando = false;
        return;
    }
    const descPct = parseFloat(document.getElementById('descuento-input').value) || 0;
    fetch('procesar_venta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ carrito, descuento: descPct, metodo_pago: metodoPago })
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {

                if (res.sonido_ventas) {
                    const audio = document.getElementById('audioVenta');

                    if (audio) {
                        audio.currentTime = 0;
                        audio.play();
                    }
                }

                window.open(
                    'generar_ticket.php?id=' + res.venta_id,
                    '_blank'
                );

                cerrarModalPago();

                carrito = [];
                document.getElementById('descuento-input').value = 0;
                renderCarrito();

                const msg = document.createElement('div');
                msg.textContent = '✅ Venta registrada correctamente';
                msg.style.cssText = 'position:fixed;top:20px;right:20px;background:#16a34a;color:#fff;padding:15px 25px;border-radius:10px;font-weight:600;z-index:999;';
                document.body.appendChild(msg);

                setTimeout(() => msg.remove(), 3000);

            } else {

                alert(res.mensaje);

            }
        })
        .catch(error => {
            console.error(error);
            alert('Error al procesar la venta');
        })
        .finally(() => {
            procesando = false;
        });

    }    

function ponerEnEsperaDesdeVenta() {
    if (carrito.length === 0) {
        alert('No hay productos en el carrito');
        return;
    }
    const venta = {
        id: Date.now(),
        carrito: carrito,
        hora: new Date().toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' })
    };
    let ventasEspera = JSON.parse(localStorage.getItem('ventas_espera')) || [];
    ventasEspera.push(venta);
    localStorage.setItem('ventas_espera', JSON.stringify(ventasEspera));

    carrito = [];
    document.getElementById('descuento-input').value = 0;
    renderCarrito();

    const msg = document.createElement('div');
    msg.textContent = '✅ Venta guardada en espera';
    msg.style.cssText = 'position:fixed;top:20px;right:20px;background:#f59e0b;color:#fff;padding:15px 25px;border-radius:10px;font-weight:600;z-index:999;';
    document.body.appendChild(msg);
    setTimeout(() => msg.remove(), 3000);
}

function irAListaEspera() {
    if (carrito.length > 0) {
        ponerEnEsperaDesdeVenta();
    }
    window.location.href = 'venta_espera.php';
}