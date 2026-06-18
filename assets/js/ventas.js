document.addEventListener('DOMContentLoaded', () => {
    const btnFiltrar = document.getElementById('btnFiltrar');
    const modal = document.getElementById('detalleModal');
    const detalleContenido = document.getElementById('detalleContenido');
    const cerrarBtn = document.getElementById('cerrarModal');

    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', () => {
            const desde = document.getElementById('fecha_desde').value;
            const hasta = document.getElementById('fecha_hasta').value;

            window.location.href = `ventas.php?fecha_desde=${encodeURIComponent(desde)}&fecha_hasta=${encodeURIComponent(hasta)}`;
        });
    }

    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-detalle');
        if (btn) {
            const id = btn.getAttribute('data-id');
            if (id) {
                verDetalle(id);
            }
        }
    });

    if (cerrarBtn) {
        cerrarBtn.addEventListener('click', cerrarModal);
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarModal();
        }
    });

    function verDetalle(idVenta) {
        modal.classList.add('active');
        detalleContenido.innerHTML = '<p>Cargando detalles...</p>';

        fetch(`detalle_venta.php?id=${encodeURIComponent(idVenta)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    detalleContenido.innerHTML = `<p>${escapeHtml(data.error)}</p>`;
                    return;
                }

                detalleContenido.innerHTML = generarTablaDetalle(data.productos, data.total);
            })
            .catch(() => {
                detalleContenido.innerHTML = '<p>Error al cargar los detalles.</p>';
            });
    }

    function cerrarModal() {
        modal.classList.remove('active');
        detalleContenido.innerHTML = '';
    }

    function generarTablaDetalle(productos, total) {
        let html = `
            <table class="detalle-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
        `;

        productos.forEach(p => {
            html += `
                <tr>
                    <td>${escapeHtml(p.nombre)}</td>
                    <td>${p.cantidad}</td>
                    <td>${CONFIG_SISTEMA.simboloMoneda} ${Number(p.precio_unitario).toFixed(2)}</td>
                    <td>${CONFIG_SISTEMA.simboloMoneda} ${Number(p.subtotal).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total venta</th>
                        <th>${CONFIG_SISTEMA.simboloMoneda} ${Number(total).toFixed(2)}</th>
                    </tr>
                </tfoot>
            </table>
        `;

        return html;
    }

    function escapeHtml(text) {
        return String(text)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
});