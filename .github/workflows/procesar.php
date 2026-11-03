<?php
session_start();

// Función de limpieza de datos para evitar inyecciones (seguridad básica)
function limpiar_datos($datos) {
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $datos;
}

// Procesamiento de formularios

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['username']) && isset($_POST['password'])) {
        
        $username = limpiar_datos($_POST['username']);
        $password = $_POST['password'];

     // Conexión
    $conexion = new mysqli("localhost", "root", "1234", "mi_db");
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

        // --- Simulación de Lógica de Inicio de Sesión ---
        
        if (empty($username) || empty($password)) {
            $mensaje = "Error: Ambos campos son obligatorios para iniciar sesión.";
            $tipo = "error";
        } else {
            // SIMULACIÓN DE ÉXITO:
            $mensaje = "¡Bienvenido, " . $username . "! Sesión iniciada correctamente. (Simulación)";
            $tipo = "exito";
        }
        
    } 
    
    // VERIFICAR SI ES UN INTENTO DE REGISTRO
    elseif (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['confirm_password'])) {
        
        $name = limpiar_datos($_POST['name']);
        $email = limpiar_datos($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // --- Simulación de Lógica de Registro ---

        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $mensaje = "Error: Todos los campos del formulario de registro son obligatorios.";
            $tipo = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "Error: El formato del correo electrónico no es válido.";
            $tipo = "error";
        } elseif ($password !== $confirm_password) {
            $mensaje = "Error: Las contraseñas no coinciden.";
            $tipo = "error";
        } elseif (strlen($password) < 6) {
            $mensaje = "Error: La contraseña debe tener al menos 6 caracteres.";
            $tipo = "error";
        } else {
            // SIMULACIÓN DE ÉXITO:
            $mensaje = "¡Registro exitoso para " . $name . "! Ya puedes iniciar sesión. (Simulación)";
            $tipo = "exito";
        }

    } else {
        $mensaje = "Error: No se pudo identificar el formulario enviado.";
        $tipo = "error";
    }

    // Guardar el mensaje en sesión
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;

    // Redireccionar al usuario a la página de inicio o donde sea apropiado
    header("Location: index.html");
    exit();

} else {
    // Si alguien intenta acceder directamente al script sin enviar datos
    header("Location: index.html");
    exit();
}

?>