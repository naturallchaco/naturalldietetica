<?php
// Inicia la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ⚠️ Asume que BASE_URL está definida en otro archivo (ej: functions.php o config.php)
// Si BASE_URL no está definida, descomenta y define aquí la ruta a tu carpeta 'naturall/'
// define('BASE_URL', '/naturall/'); 


// Define la URL donde se encuentra tu página de inicio de sesión.
define('LOGIN_PAGE', BASE_URL . 'registro.php'); 

// Lista de páginas a las que se puede acceder SIN iniciar sesión.
$paginas_permitidas = [
    '/login.php',     
    '/registro.php', 
    '/includes/auth_check.php', 
    '/includes/header.php',
];

$pagina_actual = '/' . basename($_SERVER['PHP_SELF']);

// Redirige si NO está logueado y NO está en una página permitida
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    
    if (!in_array($pagina_actual, $paginas_permitidas)) {
        
        header('Location: ' . LOGIN_PAGE);
        exit(); 
    }
}