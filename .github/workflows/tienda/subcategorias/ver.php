<?php
// ver.php
// Vista reutilizable para TODAS las subcategorías

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../includes/functions.php';

// Traer inventario agrupado por categoría
$inventario_agrupado = obtener_inventario();

// Si la categoría pedida no existe en el inventario agrupado, igual vamos a dejarte ver la página,
// pero si querés forzar redirección cuando está vacía, descomentá el bloque siguiente:
/*
if (!isset($inventario_agrupado[$categoria_bd])) {
    header('Location: ' . BASE_URL . 'tienda/productos.php');
    exit;
}
*/

// Productos de esta categoría (si no hay, array vacío)
$productos_categoria = isset($inventario_agrupado[$categoria_bd])
    ? $inventario_agrupado[$categoria_bd]
    : [];

// Mapeos globales
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

$mapeo_archivos = [
    'cero_azucares' => 'productos_cero_azucares.php',
    'sin_tacc'      => 'productos_sin_tacc.php',
    'veganos'       => 'productos_veganos.php',
    'lacteos'       => 'productos_lacteos.php',
    'panificados'   => 'productos_panificados.php',
    'bebidas'       => 'productos_bebidas.php',
    'congelados'    => 'productos_congelados.php',
    'granel'        => 'productos_a_granel.php',
];

// lista fija para iterar SIEMPRE
$todas_las_categorias = array_keys($nombres_humanos);

// Esto lo usa el header.php para el <title>
$page_title = isset($page_title) ? $page_title : ($categoria_humano . " | NaturAll");

require_once '../../includes/header.php';
?>

<main class="container product-list-page subcategory-page">
    <section class="page-header">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>tienda/categorias.php">Categorías</a>
            <span class="separator">/</span>
            <span class="current"><?php echo htmlspecialchars($categoria_humano); ?></span>
        </div>
        
        <h1>
            <i class="fa-solid <?php echo htmlspecialchars($icono_categoria); ?>"></i>
            <?php echo htmlspecialchars($categoria_humano); ?>
        </h1>

        <?php if (!empty($descripcion_pagina)): ?>
        <p><?php echo htmlspecialchars($descripcion_pagina); ?></p>
        <?php endif; ?>
    </section>

    <?php if (empty($productos_categoria)): ?>
        <div class="alerta info">
            <p>Actualmente no hay productos disponibles en esta categoría.</p>
            <a href="<?php echo BASE_URL; ?>tienda/productos.php" class="btn-principal">
                Ver todos los productos
            </a>
        </div>
    <?php endif; ?>

    <div class="shop-layout">

        <!-- SIDEBAR IZQUIERDO -->
        <aside class="filter-sidebar">
            <h3 class="sidebar-title">
                <i class="fa-solid fa-filter"></i> Navegación
            </h3>
            
            <!-- Categoría actual -->
            <div class="filter-group">
                <h4>Vista Actual</h4>
                <ul class="filter-list category-navigation">
                    <li>
                        <span class="filter-link active">
                            <i class="fa-solid <?php echo htmlspecialchars($icono_categoria); ?>"></i>
                            <?php echo htmlspecialchars($categoria_humano); ?>
                            <span class="product-count-sidebar">
                                (<?php echo count($productos_categoria); ?>)
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
            
            <!-- Otras categorías -->
            <div class="filter-group">
                <h4>Otras Categorías</h4>
                <ul class="filter-list">
                    <?php 
                    foreach ($todas_las_categorias as $cat_key):
                        if ($cat_key === $categoria_bd) continue; // no mostrar la actual en "otras"
                        if (!isset($mapeo_archivos[$cat_key])) continue;

                        $icono_otro   = $iconos_categorias[$cat_key] ?? 'fa-box';
                        $nombre_lindo = $nombres_humanos[$cat_key] ?? $cat_key;
                        $archivo_otro = $mapeo_archivos[$cat_key];

                        $count_cat = isset($inventario_agrupado[$cat_key])
                            ? count($inventario_agrupado[$cat_key])
                            : 0;
                    ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>tienda/subcategorias/<?php echo $archivo_otro; ?>" 
                               class="filter-link">
                                <i class="fa-solid <?php echo $icono_otro; ?>"></i>
                                <?php echo htmlspecialchars($nombre_lindo); ?>
                                <span class="product-count-sidebar">(<?php echo $count_cat; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Volver al catálogo completo -->
            <button class="btn-secundario btn-sm reset-filters" 
                    onclick="window.location.href='<?php echo BASE_URL; ?>tienda/productos.php'">
                <i class="fa-solid fa-border-all"></i> Ver Todas las Categorías
            </button>
        </aside>

        <!-- CONTENIDO PRINCIPAL DERECHA -->
        <div class="product-content">

            <div class="category-info-bar">
                <p class="product-count-total">
                    Mostrando 
                    <strong><?php echo count($productos_categoria); ?></strong> 
                    producto(s)
                </p>
            </div>
            
            <div class="product-grid">
                <?php 
                foreach ($productos_categoria as $producto): 
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

        </div><!-- .product-content -->

    </div><!-- .shop-layout -->
</main>

<!-- Notificación flotante carrito -->
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
require_once '../../includes/footer.php';
?>
