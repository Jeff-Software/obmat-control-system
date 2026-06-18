function actualizarFechaSidebar() {
    const fechaSidebar = document.getElementById("sidebar-fecha");

    if (fechaSidebar) {
        const ahora = new Date();

        const fecha = ahora.toLocaleDateString("es-PE");
        const hora = ahora.toLocaleTimeString("es-PE", {
            hour: "2-digit",
            minute: "2-digit"
        });

        fechaSidebar.textContent = fecha + " " + hora;
    }
}

actualizarFechaSidebar();

setInterval(actualizarFechaSidebar, 1000);