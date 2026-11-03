<?php
session_start();
require_once '../includes/functions.php'; 

$page_title = "Explora Nuestra Tienda | NaturAll";

// Traemos TODO el inventario agrupado por categoría desde la BD
// Formato esperado: ['cero_azucares' => [prod1, prod2...], 'veganos' => [...], etc.]
$inventario_agrupado = obtener_inventario();

/*
    Definimos TODAS las categorías que existen en tu tienda.
    Esto nos permite mostrarlas en la barra lateral aunque todavía no tengan productos.
*/

$iconos_categorias = [
    'cero_azucares' => 'fa-droplet-slash',
    'sin_tacc'      => 'fa-wheat-awn-slash',
    'veganos'       => 'fa-leaf',
    'lacteos'       => 'fa-cow',
    'panificados'   => 'fa-bread-slice',
    'bebidas'       => 'fa-bottle-water',
    'congelados'    => 'fa-snowflake',
    'granel'        => 'fa-weight-scale',
];

$nombres_humanos = [
    'cero_azucares' => 'Cero Azúcares',
    'sin_tacc'      => 'Sin TACC',
    'veganos'       => 'Veganos',
    'lacteos'       => 'Lácteos',
    'panificados'   => 'Panificados',
    'bebidas'       => 'Bebidas',
    'congelados'    => 'Congelados',
    'granel'        => 'A Granel',
];

$mapeo_subcategorias = [
    'cero_azucares' => 'productos_cero_azucares.php',
    'sin_tacc'      => 'productos_sin_tacc.php',
    'veganos'       => 'productos_veganos.php',
    'lacteos'       => 'productos_lacteos.php',
    'panificados'   => 'productos_panificados.php',
    'bebidas'       => 'productos_bebidas.php',
    'congelados'    => 'productos_congelados.php',
    'granel'        => 'productos_a_granel.php',
];

// lista ordenada de todas las categorías que queremos mostrar SIEMPRE
$todas_las_categorias = array_keys($nombres_humanos);

require_once '../includes/header.php';
?>

<main class="container product-list-page">
    <section class="page-header">
        <h1>🛒 Todos los Productos</h1>
        <p>Explora nuestro catálogo completo de productos naturales.</p>
    </section>

    <?php if (empty($inventario_agrupado)): ?>
        <div class="alerta info">
            <p>Actualmente no hay productos disponibles en el inventario.</p>
        </div>
    <?php else: ?>

        <div class="shop-layout">

            <!-- SIDEBAR IZQUIERDO -->
            <aside class="filter-sidebar">
                <h3 class="sidebar-title">
                    <i class="fa-solid fa-filter"></i> Navegación por Categoría
                </h3>
                
                <div class="filter-group">
                    <ul class="filter-list category-navigation">

                        <!-- "Todas las Categorías" (estás acá) -->
                        <li>
                            <span class="filter-link active">
                                <i class="fa-solid fa-border-all"></i>
                                Todas las Categorías
                            </span>
                        </li>

                        <!-- Cada categoría individual con contador -->
                        <?php foreach ($todas_las_categorias as $cat_key): 
                            $icono   = $iconos_categorias[$cat_key] ?? 'fa-box';
                            $lindo   = $nombres_humanos[$cat_key] ?? $cat_key;
                            $archivo = $mapeo_subcategorias[$cat_key] ?? null;
                            $count   = isset($inventario_agrupado[$cat_key]) ? count($inventario_agrupado[$cat_key]) : 0;
                            
                            if (!$archivo) continue; // sanity
                        ?>
                            <li>
                                <a href="<?php echo BASE_URL; ?>tienda/subcategorias/<?php echo $archivo; ?>" 
                                   class="filter-link">
                                    <i class="fa-solid <?php echo $icono; ?>"></i>
                                    <?php echo htmlspecialchars($lindo); ?>
                                    <span class="product-count-sidebar">(<?php echo $count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </div>
                
                <div class="filter-info">
                    <p>
                        <i class="fa-solid fa-info-circle"></i>
                        Haz clic en una categoría para ver sólo esos productos.
                    </p>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL DERECHA -->
            <div class="product-content category-list">
                
                <?php 
                // Recorremos solo las categorías que realmente tienen productos
                // para no mostrar secciones vacías en el cuerpo principal.
                foreach ($inventario_agrupado as $categoria_nombre_bd => $productos_list): 
                    
                    $lindo = $nombres_humanos[$categoria_nombre_bd] ?? $categoria_nombre_bd;

                    // ID amigable para usar como anchor interno
                    $categoria_id = strtolower(
                        str_replace(
                            [' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ', '-', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
                            ['', 'a', 'e', 'i', 'o', 'u', 'n', '_', 'a', 'e', 'i', 'o', 'u', 'n'],
                            $lindo
                        )
                    );
                ?>
                    
                    <section class="product-category" id="<?php echo htmlspecialchars($categoria_id); ?>">
                        <h2 class="category-title">
                            <?php echo htmlspecialchars($lindo); ?> 
                            <span class="product-count">(<?php echo count($productos_list); ?>)</span>
                        </h2>
                        
                        <div class="product-grid">
                            <?php 
                            foreach ($productos_list as $producto): 
                                $es_oferta = $producto['es_oferta'] ?? 0;
                                $stock     = (int)($producto['stock'] ?? 0);
                                $precio    = $producto['precio_unitario'] ?? 0.00;
                                $imagen    = $producto['imagen_url'] ?? 'placeholder.jpg';
                                $id        = $producto['id_producto'];
                                $nombre_producto = htmlspecialchars($producto['nombre']);
                            ?>
                                <div class="product-card <?php echo $es_oferta ? 'on-sale' : ''; ?>">
                                    
                                    <?php if ($es_oferta): ?>
                                        <span class="badge-oferta">¡Oferta!</span>
                                    <?php endif; ?>

                                    <a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>">
                                        <img src="<?php echo BASE_URL . 'producto_img/' . htmlspecialchars($imagen); ?>" 
                                            alt="<?php echo $nombre_producto; ?>" 
                                            class="product-image-thumb" 
                                            onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>producto_img/placeholder.jpg';">
                                    </a>

                                    <div class="card-body">
                                        <h3 class="product-name">
                                            <a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>">
                                                <?php echo $nombre_producto; ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="product-price">
                                            $<?php echo number_format($precio, 2, ',', '.'); ?>
                                        </p>
                                        
                                        <?php if ($stock > 0): ?>
                                            <form method="POST" action="<?php echo BASE_URL; ?>carrito.php" class="form-add-small js-add-to-cart">
                                                <input type="hidden" name="action" value="add_to_cart">
                                                <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                                <input type="hidden" name="producto_nombre" value="<?php echo $nombre_producto; ?>">
                                                <input type="hidden" name="cantidad" value="1">
                                                <button type="submit" class="btn-principal btn-sm">
                                                    <i class="fa-solid fa-cart-plus"></i> Añadir
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn-secundario-alerta btn-sm" disabled>Agotado</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                <?php endforeach; ?>
            </div>
        </div>

    <?php endif; ?>
</main>

<!-- Notificación flotante al agregar al carrito -->
<div id="floating-notification" class="notification-hidden">
    <i class="fa-solid fa-check"></i>
    <span id="notification-message"></span>
    <a href="<?php echo BASE_URL; ?>carrito.php" class="btn-ver-carrito">Ver Carrito</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.js-add-to-cart');
    const notification = document.getElementById('floating-notification');
    const notificationMessage = document.getElementById('notification-message');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const cantidad = formData.get('cantidad') || '1';
            const productoNombre = formData.get('producto_nombre') || 'Producto';
            const actionUrl = form.getAttribute('action');

            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationMessage.textContent = `${cantidad}x "${productoNombre}" agregado al carrito.`;
                    notification.classList.remove('notification-hidden', 'error');
                    notification.classList.add('success', 'notification-visible');

                    setTimeout(() => {
                        notification.classList.remove('notification-visible');
                        notification.classList.add('notification-hidden');
                    }, 4000);

                } else {
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
                notificationMessage.textContent = "Error de conexión. Intente nuevamente.";
                notification.classList.remove('notification-hidden', 'success');
                notification.classList.add('error', 'notification-visible');
                
                setTimeout(() => {
                    notification.classList.remove('notification-visible');
                    notification.classList.add('notification-hidden');
                }, 5000);
            });
        });
    });
});
</script>

<?php
require_once '../includes/footer.php';
?>
