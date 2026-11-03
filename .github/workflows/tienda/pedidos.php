<?php
// ¡ARCHIVO DE SEGUIMIENTO DE PEDIDOS!
session_start();

// Ruta de inclusión correcta para un archivo en la carpeta 'tienda/'
require_once '../includes/functions.php'; 

$page_title = "NaturAll - Seguimiento de Pedido";

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://naturall.local/');
}

// ----------------------------------------------------
// 1. CAPTURA DEL ID DE PEDIDO
// ----------------------------------------------------
$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pedido = null;
$items  = [];
$mensaje_status = '';

if ($pedido_id > 0) {
    $pdo = connect_db();

    if ($pdo) {
        try {
            // ----------------------------------------------------
            // 2. OBTENER CABECERA DEL PEDIDO (JOIN con estados_pedido)
            // ----------------------------------------------------
            $stmt_pedido = $pdo->prepare("
                SELECT 
                    p.id_pedido,
                    p.fecha_pedido,
                    p.total,
                    p.metodo_pago,
                    p.direccion_envio,
                    p.cliente_nombre,
                    p.cliente_email,
                    p.cliente_telefono,
                    p.id_estado,
                    ep.nombre_estado AS estado_nombre
                FROM pedidos p
                LEFT JOIN estados_pedido ep ON p.id_estado = ep.id_estado
                WHERE p.id_pedido = ?
                LIMIT 1
            ");
            $stmt_pedido->execute([$pedido_id]);
            $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

            if ($pedido) {
                // ----------------------------------------------------
                // 3. OBTENER LOS PRODUCTOS DEL PEDIDO
                // detalle_pedido + productos.nombre
                // ----------------------------------------------------
                $stmt_detalles = $pdo->prepare("
                    SELECT 
                        dp.id_producto,
                        pr.nombre AS producto_nombre,
                        dp.cantidad,
                        dp.precio_unitario,
                        dp.subtotal
                    FROM detalle_pedido dp
                    LEFT JOIN productos pr ON dp.id_producto = pr.id_producto
                    WHERE dp.id_pedido = ?
                    ORDER BY dp.id_detalle ASC
                ");
                $stmt_detalles->execute([$pedido_id]);
                $items = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $mensaje_status = '<div class="alerta error">❌ Error: No se encontró ningún pedido con el ID #' . htmlspecialchars($pedido_id) . '.</div>';
            }

        } catch (PDOException $e) {
            $mensaje_status = '<div class="alerta error">❌ Error al consultar la Base de Datos.</div>';
        }
    } else {
        $mensaje_status = '<div class="alerta error">❌ No se pudo conectar a la base de datos.</div>';
    }

} else {
    $mensaje_status = '<div class="alerta info">🔎 Por favor, ingresa un número de pedido para realizar el seguimiento.</div>';
}

require_once '../includes/header.php'; 
?>

<main class="container seguimiento-page">

    <section class="page-header">
        <h1>📦 Seguimiento de tu Pedido</h1>
        <p>Consulta el estado actual de tu compra.</p>
    </section>

    <?php echo $mensaje_status; ?>

    <?php if ($pedido): ?>

        <?php
        // =============================
        // ESTADO ACTUAL Y PROGRESO
        // =============================

        // Nombre del estado real que viene de la BD
        $estado_actual_bd = $pedido['estado_nombre'] ?? 'Pendiente';

        // Mapeamos estados posibles a iconos y orden
        // OJO: acá asumimos que tus estados en la tabla estados_pedido tienen
        // nombres similares a estos. Si son otros, podés renombrarlos.
        // El orden del array define el progreso visual.
        $estados_posibles = [
            'Pendiente'         => 'fa-credit-card',
            'Pago Confirmado'   => 'fa-clipboard-check',
            'En Preparación'    => 'fa-box-open',
            'Enviado'           => 'fa-truck-fast',
            'Entregado'         => 'fa-check-circle'
        ];

        // Buscamos la posición del estado_actual_bd dentro de $estados_posibles
        // Si no lo encontramos, lo dejamos como si fuera el primero
        $keys_estados = array_keys($estados_posibles);
        $estado_index = array_search($estado_actual_bd, $keys_estados);
        if ($estado_index === false) {
            $estado_index = 0;
        }

        // Valores formateados
        $fecha_pedido_legible = '—';
        if (!empty($pedido['fecha_pedido'])) {
            $ts = strtotime($pedido['fecha_pedido']);
            if ($ts !== false) {
                $fecha_pedido_legible = date('d/m/Y H:i', $ts);
            }
        }

        $total_formateado = '$' . number_format(($pedido['total'] ?? 0), 2, ',', '.');
        ?>

        <!-- DETALLE DE PEDIDO PRINCIPAL -->
        <div class="pedido-detalle">
            <h2>Pedido #<?php echo htmlspecialchars($pedido['id_pedido']); ?></h2>

            <p>
                <strong>Estado Actual:</strong>
                <span class="badge-estado badge-<?php echo strtolower(str_replace(' ', '-', $estado_actual_bd)); ?>">
                    <?php echo htmlspecialchars($estado_actual_bd); ?>
                </span>
            </p>

            <p><strong>Fecha:</strong> <?php echo $fecha_pedido_legible; ?></p>

            <p><strong>Total:</strong> <?php echo $total_formateado; ?></p>

            <p><strong>Método de Pago:</strong> 
                <?php echo htmlspecialchars($pedido['metodo_pago'] ?? ''); ?>
            </p>
        </div>

        <!-- BARRA DE PROGRESO -->
        <div class="tracking-progress-bar">
            <?php
            $i = 0;
            foreach ($estados_posibles as $estado_nombre => $icon_class):
                // done = pasos anteriores
                // active = paso actual
                $class = '';
                if ($i < $estado_index) {
                    $class = 'done';
                } elseif ($i == $estado_index) {
                    $class = 'active';
                }
            ?>
                <div class="tracking-step <?php echo $class; ?>">
                    <div class="step-icon">
                        <i class="fa-solid <?php echo $icon_class; ?>"></i>
                    </div>
                    <div class="step-label">
                        <?php echo htmlspecialchars($estado_nombre); ?>
                    </div>
                </div>
            <?php
                $i++;
            endforeach;
            ?>
        </div>

        <!-- LISTA DE PRODUCTOS COMPRADOS -->
        <h3 style="margin-top: 50px;">Artículos Comprados</h3>

        <table class="productos-comprados-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Precio Unitario</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            No se encontraron detalles de productos para este pedido.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <?php 
                                // si por alguna razón no se pudo hacer join con productos,
                                // mostramos el id_producto como fallback
                                echo htmlspecialchars(
                                    $it['producto_nombre'] ?? ('Producto #' . $it['id_producto'])
                                );
                                ?>
                            </td>
                            <td class="text-center"><?php echo (int)$it['cantidad']; ?></td>
                            <td class="text-center">
                                $<?php echo number_format(($it['precio_unitario'] ?? 0), 2, ',', '.'); ?>
                            </td>
                            <td class="text-right">
                                $<?php echo number_format(($it['subtotal'] ?? 0), 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>TOTAL FINAL:</strong></td>
                    <td class="text-right">
                        <strong><?php echo $total_formateado; ?></strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- DATOS DE ENVÍO -->
        <div class="datos-envio-container">
            <h3>Datos de Envío</h3>

            <p><strong>Cliente:</strong> 
                <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? ''); ?>
            </p>

            <p><strong>Dirección:</strong> 
                <?php
                // en tu BD guardamos direccion_envio como "Calle 123, Ciudad"
                echo htmlspecialchars($pedido['direccion_envio'] ?? '');
                ?>
            </p>

            <p><strong>Contacto:</strong> 
                <?php echo htmlspecialchars($pedido['cliente_telefono'] ?? ''); ?> 
                / 
                <?php echo htmlspecialchars($pedido['cliente_email'] ?? ''); ?>
            </p>
        </div>

        <p style="margin-top: 40px; text-align: center;">
            <a href="<?php echo BASE_URL; ?>tienda/productos.php" class="btn-secundario">
                ← Volver a Comprar
            </a>
        </p>

    <?php else: ?>

        <!-- FORM BUSCAR PEDIDO MANUALMENTE -->
        <form action="<?php echo BASE_URL; ?>tienda/pedidos.php" method="GET" class="search-form">
            <label for="id">Ingresa tu número de Pedido:</label>
            <div class="input-group">
                <input type="text" id="id" name="id" placeholder="Ej: 15" required>
                <button type="submit" class="btn-principal">Buscar Pedido</button>
            </div>
        </form>

    <?php endif; ?>

</main>

<?php
require_once '../includes/footer.php'; 
?>
