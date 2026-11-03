<?php
require_once '..\includes\functions.php'; 

$page_title = "Ofertas Imperdibles | NaturAll";

// ----------------------------------------------------------------------
// LÓGICA PHP para obtener productos en oferta desde la base de datos
// ----------------------------------------------------------------------

$ofertas = obtener_productos_en_oferta(true); // Esta función ya existe en functions.php

require_once '../includes/header.php'; 
?>

<main class="container ofertas-page">
    <section class="page-header">
        <h1>🌟 Nuestras Ofertas Imperdibles</h1>
        <p>Productos con descuento por tiempo limitado. ¡Aprovecha ahora!</p>
    </section>

    <?php if (empty($ofertas)): ?>
        <div class="alerta info">
            <p>Actualmente no tenemos ofertas activas. ¡Vuelve pronto!</p>
        </div>
    <?php else: ?>
        
        <div class="product-grid ofertas-grid">
            
            <?php foreach ($ofertas as $producto): 
                $id = $producto['id_producto'];
                $nombre = $producto['nombre'];
                $precio = $producto['precio_unitario'] ?? 0.00;
                $imagen = $producto['imagen_url'] ?? 'placeholder.jpg';
                $stock = $producto['stock'] ?? 0;
                $descuento_porcentaje = $producto['descuento_porcentaje'] ?? 0;
                
                // Calcular precio anterior (antes del descuento)
                $precio_anterior = 0;
                if ($descuento_porcentaje > 0) {
                    $precio_anterior = $precio / (1 - ($descuento_porcentaje / 100));
                }
            ?>
                
                <div class="product-card on-sale">
                    
                    <!-- Insignia de oferta -->
                    <span class="badge-oferta">
                        <?php if ($descuento_porcentaje > 0): ?>
                            -<?php echo intval($descuento_porcentaje); ?>%
                        <?php else: ?>
                            ¡Oferta!
                        <?php endif; ?>
                    </span>
                    
                    <!-- Imagen del Producto -->
                    <a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>">
                        <img src="<?php echo BASE_URL . 'producto_img/' . htmlspecialchars($imagen); ?>" 
                            alt="<?php echo htmlspecialchars($nombre); ?>" 
                            class="product-image-thumb" 
                            onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>producto_img/placeholder.jpg';"
                            loading="lazy">
                    </a>
                    
                    <div class="card-body">
                        <!-- Nombre del Producto -->
                        <h3 class="product-name">
                            <a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>">
                                <?php echo htmlspecialchars($nombre); ?>
                            </a>
                        </h3>
                        
                        <!-- Precios de Oferta y Anterior -->
                        <div class="product-price">
                            <?php if ($precio_anterior > 0 && $descuento_porcentaje > 0): ?>
                                <span class="precio-anterior">
                                    <del>$<?php echo number_format($precio_anterior, 2, ',', '.'); ?></del>
                                </span>
                            <?php endif; ?>
                            
                            <!-- Precio de Oferta (destacado) -->
                            <span class="precio-oferta">
                                $<?php echo number_format($precio, 2, ',', '.'); ?>
                            </span>
                        </div>
                        
                        <!-- Botón Agregar al Carrito -->
                        <?php if ($stock > 0): ?>
                            <form method="POST" action="<?php echo BASE_URL; ?>tienda/carrito.php" class="form-add-small">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="cantidad" value="1">
                                <button type="submit" class="btn-principal btn-sm">
                                    <i class="fa-solid fa-cart-plus"></i> Añadir al Carrito
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn-secundario-alerta btn-sm" disabled>Agotado</button>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php endforeach; ?>

        </div>
    <?php endif; ?>

    <!-- Enlace para ver todos los productos -->
    <div class="all-products-link" style="margin-top: 3rem; text-align: center;">
        <a href="<?php echo BASE_URL; ?>tienda/productos.php" class="btn-secundario">
            <i class="fa-solid fa-store"></i> Ver Todos los Productos
        </a>
    </div>

</main>

<?php
require_once '../includes/footer.php'; 
?>