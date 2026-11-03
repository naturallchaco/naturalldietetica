<?php
// Incluimos funciones y conexión
require_once '../includes/functions.php'; 
session_start();

if ($_SESSION['id_usuario'] != 2) {
    header("Location: ../registro.php");
    exit;
}

// -----------------------------
// MANEJO DE FORMULARIO DE REGISTRO
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_rol = trim($_POST['id_rol']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion_principal = trim($_POST['direccion_principal']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $horario = trim($_POST['horario']);
    $descanso = trim($_POST['descanso']);
    $vacaciones = trim($_POST['vacaciones']);

    // Validaciones básicas
    if ($password !== $confirm_password) {
        $_SESSION['mensaje_auth'] = "Las contraseñas no coinciden.";
        header('Location: registeradmin.php');
        exit;
    }

    if (email_existe($email)) {
        $_SESSION['mensaje_auth'] = "Ya existe una cuenta con ese correo.";
        header('Location: registeradmin.php');
        exit;
    }

    try {
        // Iniciar transacción para registrar en ambas tablas
        $pdo->beginTransaction();

        // Insertar en tabla usuarios
        $sql_usuario = "INSERT INTO usuarios 
            (id_rol, nombre, apellido, email, telefono, direccion_principal, password) 
            VALUES (:id_rol, :nombre, :apellido, :email, :telefono, :direccion_principal, :password)";
        $stmt = $pdo->prepare($sql_usuario);
        $stmt->execute([
            ':id_rol' => $id_rol,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':email' => $email,
            ':telefono' => $telefono,
            ':direccion_principal' => $direccion_principal,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        // Insertar en tabla empleados (vinculado por email)
        $sql_empleado = "INSERT INTO empleados (email, horario, descanso, vacaciones)
                         VALUES (:email, :horario, :descanso, :vacaciones)";
        $stmt2 = $pdo->prepare($sql_empleado);
        $stmt2->execute([
            ':email' => $email,
            ':horario' => $horario,
            ':descanso' => $descanso,
            ':vacaciones' => $vacaciones
        ]);

        // Confirmar ambas inserciones
        $pdo->commit();

        $_SESSION['mensaje_auth'] = "Empleado registrado correctamente.";
        header('Location: registeradmin.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje_auth'] = "Error al registrar el empleado: " . $e->getMessage();
        header('Location: registeradmin.php');
        exit;
    }
}

// -----------------------------
// MENSAJE TEMPORAL
// -----------------------------
$mensaje = '';
if (isset($_SESSION['mensaje_auth'])) {
    $mensaje = $_SESSION['mensaje_auth'];
    unset($_SESSION['mensaje_auth']);
}

// -----------------------------
// HTML (Formulario)
// -----------------------------
require_once '../includes/headeradmin.php'; 
?>

<main>
    <?php if ($mensaje): ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <section class="auth-container">
        <h2>Crear una Cuenta Admin 🌱</h2>

        <form id="register-form" class="auth-form" action="registeradmin.php" method="post">
            <input type="hidden" name="id_rol" value="2">

            <label for="register-nombre">Nombre</label>
            <input type="text" id="register-nombre" name="nombre" placeholder="Tu Nombre" required>

            <label for="register-apellido">Apellido</label>
            <input type="text" id="register-apellido" name="apellido" placeholder="Tu Apellido" required>
            
            <label for="register-email">Correo Electrónico</label>
            <input type="email" id="register-email" name="email" placeholder="ejemplo@correo.com" required>

            <label for="register-telefono">Teléfono</label>
            <input type="text" id="register-telefono" name="telefono" placeholder="Tu número de teléfono" required>

            <label for="register-direccion">Dirección</label>
            <input type="text" id="register-direccion" name="direccion_principal" placeholder="Tu dirección" required>

            <label for="register-password">Contraseña</label>
            <input type="password" id="register-password" name="password" placeholder="Crea una contraseña segura" required>

            <label for="register-confirm-password">Confirmar Contraseña</label>
            <input type="password" id="register-confirm-password" name="confirm_password" placeholder="Repite tu contraseña" required>

            <hr>

            <h3>Datos Laborales</h3>

            <label for="register-horario">Horario</label>
            <input type="text" id="register-horario" name="horario" placeholder="Ej: Lunes a Viernes, 9:00 - 18:00" required>

            <label for="register-descanso">Descanso</label>
            <input type="text" id="register-descanso" name="descanso" placeholder="Ej: 13:00 - 14:00" required>

            <label for="register-vacaciones">Vacaciones</label>
            <input type="text" id="register-vacaciones" name="vacaciones" placeholder="Ej: 15-22 de Enero (Aprobado)" required>

            <button type="submit">Registrar Empleado</button>
        </form>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
