<?php
session_start();
require_once 'includes/functions.php';

$page_title = "NaturAll | Carrito de Compras";

// Asegurar carrito en sesión
initialize_cart(); 

// --------------------------------------------------------
// LÓGICA POST
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Leemos la acción si existe
    $action = isset($_POST['action']) ? sanitize_input($_POST['action']) : null;

    // 1) ================== ADD TO CART (AJAX desde la tienda) ==================
    // Este caso NO debe redirigir ni renderizar HTML.
    // Debe devolver JSON porque productos.php/subcategorías esperan JSON.
    if ($action === 'add_to_cart') {

        header('Content-Type: application/json');

        $response = [
            'success' => false,
            'message' => 'Error desconocido',
            'cart_count' => get_cart_item_count()
        ];

        $product_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
        $quantity   = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

        $producto_db = obtener_producto_por_id($product_id);

        if ($producto_db && $product_id > 0 && $quantity > 0) {
            $stock_disponible = (int)($producto_db['stock'] ?? 0);

            $current_cart_quantity = $_SESSION['cart'][$product_id]['quantity'] ?? 0;
            $new_total_quantity = $current_cart_quantity + $quantity;

            if ($new_total_quantity <= $stock_disponible) {

                $added_ok = add_to_cart(
                    $producto_db['id_producto'], 
                    $quantity, 
                    $producto_db['precio_unitario'], 
                    $producto_db['nombre']
                );

                if ($added_ok) {
                    $response['success'] = true;
                    $response['message'] = "Producto agregado correctamente al carrito.";
                    $response['cart_count'] = get_cart_item_count();
                } else {
                    $response['success'] = false;
                    $response['message'] = "No se pudo agregar al carrito (error interno).";
                }

            } else {
                $response['success'] = false;
                $response['message'] = "Stock máximo: {$stock_disponible} unidad(es).";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Producto no válido o cantidad inválida.";
        }

        echo json_encode($response);
        exit; // importantísimo: no seguimos renderizando HTML
    }

    // 2) ================== REMOVE ITEM ==================
    // Botón de la papelera en el carrito. Este botón manda remove_id.
    if (isset($_POST['remove_id'])) {
        $remove_id = (int)$_POST['remove_id'];
        if ($remove_id > 0 && isset($_SESSION['cart'][$remove_id])) {
            unset($_SESSION['cart'][$remove_id]);
        }

        header('Location: ' . BASE_URL . 'carrito.php');
        exit;
    }

    // 3) ================== CLEAR CART ==================
    // Botón "Vaciar Carrito" manda action=clear
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        header('Location: ' . BASE_URL . 'carrito.php');
        exit;
    }

    // 4) ================== UPDATE CANTIDADES ==================
    // Caso normal: cambiar cantidades en el carrito (autoupdate)
    if (isset($_POST['cantidad']) && is_array($_POST['cantidad'])) {

        foreach ($_POST['cantidad'] as $product_id => $quantity_raw) {
            $product_id = (int)$product_id;
            $quantity   = (int)$quantity_raw;

            // Si no está en carrito, ignoramos
            if (!isset($_SESSION['cart'][$product_id])) {
                continue;
            }

            // Info de BD (stock real)
            $producto_db = obtener_producto_por_id($product_id);

            // Si ya no existe en BD por alguna razón, NO lo borramos automáticamente.
            if (!$producto_db) {
                continue;
            }

            $stock_disponible = (int)($producto_db['stock'] ?? 0);

            // Si stock 0 en la BD, no lo borramos solo,
            // dejamos la cantidad como estaba para no asustar al usuario.
            if ($stock_disponible < 1) {
                continue;
            }

            // Normalizamos cantidad pedida:
            if ($quantity < 1) {
                $quantity = 1;
            }

            if ($quantity > $stock_disponible) {
                $quantity = $stock_disponible;
            }

            // Guardamos cantidad corregida
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }

    // Terminamos cualquier POST no-AJAX redirigiendo de vuelta al carrito
    header('Location: ' . BASE_URL . 'carrito.php');
    exit;
}

// --------------------------------------------------------
// VISTA (GET normal)
// --------------------------------------------------------

require_once 'includes/header.php';
?>

<main class="container">
    <section class="page-header">
        <h2>🛍️ Tu Carrito de Compras</h2>
    </section>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alerta info">
            <p>
                Tu carrito está vacío. ¡Explora nuestros 
                <a href="<?php echo BASE_URL; ?>tienda/productos.php">productos</a>!
            </p>
        </div>
    <?php else: ?>

        <!-- FORMULARIO ÚNICO PARA TODO EL CARRITO -->
        <!-- Este form sirve para:
             - actualizar cantidades
             - eliminar un producto (remove_id)
             - vaciar carrito (action=clear)
        -->
        <form method="POST" action="carrito.php" id="carrito-form" class="carrito-form">

            <div class="carrito-list">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Precio Unitario</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): 
                            $producto_db = obtener_producto_por_id($id);

                            // Para el input max usamos el stock de BD si existe,
                            // si no existe usamos la cantidad actual para no romper el input
                            $max_stock = isset($producto_db['stock'])
                                ? (int)$producto_db['stock']
                                : (int)$item['quantity'];

                            if ($max_stock < 1) {
                                $max_stock = 1; // seguridad
                            }

                            $precio_unitario = $item['price'];
                            $cantidad_actual = $item['quantity'];
                            $subtotal = $precio_unitario * $cantidad_actual;
                        ?>
                            <tr>
                                <!-- Nombre -->
                                <td data-label="Producto">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>

                                <!-- Precio unitario -->
                                <td data-label="Precio Unitario" class="text-center">
                                    $<?php echo number_format($precio_unitario, 2, ',', '.'); ?>
                                </td>

                                <!-- Cantidad editable -->
                                <td data-label="Cantidad" class="text-center">
                                    <input 
                                        type="number"
                                        class="input-cantidad"
                                        name="cantidad[<?php echo $id; ?>]"
                                        value="<?php echo htmlspecialchars($cantidad_actual); ?>"
                                        min="1"
                                        max="<?php echo $max_stock; ?>"
                                        data-product-id="<?php echo $id; ?>"
                                        required
                                    >
                                </td>

                                <!-- Subtotal -->
                                <td data-label="Subtotal" class="text-right">
                                    $<?php echo number_format($subtotal, 2, ',', '.'); ?>
                                </td>

                                <!-- Papelera (eliminar este item) -->
                                <td data-label="Acciones" class="text-center">
                                    <button 
                                        type="submit"
                                        class="btn-eliminar"
                                        title="Eliminar producto"
                                        name="remove_id"
                                        value="<?php echo $id; ?>"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- RESUMEN Y ACCIONES -->
            <div class="carrito-footer">
                <div class="resumen-total">
                    <h3>Total a Pagar: </h3>
                    <p class="total-monto">
                        $<?php echo number_format(get_cart_total(), 2, ',', '.'); ?>
                    </p>
                </div>
                
                <div class="carrito-actions">
                    <a href="<?php echo BASE_URL; ?>tienda/productos.php" class="btn-secundario">
                        ← Seguir Comprando
                    </a>

                    <a href="<?php echo BASE_URL; ?>tienda/procesar_pedido.php" class="btn-principal <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>">
                        Finalizar Compra →
                    </a>
                </div>

                <!-- Vaciar carrito completo -->
                <button 
                    type="submit"
                    class="btn-secundario-alerta"
                    style="margin-top:15px;"
                    name="action"
                    value="clear"
                >
                    Vaciar Carrito
                </button>
            </div>

        </form>

        <!-- JS: cuando cambio cantidades, mando el form para actualizar -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formCarrito = document.getElementById('carrito-form');
            const inputsCantidad = document.querySelectorAll('.input-cantidad');

            inputsCantidad.forEach(input => {
                input.addEventListener('change', function () {
                    // normalizamos valor antes de enviar
                    let n = parseInt(this.value, 10);

                    if (isNaN(n) || n < 1) {
                        n = 1;
                    }

                    const maxAttr = this.getAttribute('max');
                    if (maxAttr) {
                        const maxVal = parseInt(maxAttr, 10);
                        if (!isNaN(maxVal) && n > maxVal) {
                            n = maxVal;
                        }
                    }

                    this.value = n;

                    // Enviar el form para actualizar cantidades
                    formCarrito.submit();
                });
            });
        });
        </script>

    <?php endif; ?>
</main>

<?php
require_once 'includes/footer.php';
?>
