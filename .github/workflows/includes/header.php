<?php
$page_title = $page_title ?? "NaturAll - Productos Naturales"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'NaturAll'; ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css"> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <header>
        <div class="top-bar">
            <i class="fa-solid fa-chevron-left arrow-icon"></i>
            <span>RETIRO EN EL LOCAL</span>
            <i class="fa-solid fa-chevron-right arrow-icon"></i>
        </div>
        
        <div class="main-header-wrapper">
            <div class="main-header-top">
                <div class="search-container">
                    <input type="text" placeholder="Buscar...">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <h1><i class="fa-solid fa-leaf" style="color: var(--verde-natural); "></i> NaturAll</h1>
                    </a>
                </div>
                <div class="header-icons">
                    <a href="<?php echo BASE_URL; ?>registro.php"><i class="fa-solid fa-user"></i></a> 
                    <a href="<?php echo BASE_URL; ?>carrito.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-count"><?php echo get_cart_item_count(); ?></span>
                    </a>
                </div>
            </div>
            
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Inicio</a></li>
                    <li><a href="<?php echo BASE_URL; ?>tienda/categorias.php">Categorias</a></li>
                    <li><a href="<?php echo BASE_URL; ?>tienda/ofertas.php">Ofertas</a></li>
                    <li><a href="<?php echo BASE_URL; ?>tienda/pedidos.php">Pedidos</a></li>
                    <li><a href="<?php echo BASE_URL; ?>contacto.php">Contacto</a></li>
                </ul>
            </nav>
    </header>
