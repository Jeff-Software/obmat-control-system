<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once('../config/conexion.php');

require_once('../config/config_global.php');
$configSistema = $configSistema ?? [];

// Procesar eliminación
// Procesar eliminación (más seguro)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("UPDATE productos SET estado = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: articulos.php?mensaje=eliminado");
        exit();
    }
}



// --- BLOQUE CORREGIDO DE INSERCIÓN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    // Recibimos los datos básicos
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $stock_minimo = intval($_POST['stock_minimo']);
    $categoria = $_POST['categoria'];
    
    $imagen = 'default.png';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = $_FILES['imagen']['name'];
            $ext = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            
            // Validamos que solo sean extensiones de imagen permitidas
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($ext, $extensiones_permitidas)) {
                $nombre_img = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/productos/" . $nombre_img)) {
                    $imagen = $nombre_img;
                }
            }
        }
        
    
    // INSERT ajustado a las columnas reales que mostraste en la imagen (sin descripción)
    // Por esto (añadimos la descripción):
    $descripcion = $_POST['descripcion']; // Capturamos el nuevo campo
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, precio, stock, stock_minimo, categoria, imagen, descripcion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sdiisss", $nombre, $precio, $stock, $stock_minimo, $categoria, $imagen, $descripcion);
// Cambia esto al final de tu bloque de inserción:
    if ($stmt->execute()) {
        header("Location: articulos.php?success=1"); // Añadimos el parámetro
        exit();
    } else {
        echo "Error al insertar: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener todos los productos
// Fíjate bien en el WHERE estado = 1
$productos = $conexion->query("SELECT * FROM productos WHERE estado = 1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Artículos - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .productos-table { width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .productos-table th, .productos-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .productos-table th { background-color: #f8fafc; font-weight: 600; }
        .btn-edit, .btn-delete, .btn-add { padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 13px; display: inline-block; margin: 0 4px; }
        .btn-edit { background: #3b82f6; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-add { background: #10b981; color: white; margin-bottom: 20px; border: none; cursor: pointer; }
        .form-agregar { background: white; padding: 20px; border-radius: 16px; margin-bottom: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .form-agregar input, .form-agregar textarea, .form-agregar select { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #cbd5e1; border-radius: 8px; }
        .form-agregar button { background: #0061f2; color: white; border: none; padding: 10px; border-radius: 8px; cursor: pointer; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    </style>
</head>
<body>
    <?php include('../modulos/sidebar.php'); ?>
    <main class="main-content">
        <header class="dashboard-header">
            <h2>Gestión de Artículos</h2>
        </header>

        <!-- Formulario para agregar producto -->
        <div class="form-agregar">
            <h3>Agregar nuevo producto</h3>
            <form action="articulos.php" method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="number" step="0.01" name="precio" placeholder="Precio" required>
                
                <input type="number" name="stock" placeholder="Stock" required>
                <input type="number" name="stock_minimo" placeholder="Stock mínimo" required>
                
                <input type="text" name="categoria" placeholder="Categoría">
                <input type="file" name="imagen">
                
                <textarea name="descripcion" placeholder="Descripción" style="grid-column: span 2;"></textarea>
                <button type="submit" name="agregar" style="grid-column: span 2;">Agregar producto</button>
            </form>
        </div>

        <!-- Tabla de productos existentes -->
        <table class="productos-table">
            <thead>
                <tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Stock Mín.</th><th>Categoría</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    
                    <td>
                        <?php 
                        if (!empty($p['imagen']) && $p['imagen'] !== 'default.png' && file_exists("../assets/img/productos/" . $p['imagen'])): ?>
                            <img src="../assets/img/productos/<?= htmlspecialchars($p['imagen']) ?>" width="40" height="40" style="object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <div style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 8px;">
                                <i class="fas fa-box" style="color: #94a3b8; font-size: 20px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= $simboloMoneda ?> <?= number_format($p['precio'], 2) ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td><?php echo $p['stock_minimo'] ?? 'N/A'; ?></td>
                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                    <td>
                        <a href="editar_producto.php?id=<?= $p['id'] ?>" class="btn-edit">
                            Editar
                        </a>

                        <a href="stock_producto.php?id=<?= $p['id'] ?>" class="btn-add">
                            Stock
                        </a>

                        <a href="?eliminar=<?= $p['id'] ?>"
                        class="btn-delete"
                        <?php if ($configSistema['confirmar_eliminar']) : ?>
                        onclick="return confirm('¿Eliminar?')"
                        <?php endif; ?>>
                        Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <?php if (isset($_GET['success'])): ?>
            <script>
                alert('Producto agregado correctamente');
                // Limpia la URL para que no vuelva a salir al actualizar
                window.history.replaceState({}, document.title, "articulos.php");
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'eliminado'): ?>
            <script>
                alert('Producto eliminado correctamente');
                window.history.replaceState({}, document.title, "articulos.php");
            </script>
        <?php endif; ?>
</body>
</html>