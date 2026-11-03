<?php
session_start();
require_once '../includes/functions.php'; 

// 1. Obtener el ID del producto de la URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: ' . BASE_URL . 'tienda/productos.php');
    exit;
}

// 2. Obtener los detalles del producto de la base de datos
$producto = obtener_producto_por_id($product_id);

if (!$producto) {
    header('Location: ' . BASE_URL . 'tienda/productos.php?error=no_existe');
    exit;
}

// 3. Establecer el título de la página
$page_title = "Detalle: " . htmlspecialchars($producto['nombre']) . " | NaturAll";

// 4. Incluir la cabecera
require_once '../includes/header.php';

$stock_disponible = (int)($producto['stock'] ?? 0);
?>

<main class="container product-detail">
    
    <section class="breadcrumb">
        <a href="<?php echo BASE_URL; ?>index.php">Inicio</a> /
        <a href="<?php echo BASE_URL; ?>tienda/productos.php">Productos</a> /
        <span><?php echo htmlspecialchars($producto['nombre']); ?></span>
    </section>

    <div class="product-info-wrapper">
        <div class="product-image">
            <?php 
            $image_src = !empty($producto['imagen_url']) 
                ? BASE_URL . 'producto_img/' . htmlspecialchars($producto['imagen_url']) 
                : BASE_URL . 'assets/img/placeholder.jpg';
            ?>
            <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
        </div>
        
        <div class="product-details">
            <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
            
            <p class="price">$<?php echo number_format($producto['precio_unitario'], 2, ',', '.'); ?></p>
            
            <div class="stock-status">
                <?php if ($stock_disponible > 0): ?>
                    <span class="stock-in-status"><i class="fa-solid fa-check-circle"></i> En Stock (<?php echo $stock_disponible; ?> unidades)</span>
                <?php else: ?>
                    <span class="stock-out-status"><i class="fa-solid fa-times-circle"></i> Agotado</span>
                <?php endif; ?>
            </div>

            <div class="product-description">
                <h3>Descripción</h3>
                <?php 
                $descripcion = $producto['descripcion'] ?? 'Este producto orgánico y fresco es una excelente adición a tu dieta saludable. Consulta las etiquetas para más detalles.';
                echo nl2br(htmlspecialchars($descripcion)); 
                ?>
            </div>
            
            <?php if ($stock_disponible > 0): ?>
                <form id="add-to-cart-form" method="POST" action="<?php echo BASE_URL; ?>carrito.php" class="add-to-cart-form">
                    
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="producto_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    
                    <div class="quantity-selector">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" value="1" 
                            min="1" 
                            max="<?php echo $stock_disponible; ?>" 
                            required>
                    </div>
                    
                    <button type="submit" class="btn-principal btn-add-cart">
                        <i class="fa-solid fa-cart-plus"></i> Agregar al Carrito
                    </button>
                </form>
            <?php else: ?>
                <button class="btn-principal disabled" disabled>Producto Agotado</button>
            <?php endif; ?>
            
            <div class="product-meta">
                <p>Categoría: <a href="#"><?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'General'); ?></a></p>
                </div>
        </div>
    </div>
</main>

<div id="floating-notification" class="notification-hidden">
    <i class="fa-solid fa-check"></i>
    <span id="notification-message"></span>
    <a href="<?php echo BASE_URL; ?>carrito.php" class="btn-ver-carrito">Ver Carrito</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-to-cart-form');
        const notification = document.getElementById('floating-notification');
        const notificationMessage = document.getElementById('notification-message');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío normal del formulario (que recarga la página)
                
                const formData = new FormData(form);
                const cantidad = formData.get('cantidad');
                const productoNombre = formData.get('producto_nombre');
                const actionUrl = form.getAttribute('action');

                // 1. Enviar la petición AJAX
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Espera una respuesta JSON del servidor
                .then(data => {
                    // 2. Comprobar la respuesta del servidor
                    if (data.success) {
                        // Mostrar la notificación de éxito
                        notificationMessage.textContent = `${cantidad}x "${productoNombre}" agregado al carrito.`;
                        notification.classList.remove('notification-hidden', 'error');
                        notification.classList.add('success', 'notification-visible');

                        // Ocultar la notificación después de 4 segundos
                        setTimeout(() => {
                            notification.classList.remove('notification-visible');
                            notification.classList.add('notification-hidden');
                        }, 4000);
                        
                        // Opcional: Actualizar contador del carrito si tienes uno en el header

                    } else {
                        // Mostrar un error
                        notificationMessage.textContent = data.message || "Error al agregar el producto.";
                        notification.classList.remove('notification-hidden', 'success');
                        notification.classList.add('error', 'notification-visible');
                        
                        setTimeout(() => {
                            notification.classList.remove('notification-visible');
                            notification.classList.add('notification-hidden');
                        }, 5000);
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud AJAX:', error);
                    // Mostrar un error genérico
                    notificationMessage.textContent = "Error de conexión. Intente nuevamente.";
                    notification.classList.remove('notification-hidden', 'success');
                    notification.classList.add('error', 'notification-visible');
                    
                    setTimeout(() => {
                        notification.classList.remove('notification-visible');
                        notification.classList.add('notification-hidden');
                    }, 5000);
                });
            });
        }
    });
</script>

<?php
require_once '../includes/footer.php';
?>