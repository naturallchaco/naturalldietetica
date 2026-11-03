<?php
require_once '../includes/functions.php'; 

$page_title = "Explorar Categorías | NaturAll";

/*
    Definimos las categorías disponibles.
    IMPORTANTE:
    'imagen_url' debe coincidir con el nombre real del archivo que vos tenés en /producto_img/
    Ejemplo: producto_img/veganos.jpg
*/
$categorias = [
    [
        'nombre'        => 'Cero Azúcares',
        'slug_bd'       => 'cero_azucares',
        'descripcion'   => 'Productos sin azúcar para una dieta más controlada.',
        'imagen_url'    => 'cero_azucares.jpg',
        'archivo_php'   => 'productos_cero_azucares.php'
    ],
    [
        'nombre'        => 'Sin TACC',
        'slug_bd'       => 'sin_tacc',
        'descripcion'   => 'Opciones sin gluten para una alimentación consciente.',
        'imagen_url'    => 'sin_tacc.jpg',
        'archivo_php'   => 'productos_sin_tacc.php'
    ],
    [
        'nombre'        => 'Veganos',
        'slug_bd'       => 'veganos',
        'descripcion'   => 'Alimentos 100% vegetales, libres de ingredientes animales.',
        'imagen_url'    => 'veganos.jpg',
        'archivo_php'   => 'productos_veganos.php'
    ],
    [
        'nombre'        => 'Lácteos',
        'slug_bd'       => 'lacteos',
        'descripcion'   => 'Leches, yogures y quesos orgánicos y tradicionales.',
        'imagen_url'    => 'lacteos.jpg',
        'archivo_php'   => 'productos_lacteos.php'
    ],
    [
        'nombre'        => 'Panificados',
        'slug_bd'       => 'panificados',
        'descripcion'   => 'Panes, galletas y bollería artesanales.',
        'imagen_url'    => 'panificados.jpg',
        'archivo_php'   => 'productos_panificados.php'
    ],
    [
        'nombre'        => 'Bebidas',
        'slug_bd'       => 'bebidas',
        'descripcion'   => 'Jugos, infusiones y bebidas naturales.',
        'imagen_url'    => 'bebidas.jpg',
        'archivo_php'   => 'productos_bebidas.php'
    ],
    [
        'nombre'        => 'Congelados',
        'slug_bd'       => 'congelados',
        'descripcion'   => 'Alimentos preparados y listos para guardar en el freezer.',
        'imagen_url'    => 'congelados.jpg',
        'archivo_php'   => 'productos_congelados.php'
    ],
    [
        'nombre'        => 'A Granel',
        'slug_bd'       => 'granel',
        'descripcion'   => 'Frutos secos, legumbres y cereales por peso.',
        'imagen_url'    => 'a_granel.jpg',
        'archivo_php'   => 'productos_a_granel.php'
    ],
];

require_once '../includes/header.php'; 
?>

<main class="container category-page">

    <section class="page-header">
        <h1>Explorar por Categoría</h1>
        <p>Haz clic en la imagen de la sección que deseas explorar.</p>
        
        <!-- Botón para ver todo el catálogo -->
        <div class="all-products-link">
            <a href="<?php echo BASE_URL; ?>tienda/productos.php" class="btn-principal large">
                <i class="fa-solid fa-store"></i> Ver todo el catálogo de productos
            </a>
        </div>
    </section>

    <div class="category-grid image-mode">
        
        <?php foreach ($categorias as $categoria): ?>
            <?php 
                // URL a la subcategoría real
                $target_url = BASE_URL . 'tienda/subcategorias/' . $categoria['archivo_php'];

                // Ruta a la imagen de categoría dentro de /producto_img/
                // OJO: BASE_URL termina con / así que queda ej: http://naturall.local/producto_img/veganos.jpg
                $img_src = BASE_URL . 'producto_img/' . $categoria['imagen_url'];
            ?>
            
            <!-- TARJETA CATEGORÍA -->
            <a href="<?php echo htmlspecialchars($target_url); ?>" class="category-card-link">
                <div class="category-card image-card">

                    <img src="<?php echo htmlspecialchars($img_src); ?>" 
                        alt="Categoría <?php echo htmlspecialchars($categoria['nombre']); ?>" 
                        class="category-image"
                        onerror="this.onerror=null; this.src='https://placehold.co/400x300/e8c547/4a6741?text=SIN+IMAGEN';"
                        loading="lazy">

                    <div class="category-overlay">
                        <h3><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
                    </div>
                </div>
            </a>

        <?php endforeach; ?>

    </div>
</main>

<?php
require_once '../includes/footer.php'; 
?>
