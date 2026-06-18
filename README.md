# 🏪 OBMAT Control System

Sistema web de gestión para minimarkets desarrollado como proyecto de portafolio utilizando PHP, MySQL, JavaScript y CSS.

## 📌 Descripción

OBMAT Control es una aplicación que permite administrar las operaciones diarias de un minimarket mediante un panel de administración y un panel de cajero.

El sistema permite gestionar ventas, productos, usuarios, reportes y configuraciones del negocio.

---

## ✨ Características principales

### 🔐 Sistema de autenticación
- Inicio de sesión seguro.
- Roles de usuario:
  - Administrador.
  - Cajero.
- Control de acceso según permisos.

---

### 🛒 Gestión de ventas
- Registro de nuevas ventas.
- Búsqueda de productos.
- Carrito de compra dinámico.
- Aplicación de descuentos.
- Diferentes métodos de pago:
  - Efectivo.
  - Tarjeta.
  - Yape.
- Ventas en espera.
- Generación de tickets.

---

### 📦 Gestión de inventario
- Registro de productos.
- Control de categorías.
- Gestión de precios.
- Estado de productos activos e inactivos.
- Visualización del stock.

---

### 📊 Dashboard y análisis
- Indicadores principales (KPI).
- Ventas por categoría.
- Ventas por hora.
- Ventas por día.
- Estadísticas del negocio.

---

### 👥 Administración de usuarios
- Gestión de cuentas.
- Asignación de roles.
- Control de accesos.

---

### ⚙️ Configuración del sistema
- Información del negocio.
- Configuración regional.
- Moneda.
- Zona horaria.
- Preferencias del sistema.

---

## 🛠️ Tecnologías utilizadas

### Backend
- PHP 8
- MySQL
- SQL

### Frontend
- HTML5
- CSS3
- JavaScript
- Chart.js

### Herramientas
- XAMPP
- phpMyAdmin
- Visual Studio Code
- Git & GitHub

---

## 📂 Estructura del proyecto

```
OBMAT_CONTROL/
│
├── admin/          # Panel administrador
├── cajero/         # Panel de cajero
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│
├── config/         # Conexión y configuración global
├── modulos/        # Componentes reutilizables
├── sql/            # Base de datos y scripts SQL
│
├── index.php       # Inicio de sesión
└── README.md
```

---

## 🗃️ Base de datos

La base de datos utilizada es:

```
obmat_control
```

Debe importarse desde la carpeta:

```
/sql
```

---

## 🚀 Estado del proyecto

Actualmente en desarrollo.

Módulos completados:
- Sistema de usuarios.
- Panel administrador.
- Panel cajero.
- Gestión de ventas.
- Gestión de productos.
- Dashboard y análisis.
- Configuración del sistema.

Próximas mejoras:
- Gestión dinámica del logo del negocio.
- Mejoras en reportes.
- Optimización y seguridad avanzada.

---

## 👨‍💻 Autor

Desarrollado por Jeff Nostades como proyecto de aprendizaje y portafolio en desarrollo de software.
