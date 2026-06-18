-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-06-2026 a las 10:15:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `obmat_control`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Abarrotes'),
(2, 'Lácteos'),
(3, 'Bebidas'),
(4, 'Higiene'),
(5, 'Otros'),
(6, 'Snacks');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre_negocio` varchar(255) DEFAULT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `sitio_web` varchar(100) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `pais` varchar(5) DEFAULT 'PE',
  `zona_horaria` varchar(50) DEFAULT 'lima',
  `moneda` varchar(50) DEFAULT 'Soles',
  `idioma` varchar(20) DEFAULT 'es',
  `stock_cero` tinyint(1) DEFAULT 0,
  `confirmar_eliminar` tinyint(1) DEFAULT 0,
  `sonido_ventas` tinyint(1) DEFAULT 0,
  `redondeo_totales` tinyint(1) DEFAULT 0,
  `confirmar_cancelar_venta` tinyint(1) DEFAULT 1,
  `logo` varchar(255) DEFAULT 'logo.png',
  `simbolo_moneda` varchar(10) DEFAULT 'S/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre_negocio`, `ruc`, `direccion`, `telefono`, `correo`, `sitio_web`, `logo_url`, `descripcion`, `pais`, `zona_horaria`, `moneda`, `idioma`, `stock_cero`, `confirmar_eliminar`, `sonido_ventas`, `redondeo_totales`, `confirmar_cancelar_venta`, `logo`, `simbolo_moneda`) VALUES
(1, '', '', '', '', '', '', '', '', 'PE', 'America/Lima', 'PEN', 'es', 0, 0, 1, 0, 0, 'logo.png', 'S/');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `id_venta`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 20, 1.80),
(2, 1, 2, 15, 2.50),
(3, 1, 3, 10, 3.00),
(4, 1, 4, 8, 3.00),
(5, 1, 5, 6, 3.30),
(6, 5, 1, 30, 1.80),
(7, 6, 1, 20, 1.80),
(8, 7, 1, 16, 1.80),
(9, 5, 2, 15, 2.50),
(10, 6, 2, 10, 2.50),
(11, 7, 2, 5, 2.50),
(12, 5, 3, 12, 3.00),
(13, 6, 3, 6, 3.00),
(14, 7, 3, 4, 3.00),
(15, 5, 4, 10, 3.00),
(16, 6, 4, 6, 3.00),
(17, 7, 4, 4, 3.00),
(18, 5, 5, 8, 3.30),
(19, 6, 5, 5, 3.30),
(20, 7, 5, 3, 3.30),
(21, 8, 6, 2, 5.50),
(22, 9, 7, 1, 6.00),
(23, 10, 8, 3, 4.50),
(24, 11, 9, 1, 8.00),
(25, 12, 10, 2, 7.50),
(27, 34, 1, 1, 1.80),
(28, 34, 6, 1, 5.50),
(29, 34, 5, 1, 3.30),
(30, 35, 1, 1, 1.80),
(31, 36, 1, 1, 1.80),
(32, 36, 5, 1, 3.30),
(33, 36, 6, 1, 5.50),
(34, 37, 1, 1, 1.80),
(35, 37, 6, 1, 5.50),
(36, 38, 1, 1, 1.80),
(37, 39, 1, 10, 1.80),
(38, 39, 6, 9, 5.50),
(39, 39, 5, 16, 3.30),
(40, 40, 1, 1, 1.80),
(41, 40, 5, 1, 3.30),
(42, 41, 5, 2, 3.30),
(43, 41, 6, 1, 5.50),
(44, 41, 1, 1, 1.80),
(45, 42, 1, 1, 1.80),
(46, 43, 5, 1, 3.30),
(47, 44, 1, 4, 1.80),
(48, 44, 6, 2, 5.50),
(49, 44, 10, 3, 7.50),
(50, 45, 1, 4, 1.80),
(51, 45, 5, 4, 3.30),
(52, 46, 10, 3, 7.50),
(53, 47, 13, 1, 10.00),
(54, 48, 1, 2, 1.80),
(55, 49, 5, 2, 3.30),
(56, 49, 1, 1, 1.80),
(57, 49, 6, 1, 5.50),
(58, 50, 1, 1, 1.80),
(59, 50, 5, 22, 3.30),
(60, 51, 1, 2, 1.80),
(61, 52, 1, 2, 1.80),
(62, 52, 5, 2, 3.30),
(63, 53, 1, 2, 1.80),
(64, 53, 5, 4, 3.30),
(65, 54, 5, 4, 3.30),
(66, 54, 1, 4, 1.80),
(67, 55, 5, 5, 3.30),
(68, 55, 12, 4, 3.50),
(69, 55, 6, 6, 5.50),
(70, 56, 6, 20, 5.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `devoluciones`
--

INSERT INTO `devoluciones` (`id`, `id_venta`, `motivo`, `fecha`) VALUES
(1, 10, 'Producto defectuoso', '2026-05-30 02:38:43'),
(2, 1, 'Cliente arrepentido', '2026-05-30 02:40:56'),
(3, 1, 'Producto vencido', '2026-05-30 02:43:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_caja`
--

CREATE TABLE `estado_caja` (
  `id` int(11) NOT NULL,
  `caja_activa` tinyint(1) DEFAULT 1 COMMENT '1=Activa, 0=Inactiva',
  `caja_abierta` tinyint(1) DEFAULT 0 COMMENT '1=Abierta, 0=Cerrada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_caja`
--

INSERT INTO `estado_caja` (`id`, `caja_activa`, `caja_abierta`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `accion`, `fecha`) VALUES
(1, 1, 'Cambió estado del usuario ID 10 a Inactivo', '2026-06-12 02:42:18'),
(2, 1, 'Cambió estado del usuario ID 10 a Activo', '2026-06-12 02:42:25'),
(3, 1, 'Inicio de sesión', '2026-06-13 02:24:34'),
(4, 1, 'Creó usuario: LeoMessi', '2026-06-13 02:49:36'),
(5, 1, 'Cierre de sesión', '2026-06-13 02:50:32'),
(6, 3, 'Inicio de sesión', '2026-06-13 02:50:53'),
(7, 3, 'Cierre de sesión', '2026-06-13 02:51:34'),
(8, 1, 'Inicio de sesión', '2026-06-13 02:51:39'),
(9, 1, 'Inicio de sesión', '2026-06-13 12:27:36'),
(10, 1, 'Editó usuario: cajero1', '2026-06-13 12:28:15'),
(11, 1, 'Editó usuario: cajero1', '2026-06-13 12:28:47'),
(12, 2, 'Inicio de sesión', '2026-06-13 12:28:56'),
(13, 2, 'Cierre de sesión', '2026-06-13 12:29:03'),
(14, 1, 'Inicio de sesión', '2026-06-13 12:29:17'),
(15, 1, 'Editó usuario: cajero2', '2026-06-13 12:29:28'),
(16, 1, 'Editó usuario: RRSS', '2026-06-13 12:29:40'),
(17, 1, 'Cierre de sesión', '2026-06-13 12:44:43'),
(18, 1, 'Inicio de sesión', '2026-06-13 12:44:49'),
(19, 1, 'Cierre de sesión', '2026-06-13 13:15:45'),
(20, 3, 'Inicio de sesión', '2026-06-13 13:15:56'),
(21, 3, 'Cierre de sesión', '2026-06-13 13:16:29'),
(22, 1, 'Inicio de sesión', '2026-06-13 13:16:34'),
(23, 1, 'Inicio de sesión', '2026-06-14 12:17:17'),
(24, 1, 'Editó usuario: IA', '2026-06-14 18:01:30'),
(25, 8, 'Inicio de sesión', '2026-06-14 18:01:36'),
(26, 1, 'Inicio de sesión', '2026-06-14 18:02:01'),
(27, 8, 'Inicio de sesión', '2026-06-14 18:02:14'),
(28, 1, 'Inicio de sesión', '2026-06-14 18:02:34'),
(29, 1, 'Inicio de sesión', '2026-06-15 19:51:34'),
(30, 1, 'Cierre de sesión', '2026-06-15 19:52:18'),
(31, 8, 'Inicio de sesión', '2026-06-15 19:52:21'),
(32, 8, 'Cierre de sesión', '2026-06-15 19:53:00'),
(33, 1, 'Inicio de sesión', '2026-06-15 19:53:04'),
(34, 1, 'Cierre de sesión', '2026-06-15 19:55:07'),
(35, 8, 'Inicio de sesión', '2026-06-15 19:55:09'),
(36, 8, 'Cierre de sesión', '2026-06-15 19:55:20'),
(37, 1, 'Inicio de sesión', '2026-06-15 19:55:23'),
(38, 1, 'Cierre de sesión', '2026-06-15 20:02:46'),
(39, 8, 'Inicio de sesión', '2026-06-15 20:02:49'),
(40, 8, 'Cierre de sesión', '2026-06-15 20:52:50'),
(41, 1, 'Inicio de sesión', '2026-06-15 20:52:55'),
(42, 8, 'Inicio de sesión', '2026-06-16 01:13:54'),
(43, 8, 'Cierre de sesión', '2026-06-16 01:22:46'),
(44, 1, 'Inicio de sesión', '2026-06-16 01:22:49'),
(45, 1, 'Cierre de sesión', '2026-06-16 01:30:07'),
(46, 8, 'Inicio de sesión', '2026-06-16 01:30:09'),
(47, 1, 'Inicio de sesión', '2026-06-16 01:31:00'),
(48, 1, 'Cierre de sesión', '2026-06-16 01:58:03'),
(49, 8, 'Inicio de sesión', '2026-06-16 01:58:08'),
(50, 8, 'Cierre de sesión', '2026-06-16 01:58:12'),
(51, 8, 'Inicio de sesión', '2026-06-16 01:58:15'),
(52, 8, 'Cierre de sesión', '2026-06-16 01:58:17'),
(53, 1, 'Inicio de sesión', '2026-06-16 01:58:19'),
(54, 8, 'Inicio de sesión', '2026-06-17 19:45:37'),
(55, 8, 'Cierre de sesión', '2026-06-17 19:46:13'),
(56, 1, 'Inicio de sesión', '2026-06-17 19:46:42'),
(57, 1, 'Cierre de sesión', '2026-06-17 21:16:28'),
(58, 8, 'Inicio de sesión', '2026-06-17 21:16:31'),
(59, 8, 'Cierre de sesión', '2026-06-17 21:23:14'),
(60, 1, 'Inicio de sesión', '2026-06-17 21:23:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `nombre`) VALUES
(1, 'efectivo'),
(2, 'tarjeta'),
(3, 'yape');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_stock`
--

CREATE TABLE `movimientos_stock` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `tipo` enum('entrada','salida') DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_stock`
--

INSERT INTO `movimientos_stock` (`id`, `producto_id`, `tipo`, `cantidad`, `motivo`, `fecha`) VALUES
(1, 1, 'salida', 4, 'Venta #44', '2026-06-11 01:25:47'),
(2, 6, 'salida', 2, 'Venta #44', '2026-06-11 01:25:47'),
(3, 10, 'salida', 3, 'Venta #44', '2026-06-11 01:25:47'),
(4, 1, 'salida', 4, 'Venta #45', '2026-06-11 01:26:10'),
(5, 5, 'salida', 4, 'Venta #45', '2026-06-11 01:26:10'),
(6, 10, 'salida', 3, 'Venta #46', '2026-06-11 01:26:25'),
(7, 4, 'entrada', 10, 'Reposición proveedor', '2026-06-11 01:52:38'),
(8, 4, 'entrada', 10, 'Reposición proveedor', '2026-06-11 01:52:45'),
(9, 6, 'entrada', 100, 'Reposición proveedor', '2026-06-11 01:58:31'),
(10, 10, 'entrada', 100, 'Devolución cliente', '2026-06-11 01:59:01'),
(11, 13, 'salida', 1, 'Venta #47', '2026-06-11 02:05:50'),
(12, 13, 'entrada', 100, 'Reposición proveedor', '2026-06-11 02:06:43'),
(13, 13, 'entrada', 1, 'Reposición proveedor', '2026-06-11 02:12:00'),
(14, 14, 'entrada', 100, 'Devolución cliente', '2026-06-10 15:00:00'),
(15, 5, 'entrada', 11, 'Reposición proveedor', '2026-06-12 01:38:57'),
(16, 5, 'entrada', 10, 'Reposición proveedor', '2026-06-12 02:42:37'),
(17, 1, 'salida', 2, 'Venta #48', '2026-06-13 02:51:29'),
(18, 5, 'salida', 2, 'Venta #49', '2026-06-13 13:16:10'),
(19, 1, 'salida', 1, 'Venta #49', '2026-06-13 13:16:10'),
(20, 6, 'salida', 1, 'Venta #49', '2026-06-13 13:16:10'),
(21, 1, 'salida', 1, 'Venta #50', '2026-06-13 13:16:26'),
(22, 5, 'salida', 22, 'Venta #50', '2026-06-13 13:16:26'),
(23, 1, 'salida', 2, 'Venta #51', '2026-06-14 18:01:52'),
(24, 1, 'salida', 2, 'Venta #52', '2026-06-14 18:02:25'),
(25, 5, 'salida', 2, 'Venta #52', '2026-06-14 18:02:25'),
(26, 1, 'salida', 2, 'Venta #53', '2026-06-15 19:52:41'),
(27, 5, 'salida', 4, 'Venta #53', '2026-06-15 19:52:41'),
(28, 5, 'salida', 4, 'Venta #54', '2026-06-15 20:30:47'),
(29, 1, 'salida', 4, 'Venta #54', '2026-06-15 20:30:47'),
(30, 5, 'salida', 5, 'Venta #55', '2026-06-15 20:52:06'),
(31, 12, 'salida', 4, 'Venta #55', '2026-06-15 20:52:06'),
(32, 6, 'salida', 6, 'Venta #55', '2026-06-15 20:52:06'),
(33, 6, 'salida', 20, 'Venta #56', '2026-06-15 20:52:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `mensaje` varchar(255) DEFAULT NULL,
  `tipo` enum('alerta','info','venta','stock') DEFAULT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `mensaje`, `tipo`, `leido`, `fecha`) VALUES
(93, '⚠️ Stock crítico: Mayonesa Alacena 200g (8/10)', 'stock', 0, '2026-06-17 21:23:18'),
(94, '⚠️ Stock crítico: Papel Higiénico Elite (6/10)', 'stock', 0, '2026-06-17 21:23:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `stock_minimo` int(11) DEFAULT 0,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `categoria`, `stock`, `categoria_id`, `precio_compra`, `imagen`, `estado`, `stock_minimo`, `descripcion`) VALUES
(1, 'Galletas 25 gr Chocolate', 1.80, 'Snacks', 160, 6, 1.20, 'galletas.png', 0, 10, NULL),
(2, 'Refresco 3L Sabor Uva', 2.50, 'Bebidas', 150, 3, 1.80, 'gaseosa.png', 0, 10, NULL),
(3, 'Arroz Valle del Norte 1kg', 3.00, 'Abarrotes', 100, 1, 2.30, 'arroz.png', 0, 10, NULL),
(4, 'Leche Gloria Entera 1L', 3.00, 'Lácteos', 100, 2, 2.20, 'leche.png', 1, 10, NULL),
(5, 'Aceite Vegetal Primor 1L', 3.30, 'Abarrotes', 16, 1, 2.70, 'aceite.png', 1, 10, 'hola'),
(6, 'Atún Florida en Aceite 170g', 5.50, 'Abarrotes', 73, 1, 4.20, 'atun.png', 1, 9, NULL),
(7, 'Mayonesa Alacena 200g', 6.00, 'Abarrotes', 8, 1, 4.50, 'mayonesa.png', 1, 10, NULL),
(8, 'Salsa de Tomate 200g', 4.50, 'Abarrotes', 12, 1, 3.20, 'salsa.png', 0, 10, NULL),
(9, 'Papel Higiénico Elite', 8.00, 'Higiene', 6, 4, 5.50, 'papel.png', 1, 10, NULL),
(10, 'Detergente Opal 1kg', 7.50, 'Higiene', 101, 4, 5.80, 'detergente.png', 1, 10, NULL),
(11, 'papas lays', 8.00, 'Snacks', 100, 6, 5.60, 'default.png', 0, 10, NULL),
(12, 'Galletas de Chocolate', 3.50, 'Snacks', 96, 6, 2.00, NULL, 0, 10, NULL),
(13, 'sahur', 10.00, 'Abarrotes', 109, NULL, NULL, 'default.png', 0, 10, NULL),
(14, 'sahur', 10.00, 'Abarrotes', 101, NULL, NULL, 'default.png', 0, 9, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_backup`
--

CREATE TABLE `productos_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `stock_minimo` int(11) DEFAULT 0,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_backup`
--

INSERT INTO `productos_backup` (`id`, `nombre`, `precio`, `categoria`, `stock`, `categoria_id`, `precio_compra`, `imagen`, `estado`, `stock_minimo`, `descripcion`) VALUES
(1, 'Galletas 25 gr Chocolate', 1.80, 'Snacks', 185, 5, 1.20, 'galletas.png', 1, 0, NULL),
(2, 'Refresco 3L Sabor Uva', 2.50, 'Bebidas', 150, 3, 1.80, 'gaseosa.png', 0, 0, NULL),
(3, 'Arroz Valle del Norte 1kg', 3.00, 'Abarrotes', 100, 1, 2.30, 'arroz.png', 0, 0, NULL),
(4, 'Leche Gloria Entera 1L', 3.00, 'Lácteos', 80, 2, 2.20, 'leche.png', 1, 0, NULL),
(5, 'Aceite Vegetal Primor 1L', 3.30, 'Abarrotes', 42, 1, 2.70, 'aceite.png', 1, 10, NULL),
(6, 'Atún Florida en Aceite 170g', 5.50, 'Abarrotes', 3, 1, 4.20, 'atun.png', 1, 9, NULL),
(7, 'Mayonesa Alacena 200g', 6.00, 'Abarrotes', 8, 1, 4.50, 'mayonesa.png', 0, 0, NULL),
(8, 'Salsa de Tomate 200g', 4.50, 'Abarrotes', 12, 1, 3.20, 'salsa.png', 0, 0, NULL),
(9, 'Papel Higiénico Elite', 8.00, 'Higiene', 6, 4, 5.50, 'papel.png', 0, 0, NULL),
(10, 'Detergente Opal 1kg', 7.50, 'Higiene', 7, 4, 5.80, 'detergente.png', 1, 0, NULL),
(11, 'papas lays', 8.00, 'Snacks', 100, NULL, NULL, 'default.png', 0, 10, NULL),
(12, 'Galletas de Chocolate', 3.50, 'Snacks', 100, NULL, 2.00, NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cajero') DEFAULT 'cajero',
  `caja_asignada` varchar(50) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `ultimo_acceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `rol`, `caja_asignada`, `estado`, `ultimo_acceso`) VALUES
(1, 'Luis Ramos', 'admin', '$2y$10$yftKTwYoRuCHI5YgIBj3BuRvhC7jmAr4aS96Lj95R1VAT.DxNZLz6', 'admin', NULL, 'Activo', '2026-06-17 21:23:24'),
(2, 'Jhonatan', 'cajero1', '$2y$10$LQCuOfZp0Vf2XB/6sbkTuuogC.CJKBpqWMj0.8fv73mVu/pGaQtlC', 'cajero', 'CAJA-01', 'Activo', '2026-06-13 12:28:56'),
(3, 'Donaldo', 'cajero2', '$2y$10$ux0yTtyBTb0/PW2Lk35JWOIWS7kZVi6UTds95DzaXN10CX3W5IX.m', 'cajero', 'CAJA-02', 'Activo', '2026-06-13 13:15:56'),
(8, 'Tung sahur', 'IA', '$2y$10$SEXq7JOf4meP2Rtw4nGYfOlbanVEZf76x/ZrzNIYr/qAS88AVTVeW', 'cajero', 'CAJA-04', 'Activo', '2026-06-17 21:16:31'),
(9, 'Roberto Sanchez', 'RRSS', '$2y$10$GiXJD/kmC/DTzpnrC5QHg.gEd4yLJp9j5VuUZRXMLH3klBQPba3Yy', 'cajero', 'CAJA-05', 'Activo', '2026-06-12 01:37:44'),
(10, 'Sabrina Carpenter', 'sasha', '$2y$10$Yja9kNWRGK5h0RFUiaE9ZOwNjchLx.c50Z0kEAuC/2/WUb9hb2/IC', 'cajero', 'CAJA-09', 'Activo', '2026-06-12 02:22:01'),
(11, 'Leonardo', 'LeoMessi', '$2y$10$yGCjcNzZBeWpXQvDW6Y.CuaCrEZDkcfIxLmszURG95nmQNdgxjEmW', 'cajero', 'CAJA-10', 'Activo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `metodo_pago` varchar(20) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `metodo_pago_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `total`, `fecha`, `metodo_pago`, `usuario_id`, `metodo_pago_id`, `cliente_id`) VALUES
(1, 147.30, '2026-05-24 12:50:43', 'efectivo', 1, 1, 1),
(5, 183.90, '2026-05-19 00:00:00', 'efectivo', 1, 1, NULL),
(6, 113.50, '2026-05-14 00:00:00', 'tarjeta', 1, 2, NULL),
(7, 75.20, '2026-05-09 00:00:00', 'yape', 1, 3, NULL),
(8, 11.00, '2026-05-08 00:00:00', 'efectivo', 1, 1, NULL),
(9, 6.00, '2026-05-11 00:00:00', 'efectivo', 1, 1, NULL),
(10, 13.50, '2026-05-13 00:00:00', 'efectivo', 1, 1, NULL),
(11, 8.00, '2026-05-14 00:00:00', 'efectivo', 1, 1, NULL),
(12, 15.00, '2026-05-15 00:00:00', 'efectivo', 1, 1, NULL),
(34, 8.59, '2026-06-02 03:03:56', 'tarjeta', 3, NULL, NULL),
(35, 1.80, '2026-06-02 03:54:08', 'efectivo', 3, NULL, NULL),
(36, 8.06, '2026-06-02 04:03:19', 'yape', 3, NULL, NULL),
(37, 6.79, '2026-06-02 04:07:18', 'efectivo', 3, NULL, NULL),
(38, 1.80, '2026-06-02 04:08:07', 'efectivo', 3, NULL, NULL),
(39, 120.30, '2026-06-02 14:45:48', 'efectivo', 2, NULL, NULL),
(40, 5.10, '2026-06-10 00:55:10', 'efectivo', 8, NULL, NULL),
(41, 13.90, '2026-06-10 00:55:31', 'tarjeta', 8, NULL, NULL),
(42, 1.80, '2026-06-10 01:07:52', 'yape', 8, NULL, NULL),
(43, 3.30, '2026-06-10 01:08:03', 'yape', 8, NULL, NULL),
(44, 40.70, '2026-06-11 01:25:47', 'efectivo', 8, NULL, NULL),
(45, 20.40, '2026-06-11 01:26:10', 'tarjeta', 8, NULL, NULL),
(46, 22.50, '2026-06-11 01:26:25', 'yape', 8, NULL, NULL),
(47, 10.00, '2026-06-11 02:05:50', 'efectivo', 8, NULL, NULL),
(48, 3.60, '2026-06-13 02:51:29', 'tarjeta', 3, NULL, NULL),
(49, 13.90, '2026-06-13 13:16:10', 'efectivo', 3, NULL, NULL),
(50, 74.40, '2026-06-13 13:16:26', 'yape', 3, NULL, NULL),
(51, 3.60, '2026-06-14 18:01:52', 'efectivo', 8, NULL, NULL),
(52, 10.20, '2026-06-14 18:02:25', 'yape', 8, NULL, NULL),
(53, 16.80, '2026-06-15 19:52:41', 'tarjeta', 8, NULL, NULL),
(54, 20.40, '2026-06-15 20:30:47', 'tarjeta', 8, NULL, NULL),
(55, 63.50, '2026-06-15 20:52:06', 'yape', 8, NULL, NULL),
(56, 91.30, '2026-06-15 20:52:32', 'efectivo', 8, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`id_venta`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `estado_caja`
--
ALTER TABLE `estado_caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ventas_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estado_caja`
--
ALTER TABLE `estado_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD CONSTRAINT `devoluciones_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`);

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
