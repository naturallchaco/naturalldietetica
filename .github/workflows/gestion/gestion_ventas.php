<?php
require_once '..\includes\functions.php'; 
// Inicia la sesión para verificar los permisos
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] != 2) {
    header("Location: ../registro.php");
    exit;
}

// Define el título de la página
$page_title = "Gestión de Ventas y Reportes";

// ----------------------------------------------------
// 1. LÓGICA DE FILTRO Y OBTENCIÓN DE DATOS REALES
// ----------------------------------------------------

// 1.1 Determinar el mes a consultar: usa el GET o el mes actual por defecto
$mes_actual = date('Y-m'); 
$mes_a_consultar = $_GET['mes'] ?? $mes_actual;

// 1.2 Obtener los datos reales de la base de datos
$resumen_mes = obtener_resumen_ventas_mensual($mes_a_consultar);
$ventas_por_dia = obtener_ventas_diarias($mes_a_consultar);

// 1.3 Prepara el listado de meses para el <select>
// Esto asume que tienes datos del mes actual y el mes anterior
$meses_disponibles = [
    $mes_actual => date('F Y', strtotime($mes_actual)),
    date('Y-m', strtotime('-1 month')) => date('F Y', strtotime('-1 month')),
];

// 2. Incluir la cabecera (incluye la navegación administrativa)
require_once '../includes/headeradmin.php'; 
?>

<main class="container">
    <h2>Registro y Análisis de Ventas</h2>

    <form action="gestion_ventas.php" method="GET" class="filtro-ventas">
        <label for="mes_filtro">Seleccionar Mes:</label>
        <select id="mes_filtro" name="mes">
            <?php foreach ($meses_disponibles as $valor => $etiqueta): ?>
                <option value="<?php echo $valor; ?>" <?php echo ($valor == $mes_a_consultar) ? 'selected' : ''; ?>>
                    <?php echo $etiqueta; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-principal">Ver Reporte</button>
    </form>

    <hr>

    <section id="resumen_mes">
        <h3>Resumen de Ventas (<?php echo htmlspecialchars($resumen_mes['mes']); ?>)</h3>
        
        <div class="resumen-cards">
            <div class="card-resumen total">
                <p>Importe total de venta:</p>
                <h4>$<?php echo number_format($resumen_mes['total_venta'], 2, ',', '.'); ?></h4>
            </div>
            
            <div class="card-resumen costo">
                <p>Costo total (Simulado):</p>
                <h4>$<?php echo number_format($resumen_mes['costo_total'], 2, ',', '.'); ?></h4>
            </div>
            
            <div class="card-resumen ganancia">
                <p>Ganancia neta (Simulada):</p>
                <h4>$<?php echo number_format($resumen_mes['ganancia_neta'], 2, ',', '.'); ?></h4>
            </div>
        </div>
    </section>

    <hr>

    <section id="ventas_dia">
        <h3>Ventas por Día</h3>
        
        <?php if (!empty($ventas_por_dia)): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Importe Total</th>
                    <th>Ganancia (Sim.)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas_por_dia as $venta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($venta['fecha']))); ?></td>
                        <td>$<?php echo number_format($venta['importe_total'], 2, ',', '.'); ?></td>
                        <td style="color: green;">$<?php echo number_format($venta['ganancia'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="gestion_pedidos.php?fecha=<?php echo htmlspecialchars($venta['fecha']); ?>" class="btn-pequeno">Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alerta info">No se encontraron pedidos para el mes de <?php echo htmlspecialchars($resumen_mes['mes']); ?>.</div>
        <?php endif; ?>
    </section>
</main>

<?php
// 4. Incluir el pie de página
require_once '../includes/footer.php'; 
?>