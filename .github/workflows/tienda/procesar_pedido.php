<?php
// ¡ARCHIVO DE CHECKOUT!
session_start();

// Ruta de inclusión correcta para un archivo dentro de 'tienda/'
require_once '../includes/functions.php'; 

// ----------------------------------------------------
// VERIFICACIONES PREVIAS AL CHECKOUT
// ----------------------------------------------------

$page_title = "NaturAll - Finalizar Compra";
$cart_total = get_cart_total(); 
$mensaje_status = '';
$error = false;
// $pedido_finalizado ya no se usa, la lógica de éxito redirige

// 1. Verificar si el carrito está vacío
if (empty($_SESSION['cart'])) {
    $mensaje_status = '<div class="alerta info">🛒 Tu carrito está vacío. ¡No puedes finalizar un pedido!</div>';
    require_once '../includes/header.php';
    echo '<main class="container">';
    echo '<section class="checkout-wrapper">' . $mensaje_status . '</section>';
    echo '</main>';
    require_once '../includes/footer.php';
    exit();
}

// ----------------------------------------------------
// 2. LÓGICA DE PROCESAMIENTO DEL PEDIDO (BASE DE DATOS)
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2.1. Captura y sanitización de datos
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $direccion = htmlspecialchars(trim($_POST['direccion'] ?? ''));
    $ciudad = htmlspecialchars(trim($_POST['ciudad'] ?? ''));
    $metodo_pago = htmlspecialchars(trim($_POST['metodo_pago'] ?? 'Efectivo'));

    // 2.2. Validación simple
    if (empty($nombre) || empty($telefono) || empty($email) || empty($direccion) || empty($ciudad) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje_status = '<div class="alerta error">❌ Error: Todos los campos son obligatorios y el correo debe ser válido.</div>';
        $error = true;
    } else {
        // 2.3. Lógica para FINALIZAR EL PEDIDO y guardar en la DB

        $detalles_carrito = $_SESSION['cart'];
        $datos_cliente = compact('nombre', 'telefono', 'email', 'direccion', 'ciudad', 'metodo_pago', 'cart_total');
        
        // GUARDAR EL PEDIDO EN LA BASE DE DATOS
        $id_pedido = guardar_pedido_real($datos_cliente, $detalles_carrito); 

        if ($id_pedido) { 
            // Éxito:
            unset($_SESSION['cart']); 
            
            // Redireccionar a la página de SEGUIMIENTO DE PEDIDOS
            header('Location: ' . BASE_URL . 'tienda/pedidos.php?id=' . $id_pedido); 
            exit();

        } else {
            // Este mensaje aparecerá si hay un fallo en la DB (usamos la variable $error)
            $mensaje_status = '<div class="alerta error">❌ Hubo un problema al registrar tu pedido en la Base de Datos. Por favor, inténtalo de nuevo.</div>';
            $error = true;
        }
    }
}

// Variables POST para rellenar los campos si hubo un error 
$nombre_val = htmlspecialchars($_POST['nombre'] ?? '');
$telefono_val = htmlspecialchars($_POST['telefono'] ?? '');
$email_val = htmlspecialchars($_POST['email'] ?? '');
$direccion_val = htmlspecialchars($_POST['direccion'] ?? '');
$ciudad_val = htmlspecialchars($_POST['ciudad'] ?? '');

// ----------------------------------------------------
// 3. INICIO DE LA VISTA
// ----------------------------------------------------

require_once '../includes/header.php';
?>

<main class="container checkout-page">
    <section class="page-header">
        <h1>🛒 Finalizar Compra</h1>
        <p>Revisa tu pedido e ingresa tus datos de envío.</p>
    </section>

    <?php echo $mensaje_status; ?>

    <div class="checkout-wrapper">
        
        <div class="checkout-form-container">
            <form action="<?php echo BASE_URL; ?>tienda/procesar_pedido.php" method="post" class="form-pedido">
                
                <h2>1. Información del Cliente y Envío</h2>
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required value="<?php echo $nombre_val; ?>">
                
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" placeholder="Tu número de teléfono" required value="<?php echo $telefono_val; ?>">
                
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required value="<?php echo $email_val; ?>">
                
                <label for="direccion">Dirección de Envío</label>
                <input type="text" id="direccion" name="direccion" placeholder="Calle, número, piso, dpto." required value="<?php echo $direccion_val; ?>">
                
                <label for="ciudad">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" placeholder="Tu ciudad" required value="<?php echo $ciudad_val; ?>">
                
                <h2 style="margin-top: 30px;">2. Método de Pago</h2>
                <div class="radio-group">
                    <input type="radio" id="pago_efectivo" name="metodo_pago" value="Efectivo" checked>
                    <label for="pago_efectivo">Efectivo al Retirar/Entregar</label><br>

                    <input type="radio" id="pago_transferencia" name="metodo_pago" value="Transferencia" disabled>
                    <label for="pago_transferencia">Transferencia Bancaria (Próximamente)</label><br>
                </div>

                <div class="finalizar-btn-container">
                    <button type="submit" class="btn-principal large-btn">
                        PAGAR AHORA Y FINALIZAR PEDIDO ($<?php echo number_format($cart_total, 2, ',', '.'); ?>)
                    </button>
                </div>
            </form>
        </div>

        <div class="order-summary">
            <h2>3. Resumen de tu Pedido</h2>
            
            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                <div class="summary-item">
                    <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                    <span class="item-qty">x<?php echo $item['quantity']; ?></span>
                    <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="summary-separator"></div>

            <div class="summary-total">
                <span class="total-label">Subtotal</span>
                <span class="total-value">$<?php echo number_format($cart_total, 2, ',', '.'); ?></span>
            </div>
            
            <div class="summary-total shipping">
                <span class="total-label">Envío</span>
                <span class="total-value">A calcular / Retiro</span>
            </div>
            
            <div class="summary-separator bold"></div>

            <div class="summary-grand-total">
                <span class="total-label">Total Final</span>
                <span class="total-value grand-total-amount">$<?php echo number_format($cart_total, 2, ',', '.'); ?></span>
            </div>
            
            <a href="<?php echo BASE_URL; ?>carrito.php" class="btn-secundario-alerta btn-sm" style="margin-top: 20px;">
                ← Modificar Carrito
            </a>
        </div>
        
    </div>

</main>

<?php
require_once '../includes/footer.php'; 
?>