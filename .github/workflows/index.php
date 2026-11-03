<?php
// Iniciar la sesión al comienzo de cualquier archivo que la use.
session_start();

// 1. Incluir el archivo de funciones (donde se define BASE_URL y otras utilidades)
require_once 'includes/functions.php'; 

// 2. Lógica para obtener productos que aparecerán en la portada
// Obtenemos los productos en oferta para mostrarlos como destacados.
$productos_en_oferta = obtener_productos_en_oferta(false); // Obtener solo con stock > 0
$productos_destacados = array_slice($productos_en_oferta, 0, 4); // Tomar solo los primeros 4

// NOTA: Si necesitas categorías, puedes crear una función llamada obtener_categorias_principales()
// $categorias_principales = obtener_categorias_principales(); 


// 3. Establecer el título de la página y luego incluir la cabecera
$page_title = "NaturAll | Tienda de Productos Naturales";
require_once 'includes/header.php';

// ----------------------------------------------------
// 4. CONTENIDO ESPECÍFICO DE LA PÁGINA PRINCIPAL (VISUAL Y PRODUCTIVO)
// ----------------------------------------------------
?>

<main>

    <section class="hero-slider">
        <div class="hero-content">
            <h2>¡Bienvenido a NaturAll! Vive más sano.</h2>
            <p>Descubre nuestros productos frescos, orgánicos y a granel.</p>
            <a href="<?php echo BASE_URL; ?>tienda/productos.php" class="btn-principal btn-xl">
                Comprar Ahora <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        </section>

    
    <?php if (!empty($productos_en_oferta)): ?>
    <section class="promo-bar">
        <div class="container">
            <p>🔥 ¡No te lo pierdas! Tenemos <?php echo count($productos_en_oferta); ?> productos en OFERTA por tiempo limitado. <a href="<?php echo BASE_URL; ?>tienda/ofertas.php">Ver todas las ofertas aquí</a>.</p>
        </div>
    </section>
    <?php endif; ?>

    <section class="productos-destacados container">
        <h3><?php echo count($productos_destacados) > 0 ? '✨ Super Ofertas Destacadas' : 'Categorías Populares'; ?></h3>
        
        <?php if (!empty($productos_destacados)): ?>
        <div class="product-grid destacados-grid">
            
            <?php foreach ($productos_destacados as $producto): 
                $id = $producto['id_producto'];
                $nombre = $producto['nombre'];
                $precio_original = $producto['precio_unitario'] ?? 0.00;
                $imagen = $producto['imagen_url'] ?? 'placeholder.jpg';
                $descuento_porcentaje = $producto['descuento_porcentaje'] ?? 0;
                
                // Calculamos el precio final de oferta
                $precio_oferta = $precio_original * (1 - ($descuento_porcentaje / 100));
            ?>
                <div class="product-card">
                    <span class="badge-oferta">-<?php echo intval($descuento_porcentaje); ?>%</span>
                    <a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>">
                        <img src="<?php echo BASE_URL . 'producto_img/' . htmlspecialchars($imagen); ?>" 
                            alt="<?php echo htmlspecialchars($nombre); ?>" 
                            onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>producto_img/placeholder.jpg';"
                            loading="lazy">
                    </a>
                    <div class="card-body">
                        <h4 class="product-name"><a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></a></h4>
                        <div class="product-price">
                            <span class="precio-anterior"><del>$<?php echo number_format($precio_original, 2, ',', '.'); ?></del></span>
                            <span class="precio-actual">$<?php echo number_format($precio_oferta, 2, ',', '.'); ?></span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>tienda/producto_detalle.php?id=<?php echo $id; ?>" class="btn-principal btn-sm">Ver Detalle</a>
                    </div>
                </div>
            <?php endforeach; ?>
            
        </div>
        <?php else: ?>
            <div class="alerta info">No hay ofertas destacadas disponibles en este momento.</div>
        <?php endif; ?>
    </section>

    <!-- CÓMO FUNCIONA / INFO DE COMPRA -->
    <section class="info-compra container" style="padding:2rem 1rem;">
        <h3 style="
            text-align:center;
            color:#4a6741;
            margin:0 0 1rem 0;
        ">
            🧾 ¿Cómo hago mi pedido?
        </h3>

        <div class="info-grid" style="
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
            gap:1rem;
        ">

            <div class="info-card" style="
                background:#fff;
                border:1px solid #e0e0e0;
                border-radius:8px;
                box-shadow:0 8px 20px rgba(0,0,0,0.03);
                padding:1rem 1rem 1.2rem;
                text-align:left;
            ">
                <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.5rem;">
                    <div style="
                        background:#4a6741;
                        color:#fff;
                        font-weight:600;
                        font-size:.8rem;
                        width:32px;
                        height:32px;
                        border-radius:6px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    ">1</div>
                    <div style="font-weight:600; color:#333; font-size:1rem;">
                        Elegí tus productos
                    </div>
                </div>
                <p style="margin:0; font-size:.9rem; color:#555; line-height:1.4;">
                    Entrá a la tienda y agregá lo que quieras al carrito. Podés ver precios finales y stock real.
                </p>
                <a href="<?php echo BASE_URL; ?>tienda/productos.php" style="
                    display:inline-block;
                    margin-top:.75rem;
                    font-size:.85rem;
                    color:#4a6741;
                    text-decoration:underline;
                    font-weight:600;
                ">
                    Ir a la tienda →
                </a>
            </div>

            <div class="info-card" style="
                background:#fff;
                border:1px solid #e0e0e0;
                border-radius:8px;
                box-shadow:0 8px 20px rgba(0,0,0,0.03);
                padding:1rem 1rem 1.2rem;
                text-align:left;
            ">
                <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.5rem;">
                    <div style="
                        background:#e8c547;
                        color:#4a6741;
                        font-weight:600;
                        font-size:.8rem;
                        width:32px;
                        height:32px;
                        border-radius:6px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    ">2</div>
                    <div style="font-weight:600; color:#333; font-size:1rem;">
                        Confirmá el pedido
                    </div>
                </div>
                <p style="margin:0; font-size:.9rem; color:#555; line-height:1.4;">
                    Cargás tu nombre, dirección y forma de pago. Listo: el pedido queda registrado y te damos tu número de seguimiento.
                </p>
                <a href="<?php echo BASE_URL; ?>carrito.php" style="
                    display:inline-block;
                    margin-top:.75rem;
                    font-size:.85rem;
                    color:#4a6741;
                    text-decoration:underline;
                    font-weight:600;
                ">
                    Ver mi carrito →
                </a>
            </div>

            <div class="info-card" style="
                background:#fff;
                border:1px solid #e0e0e0;
                border-radius:8px;
                box-shadow:0 8px 20px rgba(0,0,0,0.03);
                padding:1rem 1rem 1.2rem;
                text-align:left;
            ">
                <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.5rem;">
                    <div style="
                        background:#4a6741;
                        color:#fff;
                        font-weight:600;
                        font-size:.8rem;
                        width:32px;
                        height:32px;
                        border-radius:6px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    ">3</div>
                    <div style="font-weight:600; color:#333; font-size:1rem;">
                        Recibí tu compra
                    </div>
                </div>
                <p style="margin:0; font-size:.9rem; color:#555; line-height:1.4;">
                    Podés retirarlo o coordinar entrega. Además podés seguir el estado en vivo (Preparando, En Camino, Entregado).
                </p>
                <a href="<?php echo BASE_URL; ?>tienda/pedidos.php" style="
                    display:inline-block;
                    margin-top:.75rem;
                    font-size:.85rem;
                    color:#4a6741;
                    text-decoration:underline;
                    font-weight:600;
                ">
                    Seguir mi pedido →
                </a>
            </div>

        </div>
    </section>


</main>

<?php
// 5. Incluir el pie de página
require_once 'includes/footer.php'; 
?>