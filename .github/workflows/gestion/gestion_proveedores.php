<?php
require_once '../includes/functions.php'; 
session_start();

// 🔒 Verificar si el usuario es administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../registro.php");
    exit;
}

// -----------------------------
// Manejo del formulario de nuevo proveedor
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_supplier') {
    $nombre_proveedor = trim($_POST['nombre_proveedor']);
    $contacto = trim($_POST['contacto']);
    $telefono = trim($_POST['telefono']);

    if (agregar_proveedor($nombre_proveedor, $contacto, $telefono)) {
        $_SESSION['mensaje_proveedor'] = "Proveedor agregado correctamente.";
    } else {
        $_SESSION['mensaje_proveedor'] = "Error al agregar el proveedor.";
    }

    header("Location: gestion_proveedores.php");
    exit;
}

// Título de la página
$page_title = "Gestión de Proveedores";

// ----------------------------------------------------
// 1. Obtener proveedores y sus productos
// ----------------------------------------------------
$proveedores = obtener_proveedores_con_productos();

// Incluir cabecera admin
require_once '../includes/headeradmin.php';
?>

<h2>Listado de Proveedores</h2>

<!-- Mensaje -->
<?php
if (isset($_SESSION['mensaje_proveedor'])) {
    echo '<div class="alerta">' . htmlspecialchars($_SESSION['mensaje_proveedor']) . '</div>';
    unset($_SESSION['mensaje_proveedor']);
}
?>

<?php foreach ($proveedores as $prov): ?>
    <section class="proveedor-info">
        <h3><?php echo htmlspecialchars($prov['nombre_proveedor']); ?></h3>
        <p><strong>Contacto:</strong> <?php echo htmlspecialchars($prov['contacto']); ?></p>
        <?php if (!empty($prov['productos'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prov['productos'] as $prod): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                            <td>$<?php echo number_format($prod['precio_unitario'], 2, ',', '.'); ?></td>
                            <td><?php echo $prod['stock']; ?></td>
                            <td><a href="#">Editar</a> | <a href="#">Eliminar</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tiene productos asignados.</p>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<!-- Formulario para agregar nuevo proveedor -->
<section class="gestion-form">
    <h3>Agregar Nuevo Proveedor</h3>
    <form action="gestion_proveedores.php" method="POST">
        <input type="hidden" name="action" value="add_supplier">

        <label for="nombre_proveedor">Nombre del Proveedor:</label>
        <input type="text" id="nombre_proveedor" name="nombre_proveedor" required>

        <label for="contacto">Contacto (Email):</label>
        <input type="email" id="contacto" name="contacto" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required>

        <button type="submit">Guardar Proveedor</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>
