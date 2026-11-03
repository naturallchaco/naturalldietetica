<?php
// Incluimos el archivo de configuración. Este archivo DEBE definir la variable $pdo.
require_once __DIR__ . '/../tienda/config/db.php'; 
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://naturall.local/');
}

// =================================================================
// FUNCIONES DE UTILIDAD Y CONEXIÓN
// =================================================================

/**
 * Establece la conexión a la Base de Datos usando PDO.
 * El objeto $pdo es definido en el archivo db.php que se incluyó al inicio.
 * @return PDO|null Objeto PDO conectado.
 */
function connect_db() {
    global $pdo;
    if (isset($pdo) && $pdo instanceof PDO) {
        return $pdo;
    } 
    return $pdo ?? null; 
}


/**
 * Función de utilidad para sanitizar datos de entrada.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


// =================================================================
// FUNCIONES DE AUTENTICACIÓN Y USUARIOS
// =================================================================

function login($email, $password)
{
    global $pdo;
    
    // 💡 SOLUCIÓN: Sanitizamos el email con trim() para eliminar espacios en blanco
    $email = trim($email); 

    // 1. Iniciar la Sesión
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 2. Buscar el Usuario
    $stmt = $pdo->prepare("SELECT id_usuario, id_rol, password FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h3>--- DEBUG LOGIN ---</h3>";
    // 💡 También sanitizamos aquí para el debug
    echo "Email intentado (Sanitizado): " . htmlspecialchars($email) . "<br>";
    
    if (!$user) {
        echo "❌ **Resultado 1:** Usuario no encontrado.<br>";
        return false;
    }
    
    $hashed_password_db = $user['password'];
    echo "✅ **Resultado 1:** Usuario encontrado. ID: " . $user['id_usuario'] . "<br>";
    echo "Hash en BDD: <code>" . htmlspecialchars($hashed_password_db) . "</code><br>";
    
    // 3. Verificar Contraseña
    if (password_verify($password, $hashed_password_db)) {
        echo "✅ **Resultado 2:** ¡Contraseña verificada correctamente! Login exitoso.<br>";
        
        // 4. Asignar Sesión
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['id_rol'] = $user['id_rol'];
        // Asignar nombre/apellido si son necesarios, pero para la prueba no son críticos.

        // Actualizar último login (opcional)
        $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = ?")->execute([$user['id_usuario']]);
        
        // Finalizar el DEBUG y redirigir
        echo "Redirigiendo... <a href='index.php'>Clic aquí para ir a Index</a>";
        // header('Location: index.php'); // Descomentar para producción
        // exit; // Descomentar para producción
        return true;
        
    } else {
        echo "❌ **Resultado 2:** Fallo en password_verify(). Contraseña incorrecta.<br>";
        // ⚠️ Si ves este mensaje con una cuenta nueva, revisa la función registrar_usuario().
        return false;
    }
}

function email_existe($email)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() ? true : false;
}

function registrar_usuario($id_rol, $nombre, $apellido, $email, $telefono, $direccion_principal, $password)
{
    global $pdo;
    // ⚠️ CRÍTICO: Usar password_hash() para el registro
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (id_rol, nombre, apellido, email, telefono, direccion_principal, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$id_rol, $nombre, $apellido, $email, $telefono, $direccion_principal, $password_hash]);
}

function obtener_usuario_por_id($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, email, id_rol, foto_perfil, ultimo_login FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function iniciar_recuperacion($email)
{
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $pdo->prepare("UPDATE usuarios SET token_recuperacion = ?, expiracion_token = ? WHERE email = ?");
    if ($stmt->execute([$token, $expiracion, $email])) {
        $link = BASE_URL . "cambiar-password.php?token=" . $token; // CORREGIDO: Usar BASE_URL
        error_log("Recuperación: $link"); 
        return true;
    }
    return false;
}

function verificar_token_recuperacion($token)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE token_recuperacion = ? AND expiracion_token > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

function cambiar_password_por_token($token, $password)
{
    global $pdo;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL, expiracion_token = NULL WHERE token_recuperacion = ?");
    return $stmt->execute([$password_hash, $token]);
}

function editar_perfil($id, $nombre, $email, $foto_perfil)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, foto_perfil = ? WHERE id_usuario = ?");
    return $stmt->execute([$nombre, $email, $foto_perfil, $id]);
}

function subir_foto_perfil($file)
{
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed) || $file['size'] > 2 * 1024 * 1024) {
        return false;
    }
    $nombre = uniqid('perfil_') . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], "uploads/" . $nombre)) {
        return $nombre;
    }
    return false;
}

// Para el panel de admin
function obtener_todos_usuarios()
{
    global $pdo;
    $stmt = $pdo->query("SELECT id_usuario, nombre, email, id_rol, fecha_registro, ultimo_login FROM usuarios ORDER BY fecha_registro DESC");
    return $stmt->fetchAll();
}

function eliminar_usuario($id)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    return $stmt->execute([$id]);
}


// =================================================================
// FUNCIONES DE INVENTARIO Y PRODUCTOS
// =================================================================

function obtener_inventario() {
    global $pdo;
    if (!isset($pdo) || !$pdo) return [];

    $stmt = $pdo->query("
        SELECT 
            p.id_producto,
            p.nombre,
            p.precio_unitario,
            p.stock,
            p.imagen_url,
            p.es_oferta,
            c.nombre_categoria AS categoria_nombre
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        ORDER BY c.nombre_categoria, p.nombre
    ");

    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inventario = [];

    foreach ($productos as $p) {
        // normalizamos el nombre de la categoría de la BD
        $cat_key = normalizar_categoria_slug($p['categoria_nombre']);

        if (!isset($inventario[$cat_key])) {
            $inventario[$cat_key] = [];
        }

        $inventario[$cat_key][] = $p;
    }

    return $inventario;
}



function normalizar_categoria_slug($nombre_categoria_raw) {
    // 1) bajamos todo a minúsculas
    $slug = mb_strtolower(trim($nombre_categoria_raw), 'UTF-8');

    // 2) reemplazamos espacios por guiones bajos
    $slug = str_replace(' ', '_', $slug);

    // 3) sacamos acentos comunes
    $buscar  = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ä', 'ë', 'ï', 'ö', 'ü'];
    $reempl  = ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u'];
    $slug = str_replace($buscar, $reempl, $slug);

    // 4) casos especiales que querés controlar vos a mano
    // ejemplo: "a granel" en DB puede venir como "granel"
    $equivalencias = [
        'a_granel'     => 'granel',
        'granel'       => 'granel',
        'cero_azucares'=> 'cero_azucares',
        'sin_tacc'     => 'sin_tacc',
        'vegano'       => 'veganos',
        'veganos'      => 'veganos',
        'vegana'       => 'veganos',
        'lacteos'      => 'lacteos',
        'lácteos'      => 'lacteos',
        'lacteos_'     => 'lacteos',
        'panificados'  => 'panificados',
        'bebidas'      => 'bebidas',
        'congelados'   => 'congelados',
        'congelado'    => 'congelados',
    ];

    if (isset($equivalencias[$slug])) {
        return $equivalencias[$slug];
    }

    return $slug;
}



/**
 * Obtiene todos los productos que están en oferta
 */
function obtener_productos_en_oferta($incluir_sin_stock = false) {
    global $pdo;
    if (!isset($pdo) || !$pdo) {
        // Si la conexión falla, se detiene aquí.
        echo "<h3>ERROR DE CONEXIÓN A BASE DE DATOS EN FUNCIÓN DE OFERTAS</h3>";
        return [];
    }
    
    try {
        // Aseguramos que se seleccionen todas las columnas necesarias
        $sql = "SELECT 
                        p.id_producto,
                        p.nombre,
                        p.descripcion,
                        p.precio_unitario,
                        p.stock,
                        p.imagen_url,
                        p.es_oferta,
                        p.descuento_porcentaje,
                        c.nombre_categoria AS categoria
                    FROM productos p
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.es_oferta = 1"; 
        
        if (!$incluir_sin_stock) {
            $sql .= " AND p.stock > 0"; 
        }
        
        $sql .= " ORDER BY p.descuento_porcentaje DESC, p.nombre ASC"; 
        
        $stmt = $pdo->query($sql);
        
        if ($stmt) {
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $productos;
        }
        return [];
        
    } catch (PDOException $e) {
        error_log("Error al obtener productos en oferta: " . $e->getMessage());
        return [];
    }
}

function agregar_producto($id_proveedor, $id_categoria, $nombre, $precio_unitario, $stock) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO productos (id_proveedor, id_categoria, nombre, precio_unitario, stock) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$id_proveedor, $id_categoria, $nombre, $precio_unitario, $stock]);
}


// =================================================================
// FUNCIONES DE PROVEEDORES
// =================================================================

function obtener_proveedores_con_productos() {
    global $pdo;
    if (!isset($pdo) || !$pdo) return [];

    $stmt = $pdo->query("SELECT id_proveedor, nombre_proveedor, contacto, telefono FROM proveedores ORDER BY nombre_proveedor");
    $proveedores = $stmt->fetchAll();

    foreach ($proveedores as &$prov) {
        $stmt2 = $pdo->prepare("SELECT id_producto, nombre, precio_unitario, stock FROM productos WHERE id_proveedor = ?");
        $stmt2->execute([$prov['id_proveedor']]);
        $prov['productos'] = $stmt2->fetchAll();
    }

    return $proveedores;
}

function agregar_proveedor($nombre_proveedor, $contacto, $telefono) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO proveedores (nombre_proveedor, contacto, telefono) VALUES (?, ?, ?)");
    return $stmt->execute([$nombre_proveedor, $contacto, $telefono]);
}


// =================================================================
// FUNCIONES DE CARRITO Y PEDIDOS (CORREGIDAS)
// =================================================================

/**
 * Obtiene los detalles completos de un producto.
 */
function obtener_producto_por_id($id_producto) {
    global $pdo;
    if (!isset($pdo) || !$pdo) return false;

    $stmt = $pdo->prepare("
        SELECT 
            p.id_producto, 
            p.nombre, 
            p.descripcion, 
            p.precio_unitario, 
            p.stock, 
            p.imagen_url,
            c.nombre_categoria
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.id_producto = ?
    ");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC); 
    
    if ($producto) {
        $producto['stock'] = (int)($producto['stock'] ?? 0);
    }
    
    return $producto;
}


/**
 * Inicializa el array de carrito en la sesión si aún no existe.
 */
function initialize_cart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Añade un producto al carrito de compras.
 */
function add_to_cart($product_id, $quantity, $price, $name) {
    initialize_cart();

    if ($quantity <= 0) {
        return false;
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
        ];
    }
    return true;
}

/**
 * Calcula el total monetario del carrito.
 * @return float El total a pagar.
 */
function get_cart_total() {
    if (!isset($_SESSION['cart'])) {
        return 0.00;
    }
    $total = 0.00;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

/**
 * Calcula el número total de artículos (unidades) en el carrito.
 * @return int El total de unidades en el carrito.
 */
function get_cart_item_count() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'] ?? 0;
        }
    }
    return $count;
}


/**
 * Guarda el pedido completo en la Base de Datos.
 * **CORREGIDO** el error de la sentencia INSERT de detalle_pedido.
 * @param array $datos_cliente Datos del cliente y del pedido.
 * @param array $detalles_carrito Productos del carrito.
 * @return int|false ID del nuevo pedido o false si falla.
 */
function guardar_pedido_real($datos_cliente, $detalles_carrito) {
    global $pdo; 
    
    if (!isset($pdo) || !$pdo) {
        // no hay conexión PDO
        return false; 
    }

    try {
        // Modo estricto de errores y transacción
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $pdo->beginTransaction();

        // Si el usuario no está logueado, lo guardamos como NULL (tu columna lo permite)
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        // dirección final tipo "Calle 123, Ciudad"
        $direccion_completa = $datos_cliente['direccion'] . ', ' . $datos_cliente['ciudad'];

        // IMPORTANTE:
        // Tu tabla pedidos tiene:
        // id_usuario, cliente_nombre, cliente_email, cliente_telefono,
        // id_estado (default '1' en tu schema), fecha_pedido (default current_timestamp),
        // total, metodo_pago, direccion_envio
        //
        // Entonces NO necesitamos pasar id_estado ni fecha_pedido manualmente.
        // Los deja la BD con sus defaults.

        $stmt_pedido = $pdo->prepare("
            INSERT INTO pedidos (
                id_usuario,
                cliente_nombre,
                cliente_email,
                cliente_telefono,
                total,
                metodo_pago,
                direccion_envio
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt_pedido->execute([
            $id_usuario,                     // puede ser NULL si no hay login
            $datos_cliente['nombre'],
            $datos_cliente['email'],
            $datos_cliente['telefono'],
            $datos_cliente['cart_total'],
            $datos_cliente['metodo_pago'],
            $direccion_completa
        ]);

        // id del pedido recién creado (nro de seguimiento)
        $pedido_id = $pdo->lastInsertId();

        if (!$pedido_id) {
            $pdo->rollBack();
            return false;
        }

        // Ahora insertamos cada línea del carrito en detalle_pedido
        //
        // Tu tabla detalle_pedido tiene:
        // id_detalle (AI), id_pedido, id_producto,
        // cantidad, precio_unitario, subtotal
        //
        // OJO: en tu tabla NO existe 'nombre_producto'
        //
        $stmt_detalle = $pdo->prepare("
            INSERT INTO detalle_pedido (
                id_pedido,
                id_producto,
                cantidad,
                precio_unitario,
                subtotal
            ) VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($detalles_carrito as $producto_id => $item) {
            $cantidad = (int)$item['quantity'];
            $precio_unitario = $item['price'];
            $subtotal = $precio_unitario * $cantidad;

            $stmt_detalle->execute([
                $pedido_id,
                $producto_id,
                $cantidad,
                $precio_unitario,
                $subtotal
            ]);
        }

        // Si todo salió bien, confirmamos la transacción
        $pdo->commit();

        // devolvemos el número de pedido para el seguimiento
        return (int)$pedido_id;

    } catch (PDOException $e) {
        $pdo->rollBack();

        // Para debug mientras probás:
        // error_log("Error al guardar pedido: " . $e->getMessage());

        return false;
    }
}


// seccion admin
/**
 * Obtiene el resumen de ventas (importe total) para un mes y año específicos.
 * @param string $year_month Formato 'YYYY-MM' (ej: '2025-06')
 * @return array Resumen de ventas (Total)
 */
function obtener_resumen_ventas_mensual($year_month) {
    global $pdo;
    
    // Calcula el primer y último día del mes
    $start_date = $year_month . '-01 00:00:00';
    $end_date = date('Y-m-t 23:59:59', strtotime($year_month)); // 'Y-m-t' da el último día del mes

    try {
        $sql = "SELECT 
                        SUM(total) AS total_venta
                    FROM pedidos
                    WHERE fecha_pedido BETWEEN ? AND ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$start_date, $end_date]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Formato para el reporte
        $month_name = date('F Y', strtotime($year_month));
        
        return [
            'mes' => $month_name,
            'total_venta' => (float)$resultado['total_venta'] ?? 0.00,
            // Valores de costo/ganancia quedan en 0 ya que no tenemos costos en la BD
            'costo_total' => 0.00,
            'ganancia_neta' => (float)$resultado['total_venta'] ?? 0.00, 
        ];
        
    } catch (PDOException $e) {
        error_log("Error al obtener resumen de ventas: " . $e->getMessage());
        return ['mes' => 'Error', 'total_venta' => 0.00, 'costo_total' => 0.00, 'ganancia_neta' => 0.00];
    }
}

/**
 * Obtiene el total de ventas (importe total) agrupado por día para un mes y año específicos.
 * @param string $year_month Formato 'YYYY-MM' (ej: '2025-06')
 * @return array Lista de ventas por día
 */
function obtener_ventas_diarias($year_month) {
    global $pdo;
    
    $start_date = $year_month . '-01 00:00:00';
    $end_date = date('Y-m-t 23:59:59', strtotime($year_month));

    try {
        $sql = "SELECT 
                        DATE(fecha_pedido) AS fecha,
                        SUM(total) AS importe_total
                    FROM pedidos
                    WHERE fecha_pedido BETWEEN ? AND ?
                    GROUP BY DATE(fecha_pedido)
                    ORDER BY fecha DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$start_date, $end_date]);
        
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Añadir una ganancia simulada para mantener la estructura de la tabla (Ganancia = Venta Total)
        $ventas_con_ganancia = array_map(function($venta) {
            $venta['ganancia'] = (float)$venta['importe_total']; 
            return $venta;
        }, $resultados);
        
        return $ventas_con_ganancia;
        
    } catch (PDOException $e) {
        error_log("Error al obtener ventas diarias: " . $e->getMessage());
        return [];
    }
}