<?php
require_once '../includes/functions.php'; 
session_start();

// 🔒 Verificar si el usuario es administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../registro.php");
    exit;
}

// -----------------------------
// Manejo del formulario de nuevo producto
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'agregar_producto') {
    $id_proveedor = intval($_POST['id_proveedor']);
    $id_categoria = intval($_POST['id_categoria']);
    $nombre = trim($_POST['nombre']);
    $precio_unitario = floatval($_POST['precio_unitario']);
    $stock = intval($_POST['stock']);

    if (agregar_producto($id_proveedor, $id_categoria, $nombre, $precio_unitario, $stock)) {
        $_SESSION['mensaje_stock'] = "Producto agregado correctamente.";
    } else {
        $_SESSION['mensaje_stock'] = "Error al agregar el producto.";
    }

    header("Location: gestion_stock.php");
    exit;
}

// Título de la página
$page_title = "Gestión de Stock - Inventario";

// ----------------------------------------------------
// Obtener inventario
// ----------------------------------------------------
$inventario = obtener_inventario();

// Incluir cabecera admin
require_once '../includes/headeradmin.php'; 
?>

<h2>Inventario de Productos</h2>

<!-- Mensaje de acción -->
<?php
if (isset($_SESSION['mensaje_stock'])) {
    echo '<div class="alerta">' . htmlspecialchars($_SESSION['mensaje_stock']) . '</div>';
    unset($_SESSION['mensaje_stock']);
}
?>

<!-- Botón añadir nuevo producto -->
<a href="#" class="btn-agregar">
    <i class="fas fa-plus"></i> Añadir Nuevo Producto
</a>

<!-- Formulario oculto -->
<div id="form-nuevo-producto" style="display:none; margin: 20px 0; border: 1px solid #ccc; padding: 20px;">
    <h3>Agregar Nuevo Producto</h3>
    <form action="gestion_stock.php" method="post">
        <input type="hidden" name="action" value="agregar_producto">

        <label>Proveedor</label>
        <select name="id_proveedor" required>
            <?php
            // Traer proveedores de la base de datos
            $proveedores = $pdo->query("SELECT id_proveedor, nombre_proveedor FROM proveedores ORDER BY nombre_proveedor")->fetchAll();
            foreach ($proveedores as $prov) {
            echo '<option value="' . $prov['id_proveedor'] . '">' . htmlspecialchars($prov['nombre_proveedor']) . '</option>';
            }
            ?>
        </select>


        <label>Categoría</label>
        <select name="id_categoria" required>
            <?php
            // Traer categorías de la base de datos
            $categorias = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria")->fetchAll();
            foreach ($categorias as $cat) {
                echo '<option value="'. $cat['id_categoria'] .'">'. htmlspecialchars($cat['nombre_categoria']) .'</option>';
            }
            ?>
        </select>
        
        <label>Nombre del Producto</label>
        <input type="text" name="nombre" required>

        <label>Precio Unitario</label>
        <input type="number" step="0.01" name="precio_unitario" required>

        <label>Stock</label>
        <input type="number" name="stock" required>

        <button type="submit">Guardar Producto</button>
        <button type="button" id="cancelar-nuevo-producto">Cancelar</button>
    </form>
</div>

<!-- Inventario -->
<?php if (!empty($inventario)): ?>
    <?php foreach ($inventario as $categoria => $productos): ?>
        <section id="<?php echo strtolower(str_replace('_', ' ', $categoria)); ?>">
            <h3>Sección <?php echo htmlspecialchars($categoria); ?></h3>
            
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio de Venta</th>
                        <th>Categoría</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>
                                <?php 
                                if ($producto['stock'] < 30) {
                                    echo '<strong style="color: red;">' . $producto['stock'] . ' 🚨</strong>';
                                } else {
                                    echo $producto['stock'];
                                }
                                ?>
                            </td>
                            <td>$<?php echo number_format($producto['precio_unitario'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                            <td>
                                <a href="gestion_stock.php?action=editar&id=<?php echo urlencode($producto['id_producto']); ?>" title="Editar"><i class="fas fa-edit"></i></a> | 
                                <a href="gestion_stock.php?action=eliminar&id=<?php echo urlencode($producto['id_producto']); ?>" title="Eliminar" onclick="return confirm('¿Seguro que desea eliminar este producto?');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <p>No hay productos registrados en el inventario.</p>
<?php endif; ?>

<?php
require_once '../includes/footer.php'; 
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnAgregar = document.querySelector('.btn-agregar');
    const formNuevo = document.getElementById('form-nuevo-producto');
    const btnCancelar = document.getElementById('cancelar-nuevo-producto');

    btnAgregar.addEventListener('click', (e) => {
        e.preventDefault();
        formNuevo.style.display = 'block';
        formNuevo.scrollIntoView({ behavior: 'smooth' });
    });

    btnCancelar.addEventListener('click', () => {
        formNuevo.style.display = 'none';
    });
});
</script>
