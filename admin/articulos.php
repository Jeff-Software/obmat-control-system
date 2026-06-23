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
// Configuración de paginación
$productosPorPagina = 5;

// Página actual
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

if ($pagina < 1) {
    $pagina = 1;
}

// Contar total de productos activos
$totalProductos = $conexion
    ->query("SELECT COUNT(*) AS total FROM productos WHERE estado = 1")
    ->fetch_assoc()['total'];

// Calcular total de páginas
$totalPaginas = ceil($totalProductos / $productosPorPagina);

// Evitar páginas mayores al límite
if ($pagina > $totalPaginas && $totalPaginas > 0) {
    $pagina = $totalPaginas;
}

// Calcular desde qué registro iniciar
$inicio = ($pagina - 1) * $productosPorPagina;


// Obtener productos de la página actual
$query = "
    SELECT *
    FROM productos
    WHERE estado = 1
    ORDER BY nombre
    LIMIT $inicio, $productosPorPagina
";

$productos = $conexion
    ->query($query)
    ->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= __('gestion_articulos') ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/articulos.css">
    <link rel="stylesheet" href="../assets/css/paginacion.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../modulos/sidebar.php'); ?>
    <main class="main-content">
        <header class="dashboard-header">
            <h2><?= __('gestion_articulos') ?></h2>
        </header>

        <!-- Formulario para agregar producto -->
        <div class="form-agregar">
            <h3><?= __('agregar_producto') ?></h3>
            <form action="articulos.php" method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="text" name="nombre" placeholder="<?= __('nombre') ?>" required>
                <input type="number" step="0.01" name="precio" placeholder="<?= __('precio') ?>" required>
                
                <input type="number" name="stock" placeholder="<?= __('stock') ?>" required>
                <input type="number" name="stock_minimo" placeholder="<?= __('stock_minimo') ?>"required>
                
                <input type="text" name="categoria" placeholder="<?= __('categoria') ?>">
                <label for="imagen" class="btn-subir-imagen">

                <i class="fas fa-image"></i>
                <?= __('seleccionar_imagen') ?>

                </label>


                <input 
                type="file" 
                id="imagen"
                name="imagen"
                class="input-imagen"
                accept="image/png,image/jpeg,image/webp"
                hidden>


                
                <textarea name="descripcion" placeholder="<?= __('descripcion') ?>" style="grid-column: span 2;"></textarea>
                <button type="submit" name="agregar" style="grid-column: span 2;"><?= __('agregar') ?></button>
            </form>
        </div>

        <!-- Tabla de productos existentes -->
        <table class="productos-table">
            <thead>
                <tr><th><?= __('id') ?></th>
                    <th><?= __('imagen') ?></th>
                    <th><?= __('nombre') ?></th>
                    <th><?= __('precio') ?></th>
                    <th><?= __('stock') ?></th>
                    <th><?= __('stock_minimo') ?></th>
                    <th><?= __('categoria') ?></th>
                    <th><?= __('acciones') ?></th>
                </tr>
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
                            <?= __('editar') ?>
                        </a>

                        <a href="stock_producto.php?id=<?= $p['id'] ?>" class="btn-add">
                            <?= __('editar_stock') ?>
                        </a>

                        <a href="?eliminar=<?= $p['id'] ?>"
                        class="btn-delete"
                        <?php if ($configSistema['confirmar_eliminar']) : ?>
                        onclick="return confirm('¿Eliminar?')"
                        <?php endif; ?>>
                        <?= __('eliminar') ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginacion">

<?php if ($pagina > 1): ?>
    <a href="?pagina=<?= $pagina - 1 ?>">
        ← <?= __('anterior') ?>
    </a>
<?php endif; ?>


<?php for ($i = 1; $i <= $totalPaginas; $i++): ?>

    <a 
        href="?pagina=<?= $i ?>"
        class="<?= ($i == $pagina) ? 'activa' : '' ?>">
        <?= $i ?>
    </a>

<?php endfor; ?>


<?php if ($pagina < $totalPaginas): ?>
    <a href="?pagina=<?= $pagina + 1 ?>">
        <?= __('siguiente') ?> →
    </a>
<?php endif; ?>

</div>
    </main>
    <?php if (isset($_GET['success'])): ?>
            <script>
                alert('<?= __('producto_agregado') ?>');
                // Limpia la URL para que no vuelva a salir al actualizar
                window.history.replaceState({}, document.title, "articulos.php");
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'eliminado'): ?>
            <script>
                alert('<?= __('producto_eliminado') ?>');
                window.history.replaceState({}, document.title, "articulos.php");
            </script>
        <?php endif; ?>

        <script>

const inputImagen = document.getElementById('imagen');
const nombreArchivo = document.getElementById('nombreArchivo');


inputImagen.addEventListener('change', function(){

    if(this.files.length > 0){

        nombreArchivo.textContent =
        this.files[0].name;

    }else{

        nombreArchivo.textContent =
        "<?= __('ningun_archivo') ?>";

    }

});

</script>
</body>
</html>