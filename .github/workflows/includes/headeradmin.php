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
                    <a href="..\index.php">
                        <h1><i class="fa-solid fa-leaf" style="color: var(--verde-natural); "></i> NaturAll</h1>
                    </a>
                </div>
                <div class="header-icons">
                    <a href="..\..\registro.php"><i class="fa-solid fa-user"></i></a> 
                    <a href="tienda/carrito.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
            
            <nav>
                <ul>
                    <li><a href="gestion_envios.php">Envios</a></li>
                    <li><a href="gestion_personal.php">Personal</a></li>
                    <li><a href="gestion_proveedores.php">Proveedores</a></li>
                    <li><a href="gestion_stock.php">Stock</a></li>
                    <li><a href="gestion_ventas.php">Ventas</a></li>
                </ul>
            </nav>
    </header>