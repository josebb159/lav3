<?php
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Consulta con búsqueda en referencia, estado o método de pago (puedes ajustar columnas)
$sql = "
  SELECT * FROM pagos_pay
  WHERE reference_code LIKE '%$search%'
  OR estado LIKE '%$search%'
  OR metodo_pago LIKE '%$search%'
  LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

// Contar total para paginación
$sql_count = "
  SELECT COUNT(*) as total FROM pagos_pay
  WHERE reference_code LIKE '%$search%'
  OR estado LIKE '%$search%'
  OR metodo_pago LIKE '%$search%'
";
$count_result = $conn->query($sql_count);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<h1>Listado de Pagos</h1>
<form method="GET" class="mb-3">
  <input type="hidden" name="m" value="pau"> <!-- ajusta si usas -->
  <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Buscar referencia, estado o método" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" class="btn btn-primary">Buscar</button>
  </div>
</form>

<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>User ID</th>
      <th>Referencia</th>
      <th>Monto</th>
      <th>Moneda</th>
      <th>Estado</th>
      <th>ID Transacción</th>
      <th>Método Pago</th>
      <th>Fecha Pago</th>
      <th>Fecha Actualización</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo htmlspecialchars($row['reference_code']); ?></td>
        <td><?php echo number_format($row['amount'], 2); ?></td>
        <td><?php echo htmlspecialchars($row['currency']); ?></td>
        <td>
          <?php
            $estado = $row['estado'];
            $badge_class = 'bg-secondary';
            if (strtoupper($estado) == 'APROBADO') $badge_class = 'bg-success';
            else if (strtoupper($estado) == 'PENDIENTE') $badge_class = 'bg-warning';
            else if (strtoupper($estado) == 'RECHAZADO') $badge_class = 'bg-danger';
            echo "<span class='badge $badge_class'>" . htmlspecialchars($estado) . "</span>";
          ?>
        </td>
        <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
        <td><?php echo htmlspecialchars($row['metodo_pago']); ?></td>
        <td><?php echo $row['fecha_pago']; ?></td>
        <td><?php echo $row['fecha_actualizacion']; ?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<nav>
  <ul class="pagination">
    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
      <a class="page-link" href="?m=pagos&page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Anterior</a>
    </li>

    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
      <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
        <a class="page-link" href="?m=pagos&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
      </li>
    <?php } ?>

    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
      <a class="page-link" href="?m=pagos&page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Siguiente</a>
    </li>
  </ul>
</nav>
