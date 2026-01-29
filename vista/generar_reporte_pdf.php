<?php
require_once '../vendor/autoload.php';
require_once '../modelo/db.php';
$conn = conect();

$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];
$negocio_id = $_GET['negocio_id'];

$where = "WHERE a.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
if (!empty($negocio_id)) {
    $where .= " AND a.negocio_id = $negocio_id";
}

$query = "
SELECT a.*, u.nombre AS usuario, l.codigo AS lavadora, n.nombre AS negocio
FROM alquileres a
JOIN usuarios u ON a.user_id = u.id
JOIN lavadoras l ON a.lavadora_id = l.id
JOIN negocios n ON a.negocio_id = n.id
$where
ORDER BY a.fecha_inicio DESC
";
$result = $conn->query($query);

// Iniciar PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema Lavadoras');
$pdf->SetTitle('Reporte de Alquileres');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Encabezado HTML
$html = '
<style>
    h2 {
        color: #003366;
        text-align: center;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th {
        background-color: #003366;
        color: white;
        text-align: center;
        font-weight: bold;
    }
    td {
        border: 1px solid #888;
        text-align: center;
    }
    .resumen {
        margin-bottom: 10px;
    }
    .total {
        font-weight: bold;
        background-color: #f0f0f0;
    }
</style>
<h2>Reporte de Alquileres</h2>
<div class="resumen"><strong>Desde:</strong> ' . $fecha_inicio . ' &nbsp;&nbsp; <strong>Hasta:</strong> ' . $fecha_fin . '</div>';

if ($negocio_id) {
    $neg = $conn->query("SELECT nombre FROM negocios WHERE id = $negocio_id")->fetch_assoc();
    $html .= '<div class="resumen"><strong>Negocio:</strong> ' . $neg['nombre'] . '</div>';
}

$html .= '
<table cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Lavadora</th>
            <th>Negocio</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>';

// Inicializar total
$total = 0;

while ($row = $result->fetch_assoc()) {
    
    $tt = $row['valor_servicio'] * $row['tiempo_alquiler'];
    $total += $tt;
    
    $html .= "<tr>
        <td>{$row['id']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['lavadora']}</td>
        <td>{$row['negocio']}</td>
        <td>{$row['fecha_inicio']}</td>
        <td>{$row['fecha_fin']}</td>
        <td>$ $tt</td>
    </tr>";
}

// Agregar total
$html .= '<tr class="total">
    <td colspan="6" align="right"><strong>Total:</strong></td>
    <td><strong>$ ' . number_format($total, 2) . '</strong></td>
</tr>';

$html .= '</tbody></table>';

// Salida del PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("reporte_alquileres.pdf", "I");
