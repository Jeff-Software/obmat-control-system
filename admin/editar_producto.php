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
    <title><?= __('editar_producto') ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/producto_editar.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../modulos/sidebar.php'); ?>
    <main class="main-content main-product-edit">
        <div class="edit-container">
            <h2>
            <i class="fas fa-edit"></i>
            <?= __('editar_producto') ?>
            </h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><?= __('nombre') ?></label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($prod['nombre']) ?>" required>
                </div>
                <div class="form-group">
                    <label><?= __('precio') ?> (<?= $simboloMoneda ?>)</label>
                    <input type="number" step="0.01" name="precio" value="<?= $prod['precio'] ?>" required>
                </div>
                <div class="form-group">
                    <label><?= __('stock') ?></label>
                    <input type="number" name="stock" value="<?= $prod['stock'] ?>" required>
                </div>
                <div class="form-group">
                    <label><?= __('stock_minimo') ?></label>
                    <input type="number" name="stock_minimo" value="<?= $prod['stock_minimo'] ?>" required>
                </div>
                <div class="form-group">
                    <label><?= __('categoria') ?></label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($prod['categoria']) ?>" required>
                </div>
                <div class="form-group">
                    <label><?= __('descripcion') ?></label>
                    <textarea name="descripcion" rows="3"><?= htmlspecialchars($prod['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label><?= __('imagen') ?></label>
                    <div class="file-upload">
                        <input type="file" name="imagen" id="imagen" accept="image/*">
                        <label for="imagen">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span><?= __('subir_imagen') ?></span>
                            <small><?= __('formatos_imagen') ?></small>
                        </label>
                    </div>
                    <div class="current-image">
                        <p><?= __('imagen_producto') ?>:</p>
                        <img src="../assets/img/productos/<?= htmlspecialchars($prod['imagen'] ?? 'default.png') ?>" width="80">
                    </div>
                </div>
            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> <?= __('guardar_cambios') ?>
                </button>

                <a href="articulos.php" class="btn-cancel">
                    <i class="fas fa-times"></i> <?= __('cancelar') ?>
                </a>
            </div>
        <script>
document.getElementById('imagen').addEventListener('change', function () {
    const fileName = this.files[0]?.name;
    if (fileName) {
        this.nextElementSibling.querySelector('span').textContent = fileName;
    }
});
</script>
    </main>
</body>
</html>