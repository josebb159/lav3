<?php
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Filtro con sesión de negocio
$where = "";

$sql = "SELECT * FROM motivo WHERE $where descripcion LIKE '%$search%' LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$sql_count = "SELECT COUNT(*) as total FROM motivo WHERE $where descripcion LIKE '%$search%'";
$count_result = $conn->query($sql_count);
$total_motivo = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_motivo / $limit);


?>

<h1>Listado de Motivos de Cancelación</h1>

<form action="home.php?m=motivo" method="GET" class="mb-3">
    <input type="hidden" name="m" value="motivo">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Buscar por descripción" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </div>
</form>

<a href="crear_motivo.php" class="btn btn-primary mb-3">Crear Nuevo motivo</a>

<div class="card shadow mb-4">
    <div class="card-header">
        <h5>Lista de motivos</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['descripcion']); ?></td>
                    <td>
                        <?php
                        $status = strtolower($row['estado']);
                        echo $status == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarMotivo(<?= $row['id']; ?>)">Editar</button>
                        <?php if ($row['estado'] == 1) { ?>
                            <button class="btn btn-danger btn-sm" onclick="cambiarStatusMotivo(<?= $row['id']; ?>, 2)">Bloquear</button>
                        <?php } else { ?>
                            <button class="btn btn-success btn-sm" onclick="cambiarStatusMotivo(<?= $row['id']; ?>, 1)">Activar</button>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?m=motivo&page=<?= $page - 1; ?>&search=<?= htmlspecialchars($search); ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?m=motivo&page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"><?= $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?m=motivo&page=<?= $page + 1; ?>&search=<?= htmlspecialchars($search); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarMotivo" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEditarMotivo">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Motivo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editar_motivo_id">
          <div class="mb-3">
            <label>Descripción</label>
            <input type="text" name="descripcion" id="editar_descripcion" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCrearMotivo" tabindex="-1">
  <div class="modal-dialog">
    <form id="formCrearMotivo">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo motivo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
   
          <div class="mb-3">
              <label>Descripción</label>
              <input type="text" name="descripcion" id="crear_descripcion" class="form-control" required>
          </div>
      
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Crear</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function cambiarStatusMotivo(id, nuevoStatus) {
    showLoading();
    $.post('../controllers/Motivo_controller.php', {
        action: 'cambiar_status_motivo',
        id: id,
        status: nuevoStatus
    }, function() {
        window.location.reload();
    });
}

function editarMotivo(id) {
    showLoading('Cargando motivo...');
    $.get('../controllers/motivo_controller.php', { action: 'obtener_motivo', id: id }, function(data) {
        Swal.close();
        const motivo = JSON.parse(data);
        $('#editar_motivo_id').val(motivo.id);
        $('#editar_descripcion').val(motivo.descripcion);
        $('#modalEditarMotivo').modal('show');
    });
}

$('#formEditarMotivo').submit(function(e) {
    e.preventDefault();
    const desc = $('#editar_descripcion').val();
    if (!validateNotEmpty(desc)) { showErrorAlert('Descripción obligatoria'); return; }

    showLoading();
    $.post('../controllers/motivo_controller.php', $(this).serialize() + '&action=editar_motivo', function() {
        Swal.fire({
            icon: 'success',
            title: 'Motivo actualizado',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#modalEditarMotivo').modal('hide');
            location.reload();
        });
    });
});

$('a[href="crear_motivo.php"]').click(function(e) {
    e.preventDefault();
    $('#modalCrearMotivo').modal('show');
});

$('#formCrearMotivo').submit(function(e) {
    e.preventDefault();
    const desc = $('#crear_descripcion').val();
    if (!validateNotEmpty(desc)) { showErrorAlert('Descripción obligatoria'); return; }

    showLoading();
    $.post('../controllers/motivo_controller.php', $(this).serialize() + '&action=crear_motivo', function() {
       Swal.fire({
            icon: 'success',
            title: 'Motivo creado',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#modalCrearMotivo').modal('hide');
            location.reload();
        });
    });
});
</script>
