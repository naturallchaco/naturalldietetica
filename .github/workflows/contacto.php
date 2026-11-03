<?php

require_once 'includes/functions.php'; 

// 1. Establecer el título de la página
$page_title = "NaturAll - Contacto";

// 2. INICIALIZACIÓN DE LA VARIABLE DE ESTADO (CORRECCIÓN DEL WARNING)
$mensaje_status = ''; 

// 3. LÓGICA DE PROCESAMIENTO DEL FORMULARIO
// Esta lógica se ejecuta antes de cualquier salida HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3.1. Sanitización y Captura de datos
    $nombre = htmlspecialchars(trim($_POST['nombre_contacto']));
    $email = filter_var($_POST['email_contacto'], FILTER_SANITIZE_EMAIL);
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));

    // 3.2. Validación simple
    if (empty($nombre) || empty($email) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje_status = '<div class="alerta error">Por favor, complete todos los campos correctamente.</div>';
    } else {
        // 3.3. Lógica de Envío de Email (Simulada)
        $destinatario = "contacto@naturall.com";
        $asunto = "Nuevo mensaje de contacto de: " . $nombre;
        $contenido = "Nombre: " . $nombre . "\n";
        $contenido .= "Email: " . $email . "\n";
        $contenido .= "Mensaje:\n" . $mensaje;

        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Simulación de la función mail(). En un servidor real, esto intenta enviar el correo.
        if (mail($destinatario, $asunto, $contenido, $headers)) {
            $mensaje_status = '<div class="alerta exito">✅ ¡Gracias! Tu mensaje ha sido enviado correctamente. Te responderemos pronto.</div>';
            // Opcional: limpiar los campos si fue exitoso
            $_POST = array(); 
        } else {
            $mensaje_status = '<div class="alerta error">❌ Lo sentimos, hubo un error al enviar tu mensaje. Intenta de nuevo o contáctanos por teléfono.</div>';
        }
    }
}

// 4. Incluir la cabecera (inicia el HTML y la navegación de la tienda)
require_once 'includes/header.php'; 
?>

<main class="container contacto-page">
    <section class="page-header">
        <h1>Estamos listos para ayudarte</h1>
        <p class="subtitle">
            ¿Tienes alguna pregunta, pedido especial o simplemente quieres saludarnos? Contáctanos por el medio que prefieras.
        </p>
    </section>

    <!-- Muestra el mensaje de estado (Ahora la variable existe siempre) -->
    <?php echo $mensaje_status; ?>

    <div class="contacto-grid">
        
        <!-- COLUMNA IZQUIERDA: INFORMACIÓN Y CANALES -->
        <div class="contacto-info card-shadow fade-in-left">
            <h3>Nuestros Canales de Atención</h3>
            
            <div class="info-item">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <h4>Llámanos</h4>
                    <p>+54 362 415 8704</p>
                    <a href="tel:+543624158704" class="btn-secundario btn-sm">Llamar Ahora</a>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <h4>Escríbenos</h4>
                    <p>contacto@naturall.com</p>
                    <a href="mailto:contacto@naturall.com" class="btn-secundario btn-sm">Enviar Email</a>
                </div>
            </div>

            <div class="info-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <h4>Visítanos</h4>
                    <p>Liniers 382, Resistencia, Chaco</p>
                </div>
            </div>
            
        </div>

        <!-- COLUMNA DERECHA: FORMULARIO -->
        <div class="contacto-form card-shadow fade-in-right">
            <h3>Envíanos un Mensaje</h3>
            <p class="form-description">Utiliza el formulario y te responderemos en un plazo máximo de 24 horas hábiles.</p>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                
                <label for="nombre_contacto">Nombre Completo</label>
                <input type="text" id="nombre_contacto" name="nombre_contacto" required 
                    value="<?php echo isset($_POST['nombre_contacto']) ? htmlspecialchars($_POST['nombre_contacto']) : ''; ?>">
                
                <label for="email_contacto">Correo Electrónico</label>
                <input type="email" id="email_contacto" name="email_contacto" required 
                    value="<?php echo isset($_POST['email_contacto']) ? htmlspecialchars($_POST['email_contacto']) : ''; ?>">
                
                <label for="mensaje">Mensaje / Consulta</label>
                <textarea id="mensaje" name="mensaje" rows="6" required><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                
                <button type="submit" class="btn-principal btn-block">
                    Enviar Mensaje <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- MAPA DE UBICACIÓN -->
    <section class="ubicacion-mapa">
        <h3>Encuéntranos en el Mapa</h3>
        <p>Estamos aquí para recibirte.</p>
        <div class="map-embed">
            <!-- URL de Google Maps incrustado -->
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2231.10851057014!2d-58.986450622499625!3d-27.446498932126207!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94450c5fa7507beb%3A0x13898aebace676a!2sLiniers%20382%2C%20H3508%20Resistencia%2C%20Chaco!5e1!3m2!1ses-419!2sar!4v1760931687954!5m2!1ses-419!2sar" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>        </div>
    </section>
</main>

<?php
// 5. Incluir el pie de página (cierra el HTML)
require_once 'includes/footer.php'; 
?>
