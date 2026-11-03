<?php
require_once '../includes/functions.php';
session_start();

// ----------------------------------------------------
// 0. Seguridad básica de admin
// ----------------------------------------------------
// Acá estás usando que el admin es id_usuario = 2. Bien.
// Si querés algo más fino (rol admin), después lo cambiamos.
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] != 2) {
    header("Location: ../registro.php");
    exit;
}

$page_title = "Gestión de Envíos y Pedidos";

// Conexión PDO
$pdo = connect_db();
if (!$pdo) {
    die("Error: no hay conexión a la base de datos.");
}

// ----------------------------------------------------
// 1. Leer catálogo de estados desde la tabla estados_pedido
//    Esto nos sirve para:
//    - Mostrar el estado legible
//    - Poblar el <select> para cambiar estado
// ----------------------------------------------------

$estados_catalogo = []; // [id_estado => nombre_estado]

try {
    $stmt_estados = $pdo->query("
        SELECT id_estado, nombre_estado
        FROM estados_pedido
        ORDER BY id_estado ASC
    ");
    $rows_estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows_estados as $row) {
        $estados_catalogo[(int)$row['id_estado']] = $row['nombre_estado'];
    }
} catch (PDOException $e) {
    // Si falla leer estados, igual seguimos pero sin combo
    $estados_catalogo = [];
}

// ----------------------------------------------------
// 2. Si el admin mandó un POST para cambiar el estado de un pedido
//    POST esperado:
//    - pedido_id
//    - nuevo_estado (id_estado nuevo)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['nuevo_estado'])) {
    $pedido_id_post = (int)$_POST['pedido_id'];
    $nuevo_estado_id = (int)$_POST['nuevo_estado'];

    // Validamos que el estado exista en el catálogo
    if ($pedido_id_post > 0 && array_key_exists($nuevo_estado_id, $estados_catalogo)) {
        try {
            $stmt_update = $pdo->prepare("
                UPDATE pedidos
                SET id_estado = ?
                WHERE id_pedido = ?
            ");
            $stmt_update->execute([$nuevo_estado_id, $pedido_id_post]);
        } catch (PDOException $e) {
            // Podrías loguear este error con error_log si querés
        }
    }

    // Post-Redirect-Get para que al refrescar no reenviemos el form
    header("Location: gestion_envios.php");
    exit;
}

// ----------------------------------------------------
// 3. Traer TODOS los pedidos reales de la tabla pedidos
//    con su estado legible y datos del cliente.
// ----------------------------------------------------
$pedidos = []; 
try {
    $stmt_pedidos = $pdo->query("
        SELECT
            p.id_pedido,
            p.cliente_nombre,
            p.cliente_email,
            p.cliente_telefono,
            p.direccion_envio,
            p.total,
            p.fecha_pedido,
            p.id_estado,
            ep.nombre_estado AS estado_nombre
        FROM pedidos p
        LEFT JOIN estados_pedido ep ON p.id_estado = ep.id_estado
        ORDER BY p.fecha_pedido DESC
    ");
    $pedidos_raw = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

    // Para cada pedido, buscamos sus productos
    $stmt_detalle = $pdo->prepare("
        SELECT 
            pr.nombre AS producto_nombre,
            dp.cantidad,
            dp.precio_unitario,
            dp.subtotal
        FROM detalle_pedido dp
        LEFT JOIN productos pr ON dp.id_producto = pr.id_producto
        WHERE dp.id_pedido = ?
        ORDER BY dp.id_detalle ASC
    ");

    foreach ($pedidos_raw as $p) {
        $pid = (int)$p['id_pedido'];

        $stmt_detalle->execute([$pid]);
        $items = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);

        $pedidos[] = [
            'id_pedido'        => $pid,
            'cliente_nombre'   => $p['cliente_nombre'],
            'cliente_email'    => $p['cliente_email'],
            'cliente_telefono' => $p['cliente_telefono'],
            'direccion_envio'  => $p['direccion_envio'],
            'total'            => $p['total'],
            'fecha_pedido'     => $p['fecha_pedido'],
            'id_estado'        => (int)$p['id_estado'],
            'estado_nombre'    => $p['estado_nombre'] ?? 'Pendiente',
            'items'            => $items,
        ];
    }

} catch (PDOException $e) {
    // si hay error leyendo pedidos
    $pedidos = [];
}

// ----------------------------------------------------
// 4. Incluimos la cabecera del panel admin
// ----------------------------------------------------
require_once '../includes/headeradmin.php'; 
?>

<h2>Pedidos Pendientes / En Curso / Entregados</h2>

<?php if (empty($pedidos)): ?>
    <div class="alerta exito">🎉 No hay pedidos registrados todavía.</div>
<?php endif; ?>

<?php foreach ($pedidos as $pedido): ?>
    <?php
        // Helpers visuales
        $fecha_legible = '—';
        if (!empty($pedido['fecha_pedido'])) {
            $ts = strtotime($pedido['fecha_pedido']);
            if ($ts !== false) {
                $fecha_legible = date('d/m/Y H:i', $ts);
            }
        }

        $total_legible = '$' . number_format(($pedido['total'] ?? 0), 2, ',', '.');

        // Clases CSS dinámicas para estado (ej: "estado-en-preparacion")
        $slug_estado = strtolower(str_replace(' ', '-', $pedido['estado_nombre'] ?? ''));
    ?>

    <section class="pedido card-gestion" style="border:1px solid #ccc; border-radius:8px; padding:1rem; margin-bottom:1.5rem; background:#fff;">
        <div class="pedido-header" style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:1rem;">
            <div>
                <h3 style="margin:0 0 .5rem 0;">
                    Pedido #<?php echo htmlspecialchars($pedido['id_pedido']); ?>
                </h3>
                <p style="margin:0; font-size:0.9rem; color:#555;">
                    <strong>Fecha:</strong> <?php echo $fecha_legible; ?><br>
                    <strong>Estado Actual:</strong>
                    <span class="estado-<?php echo $slug_estado; ?>" style="font-weight:bold;">
                        <?php echo htmlspecialchars($pedido['estado_nombre'] ?? 'Pendiente'); ?>
                    </span><br>
                    <strong>Total:</strong> <?php echo $total_legible; ?>
                </p>
            </div>

            <!-- Form para cambiar estado -->
            <form action="gestion_envios.php" method="POST" style="margin-left:auto; min-width:200px;">
                <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($pedido['id_pedido']); ?>">
                
                <label for="estado_<?php echo $pedido['id_pedido']; ?>" style="font-size:0.9rem; font-weight:600; display:block; margin-bottom:0.25rem;">
                    Cambiar Estado:
                </label>
                <select name="nuevo_estado" id="estado_<?php echo $pedido['id_pedido']; ?>" style="width:100%; padding:0.4rem; border-radius:4px; border:1px solid #aaa; margin-bottom:0.5rem;">
                    <?php foreach ($estados_catalogo as $id_estado => $nombre_estado): ?>
                        <option 
                            value="<?php echo $id_estado; ?>" 
                            <?php echo ($id_estado === $pedido['id_estado']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($nombre_estado); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-pequeno" style="padding:0.4rem 0.6rem; border:none; border-radius:4px; background:#4a6741; color:#fff; cursor:pointer;">
                    Actualizar
                </button>
            </form>
        </div>
        
        <hr style="margin:1rem 0; border:none; border-top:1px solid #e0e0e0;">

        <p style="margin:0 0 .5rem 0;">
            <strong>Cliente:</strong>
            <?php echo htmlspecialchars($pedido['cliente_nombre']); ?>
            <br>
            <strong>Contacto:</strong>
            <?php echo htmlspecialchars($pedido['cliente_telefono']); ?>
            /
            <?php echo htmlspecialchars($pedido['cliente_email']); ?>
            <br>
            <strong>Dirección de envío:</strong>
            <?php echo htmlspecialchars($pedido['direccion_envio']); ?>
        </p>

        <div style="margin-top:1rem;">
            <h4 style="margin:0 0 .5rem 0;">Productos:</h4>
            <?php if (empty($pedido['items'])): ?>
                <div class="alerta info" style="font-size:0.9rem; color:#666; background:#f8f9fa; padding:0.75rem; border-radius:4px;">
                    No hay detalle de productos para este pedido.
                </div>
            <?php else: ?>
                <ul style="list-style:disc; padding-left:1.2rem; margin:0;">
                    <?php foreach ($pedido['items'] as $it): ?>
                        <li style="margin-bottom:0.4rem;">
                            <?php echo htmlspecialchars($it['cantidad']); ?>x 
                            <?php echo htmlspecialchars($it['producto_nombre'] ?? ('Producto #' . $it['id_producto'])); ?>
                            <span style="color:#555; font-size:0.9rem;">
                                - $<?php echo number_format(($it['precio_unitario'] ?? 0), 2, ',', '.'); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div style="text-align:right; margin-top:0.5rem; font-weight:600;">
                    Total: <?php echo $total_legible; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>


<?php
// ----------------------------------------------------
// 5. Footer admin
// ----------------------------------------------------
require_once '../includes/footer.php'; 
?>
