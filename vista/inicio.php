<?php
// vista/inicio.php

// 1. Definicón de variables base
$negocio_id = isset($_SESSION['negocio']) ? (int)$_SESSION['negocio'] : 0;
$is_admin = (!$negocio_id);

// Inicializar contadores
$total_alquileres = 0;
$total_ingresos = 0;
$alquileres_activos = 0;
$usuarios_registrados = 0; // Solo admin
$negocios_registrados = 0; // Solo admin
$lavadoras_disponibles = 0;
$total_lavadoras = 0;

// Array para gráfica mensual (Enero-Diciembre)
$chart_revenue = array_fill(1, 12, 0);
$chart_rentals = array_fill(1, 12, 0);
$months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

// 2. Consultas según Rol
if ($is_admin) {
    // --- ADMIN QUERY ---
    
    // KPIs Generales - Solo ingresos de servicios completados (status_servicio = 4)
    $kpi1 = $conn->query("SELECT 
        COUNT(*) as total_rentals, 
        (SELECT SUM(tiempo_alquiler * valor_servicio) FROM alquileres WHERE status_servicio = 4) as total_revenue,
        (SELECT COUNT(*) FROM alquileres WHERE status = 'activo') as active_rentals
        FROM alquileres");
    if($r = $kpi1->fetch_assoc()){
        $total_alquileres = $r['total_rentals'];
        $total_ingresos = $r['total_revenue'] ?? 0;
        $alquileres_activos = $r['active_rentals'];
    }

    // KPIs Usuarios/Negocios
    $usersq = $conn->query("SELECT COUNT(*) as c FROM usuarios");
    $usuarios_registrados = $usersq->fetch_assoc()['c'];
    
    $negsq = $conn->query("SELECT COUNT(*) as c FROM negocios");
    $negocios_registrados = $negsq->fetch_assoc()['c'];

    // Gráfica de Ingresos Mensuales - Solo servicios completados
    $revenue_q = $conn->query("SELECT MONTH(fecha_inicio) as mes, SUM(tiempo_alquiler * valor_servicio) as total FROM alquileres WHERE status_servicio = 4 GROUP BY mes");
    while($row = $revenue_q->fetch_assoc()){
        $chart_revenue[(int)$row['mes']] = (float)$row['total'];
    }

} else {
    // --- NEGOCIO QUERY ---

    // KPIs Generales Negocio - Solo ingresos de servicios completados
    $kpi1 = $conn->query("SELECT 
        COUNT(*) as total_rentals, 
        (SELECT SUM(tiempo_alquiler * valor_servicio) FROM alquileres WHERE negocio_id = $negocio_id AND status_servicio = 4) as total_revenue,
        (SELECT COUNT(*) FROM alquileres WHERE negocio_id = $negocio_id AND status = 'activo') as active_rentals
        FROM alquileres WHERE negocio_id = $negocio_id");
    
    if($r = $kpi1->fetch_assoc()){
        $total_alquileres = $r['total_rentals'];
        $total_ingresos = $r['total_revenue'] ?? 0;
        $alquileres_activos = $r['active_rentals'];
    }

    // Lavadoras
    $lavq = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'disponible' THEN 1 ELSE 0 END) as disponibles
        FROM lavadoras WHERE negocio_id = $negocio_id");
    if($l = $lavq->fetch_assoc()){
        $total_lavadoras = $l['total'];
        $lavadoras_disponibles = $l['disponibles'];
    }

    // Gráfica de Ingresos Mensuales Negocio - Solo servicios completados
    $revenue_q = $conn->query("SELECT MONTH(fecha_inicio) as mes, SUM(tiempo_alquiler * valor_servicio) as total 
                               FROM alquileres WHERE negocio_id = $negocio_id AND status_servicio = 4 GROUP BY mes");
    while($row = $revenue_q->fetch_assoc()){
        $chart_revenue[(int)$row['mes']] = (float)$row['total'];
    }
}

// 3. Tabla de Actividad Reciente (Común pero filtrada)
$limit_recent = 5;
$where_recent = $is_admin ? "1=1" : "a.negocio_id = $negocio_id";
$recent_q = "SELECT a.*, u.nombre as usuario, l.codigo as lavadora,
             (SELECT puntuacion FROM servicio_calificaciones WHERE alquiler_id = a.id LIMIT 1) as puntuacion 
             FROM alquileres a
             JOIN usuarios u ON a.user_id = u.id
             JOIN lavadoras l ON a.lavadora_id = l.id
             WHERE $where_recent
             ORDER BY a.id DESC LIMIT $limit_recent";
$recent_res = $conn->query($recent_q);

?>

<!-- ESTILOS DASHBOARD -->
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        color: white;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .stat-card .card-body {
        padding: 25px;
        position: relative;
        z-index: 1;
    }
    .stat-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3.5rem;
        opacity: 0.3;
        z-index: 0;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
    }
    
    /* Gradientes */
    .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
    .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .bg-gradient-info    { background: linear-gradient(45deg, #36b9cc, #258391); }
    .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    .bg-gradient-danger  { background: linear-gradient(45deg, #e74a3b, #be2617); }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }
    .table-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .badge-status-activo { background-color: #1cc88a; color: white; }
    .badge-status-finalizado { background-color: #858796; color: white; }
</style>

<div class="row mb-4 animate__animated animate__fadeIn">
    <div class="col-12">
        <h2 class="text-gray-800 border-bottom pb-2">Panel de Control <small class="text-muted text-sm"><?= $is_admin ? '(Administrador)' : '(Negocio)' ?></small></h2>
    </div>
</div>

<!-- KPI CARDS -->
<div class="row g-4 mb-5 animate__animated animate__fadeInUp">
    
    <!-- Ingresos Totales -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-gradient-success">
            <div class="card-body">
                <div class="stat-value">$ <?= number_format($total_ingresos, 0, ',', '.') ?></div>
                <div class="stat-label">Ingresos Totales</div>
                <i class="fas fa-dollar-sign stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Alquileres Activos -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-gradient-warning">
            <div class="card-body">
                <div class="stat-value"><?= $alquileres_activos ?></div>
                <div class="stat-label">Alquileres En Curso</div>
                <i class="fas fa-stopwatch stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Alquileres Totales -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-gradient-primary">
            <div class="card-body">
                <div class="stat-value"><?= $total_alquileres ?></div>
                <div class="stat-label">Histórico Alquileres</div>
                <i class="fas fa-history stat-icon"></i>
            </div>
        </div>
    </div>

    <?php if($is_admin): ?>
        <!-- Usuarios (Solo Admin) -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-gradient-info">
                <div class="card-body">
                    <div class="stat-value"><?= $usuarios_registrados ?></div>
                    <div class="stat-label">Usuarios App</div>
                    <i class="fas fa-users stat-icon"></i>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Lavadoras (Solo Negocio) -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-gradient-info">
                <div class="card-body">
                    <div class="stat-value"><?= $lavadoras_disponibles ?> / <?= $total_lavadoras ?></div>
                    <div class="stat-label">Lavadoras Disponibles</div>
                    <i class="fas fa-plug stat-icon"></i>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- CHARTS & TABLES -->
<div class="row">
    <!-- Chart: Ingresos -->
    <div class="col-lg-8 mb-4">
        <div class="chart-container h-100">
            <h5 class="text-primary mb-3"><i class="fas fa-chart-line me-2"></i>Ingresos Mensuales</h5>
            <div id="revenueChart" style="height: 350px;"></div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-4 mb-4">
        <div class="table-container h-100">
            <h5 class="text-primary mb-3"><i class="fas fa-list me-2"></i>Últimos Alquileres</h5>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Calificación</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($recent_res && $recent_res->num_rows > 0): ?>
                            <?php while($row = $recent_res->fetch_assoc()): 
                                // Determinar badge según status_servicio
                                switch($row['status_servicio']) {
                                    case 1: $status_badge = 'bg-warning text-dark'; $status_text = 'En espera'; break;
                                    case 6: $status_badge = 'bg-info'; $status_text = 'En camino'; break;
                                    case 2: $status_badge = 'bg-primary'; $status_text = 'En proceso'; break;
                                    case 3: $status_badge = 'bg-success'; $status_text = 'Finalizado'; break;
                                    case 4: $status_badge = 'bg-secondary'; $status_text = 'Completado'; break;
                                    case 5: $status_badge = 'bg-danger'; $status_text = 'Cancelado'; break;
                                    case 7: $status_badge = 'bg-dark'; $status_text = 'Cancelado auto'; break;
                                    default: $status_badge = 'bg-secondary'; $status_text = 'Desconocido'; break;
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['usuario']) ?></div>
                                        <small class="text-muted"><?= date('d M H:i', strtotime($row['fecha_inicio'])) ?></small>
                                    </td>
                                    <td><span class="badge <?= $status_badge ?> rounded-pill"><?= $status_text ?></span></td>
                                    <td class="text-warning">
                                        <?php if(isset($row['puntuacion']) && $row['puntuacion'] > 0): ?>
                                            <?= str_repeat('★', $row['puntuacion']) ?>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-success fw-bold">$<?= number_format($row['tiempo_alquiler'] * $row['valor_servicio'],0,',','.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted">Sin actividad reciente</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-3">
                <a href="home.php?m=a" class="btn btn-sm btn-outline-primary rounded-pill">Ver todos</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Highcharts.chart('revenueChart', {
        chart: { type: 'areaspline' },
        title: { text: null },
        xAxis: { 
            categories: <?= json_encode($months) ?>,
            crosshair: true
        },
        yAxis: { 
            title: { text: 'Ingresos ($)' },
            labels: { format: '${value}' }
        },
        tooltip: {
            shared: true,
            valuePrefix: '$'
        },
        credits: { enabled: false },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.5
            }
        },
        series: [{
            name: 'Ingresos',
            data: <?= json_encode(array_values($chart_revenue)) ?>,
            color: '#1cc88a'
        }]
    });
});
</script>