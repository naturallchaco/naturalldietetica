<?php
// Incluimos funciones y conexión
require_once 'includes/functions.php'; 

// -----------------------------
// MANEJO DE FORMULARIOS
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        // LOGIN
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (login($email, $password)) {
            $_SESSION['mensaje_auth'] = "Inicio de sesión exitoso. ¡Bienvenido/a!";

            // Redirección según el rol
            if ($_SESSION['id_rol'] == 1) {
                header('Location:index.php'); // Página de clientes
                exit;
            } elseif ($_SESSION['id_rol'] == 2) {
                header('Location:gestion/gestion_stock.php'); // Panel de administradores
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            $_SESSION['mensaje_auth'] = "Correo o contraseña incorrectos.";
            header('Location: registro.php');
            exit;
        }

    } elseif ($action === 'register') {
        // REGISTRO (no se toca)
        $id_rol = trim($_POST['id_rol']);
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);
        $direccion_principal = trim($_POST['direccion_principal']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if ($password !== $confirm_password) {
            $_SESSION['mensaje_auth'] = "Las contraseñas no coinciden.";
            header('Location: registro.php');
            exit;
        }

        if (email_existe($email)) {
            $_SESSION['mensaje_auth'] = "Ya existe una cuenta con ese correo.";
            header('Location: registro.php');
            exit;
        }

        if (registrar_usuario($id_rol, $nombre, $apellido, $email, $telefono, $direccion_principal, $password)) {
            $_SESSION['mensaje_auth'] = "Cuenta creada correctamente. Ahora podés iniciar sesión.";
            header('Location: registro.php');
            exit;
        } else {
            $_SESSION['mensaje_auth'] = "Error al crear la cuenta. Intentalo nuevamente.";
            header('Location: registro.php');
            exit;
        }
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
// HTML (Formulario de Login / Registro)
// -----------------------------
require_once 'includes/header.php'; 
?>

<main>
    <?php if ($mensaje): ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <section class="auth-container">
        <h2>Acceso a NaturAll 🌱</h2>

        <div class="form-toggle">
            <button class="active" id="show-login">Iniciar Sesión</button>
            <button id="show-register">Registrarse</button>
        </div>

        <!-- FORMULARIO LOGIN -->
        <form id="login-form" class="auth-form" action="registro.php" method="post">
            <h3>Iniciar Sesión</h3>
            <input type="hidden" name="action" value="login"> 

            <label for="login-email">Correo Electrónico</label>
            <input type="email" id="login-email" name="email" placeholder="ejemplo@correo.com" required> 

            <label for="login-password">Contraseña</label>
            <input type="password" id="login-password" name="password" placeholder="••••••••" required>

            <button type="submit">Entrar</button>
        </form>

        <!-- FORMULARIO REGISTRO -->
        <form id="register-form" class="auth-form" action="registro.php" method="post">
            <h3>Crear una Cuenta</h3>
            <input type="hidden" name="action" value="register"> 
            <input type="hidden" name="id_rol" value="1">
            
            <label for="register-nombre">Nombre</label>
            <input type="text" id="register-nombre" name="nombre" placeholder="Tu Nombre" required>

            <label for="register-apellido">Apellido</label>
            <input type="text" id="register-apellido" name="apellido" placeholder="Tu Apellido" required>
            
            <label for="register-email">Correo Electrónico</label>
            <input type="email" id="register-email" name="email" placeholder="ejemplo@correo.com" required>

            <label for="register-telefono">Telefono</label>
            <input type="text" id="register-telefono" name="telefono" placeholder="Tu Numero de Telefono" required>

            <label for="register-direccion">Direccion</label>
            <input type="text" id="register-direccion" name="direccion_principal" placeholder="Tu Direccion" required>

            <label for="register-password">Contraseña</label>
            <input type="password" id="register-password" name="password" placeholder="Crea una contraseña segura" required>

            <label for="register-confirm-password">Confirmar Contraseña</label>
            <input type="password" id="register-confirm-password" name="confirm_password" placeholder="Repite tu contraseña" required>

            <button type="submit">Registrarme</button>
        </form>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const showLoginBtn = document.getElementById('show-login');
    const showRegisterBtn = document.getElementById('show-register');

    loginForm.style.display = 'flex';
    registerForm.style.display = 'none';
    showLoginBtn.classList.add('active');

    showLoginBtn.addEventListener('click', () => {
        loginForm.style.display = 'flex';
        registerForm.style.display = 'none';
        showLoginBtn.classList.add('active');
        showRegisterBtn.classList.remove('active');
    });

    showRegisterBtn.addEventListener('click', () => {
        registerForm.style.display = 'flex';
        loginForm.style.display = 'none';
        showRegisterBtn.classList.add('active');
        showLoginBtn.classList.remove('active');
    });
});
</script>
