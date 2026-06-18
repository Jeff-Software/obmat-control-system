<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once('../config/conexion.php');
require_once('../config/config_global.php');

$id = intval($_GET['id']);
$prod = $conexion->query("SELECT * FROM productos WHERE id = $id")->fetch_assoc();
if (!$prod) {
    header("Location: articulos.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $stock_minimo = intval($_POST['stock_minimo']);
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conexion->prepare("
        UPDATE productos
        SET nombre=?,
        precio=?,
        stock=?,
        stock_minimo=?,
        categoria=?,
        descripcion=?
        WHERE id=?
        ");

        $stmt->bind_param(
            "sdiissi",
            $nombre,
            $precio,
            $stock,
            $stock_minimo,
            $categoria,
            $descripcion,
            $id
            );
    

    $stmt->execute();

    if ($stmt->execute()) {

    // subir imagen si existe

} else {

    die("Error al actualizar: " . $stmt->error);

}
// ...
    
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);

    $nombre_img = uniqid() . '.' . $ext;

    move_uploaded_file(
        $_FILES['imagen']['tmp_name'],
        "../assets/img/productos/" . $nombre_img
    );

    $conexion->query(
        "UPDATE productos SET imagen='$nombre_img' WHERE id=$id"
    );
}

$stmt->close();

header("Location: articulos.php");
exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        .main-content { margin-left: 280px; padding: 30px; background: #f8fafc; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .edit-container { background: white; padding: 30px; border-radius: 24px; max-width: 600px; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .edit-container h2 { margin-bottom: 24px; font-size: 24px; font-weight: 600; color: #0f172a; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #1e293b; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #0061f2; box-shadow: 0 0 0 3px rgba(0,97,242,0.1); }
        .current-image { margin-top: 10px; }
        .current-image img { border-radius: 12px; border: 1px solid #e2e8f0; padding: 4px; background: white; }
        .btn-save { background: #0061f2; color: white; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; margin-right: 12px; }
        .btn-save:hover { background: #004ec2; }
        .btn-cancel { background: #e2e8f0; color: #1e293b; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 500; display: inline-block; }
        .btn-cancel:hover { background: #cbd5e1; }
    </style>
</head>
<body>
    <?php include('../modulos/sidebar.php'); ?>
    <main class="main-content">
        <div class="edit-container">
            <h2><i class="fas fa-edit"></i> Editar producto</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($prod['nombre']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Precio (<?= $simboloMoneda ?>)</label>
                    <input type="number" step="0.01" name="precio" value="<?= $prod['precio'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" value="<?= $prod['stock'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock mínimo</label>
                    <input type="number" name="stock_minimo" value="<?= $prod['stock_minimo'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($prod['categoria']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3"><?= htmlspecialchars($prod['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Imagen</label>
                    <input type="file" name="imagen" accept="image/*">
                    <div class="current-image">
                        <p>Imagen Producto:</p>
                        <img src="../assets/img/productos/<?= htmlspecialchars($prod['imagen'] ?? 'default.png') ?>" width="80">
                    </div>
                </div>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar cambios</button>
                <a href="articulos.php" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
            </form>
        </div>
    </main>
</body>
</html>