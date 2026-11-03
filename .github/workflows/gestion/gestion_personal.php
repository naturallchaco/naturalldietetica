<?php
require_once '../includes/functions.php'; 
session_start();

// Verificación de acceso
if ($_SESSION['id_usuario'] != 2) {
    header("Location: ../registro.php");
    exit;
}

// Título de la página
$page_title = "Gestión de Personal y Registro";

// ---------------------------------------------
// CONSULTA PARA OBTENER DATOS DEL PERSONAL
// ---------------------------------------------
try {
    $sql = "
        SELECT 
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.email,
            u.telefono,
            u.direccion_principal,
            r.nombre_rol,
            e.horario,
            e.descanso,
            e.vacaciones
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol
        INNER JOIN empleados e ON u.email = e.email
        ORDER BY u.apellido, u.nombre
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener empleados: " . $e->getMessage());
}

// Incluir cabecera administrativa
require_once '../includes/headeradmin.php'; 
?>

<h2>Miembros del Equipo</h2>

<div class="gestion-acciones">
    <a href="registeradmin.php" class="btn-agregar">
        <i class="fas fa-user-plus"></i> Añadir Nuevo Empleado
    </a>
    <a href="gestion_personal.php?view=vacaciones" class="btn-secundario">
        <i class="fas fa-calendar-alt"></i> Revisar Vacaciones
    </a>
</div>

<?php if (!empty($empleados)): ?>
    <?php foreach ($empleados as $empleado): ?>
        <section class="empleado card-gestion">
            <div class="empleado-info">
                <h3><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?></h3>
                <p><strong>Rol:</strong> <?php echo htmlspecialchars($empleado['nombre_rol']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($empleado['email']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($empleado['telefono']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($empleado['direccion_principal']); ?></p>
                <p><strong>Horario:</strong> <?php echo htmlspecialchars($empleado['horario']); ?></p>
                <p><strong>Descanso:</strong> <?php echo htmlspecialchars($empleado['descanso']); ?></p>
                <p><strong>Vacaciones:</strong> <?php echo htmlspecialchars($empleado['vacaciones']); ?></p>
            </div>

            <div class="empleado-acciones">
                <a href="gestion_personal.php?action=editar&id=<?php echo $empleado['id_usuario']; ?>" class="btn-editar">
                    Editar
                </a>
                <a href="gestion_personal.php?action=eliminar&id=<?php echo $empleado['id_usuario']; ?>" 
                   class="btn-eliminar" 
                   onclick="return confirm('¿Seguro que desea eliminar a <?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?>?');">
                    Eliminar
                </a>
            </div>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <p class="alerta info">No hay empleados registrados en el sistema.</p>
<?php endif; ?>

<?php
// Incluir pie de página
require_once '../includes/footer.php'; 
?>
