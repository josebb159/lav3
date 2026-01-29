<?php
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Filtro con sesión de negocio
$where = "";
if (isset($_SESSION['negocio']) && $_SESSION['negocio']) {
    $negocio_id = (int) $_SESSION['negocio'];
    $where = " negocio_id = '$negocio_id' AND ";
}

$sql = "SELECT * FROM proveedores WHERE $where estado != 'eliminado' AND nombre LIKE '%$search%' LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$sql_count = "SELECT COUNT(*) as total FROM proveedores WHERE $where estado != 'eliminado' AND nombre LIKE '%$search%'";
$count_result = $conn->query($sql_count);
$total_proveedores = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_proveedores / $limit);

$negocios  = "SELECT * FROM negocios ";
$list_negocios = $conn->query($negocios);
?>

<h1>Listado de Proveedores</h1>

<form action="home.php?m=pr" method="GET" class="mb-3">
    <input type="hidden" name="m" value="pr">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </div>
</form>

<a href="crear_proveedor.php" class="btn btn-primary mb-3">Crear Nuevo Proveedor</a>

<div class="card shadow mb-4">
    <div class="card-header">
        <h5>Lista de Proveedores</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['nombre']); ?></td>
                    <td><?= htmlspecialchars($row['telefono']); ?></td>
                    <td>
                        <?php
                        $status = strtolower($row['estado']);
                        echo $status === 'activo' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarProveedor(<?= $row['id']; ?>)">Editar</button>
                        <?php if ($row['estado'] == 'activo') { ?>
                            <button class="btn btn-danger btn-sm" onclick="cambiarStatusProveedor(<?= $row['id']; ?>, 'inactivo')">Bloquear</button>
                        <?php } else { ?>
                            <button class="btn btn-success btn-sm" onclick="cambiarStatusProveedor(<?= $row['id']; ?>, 'activo')">Activar</button>
                        <?php } ?>
                        <button class="btn btn-danger btn-sm" onclick="eliminarProveedor(<?= $row['id']; ?>)">Eliminar</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?m=pr&page=<?= $page - 1; ?>&search=<?= htmlspecialchars($search); ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?m=pr&page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"><?= $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?m=pr&page=<?= $page + 1; ?>&search=<?= htmlspecialchars($search); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarProveedor" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEditarProveedor">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Proveedor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editar_proveedor_id">
          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" id="editar_nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" id="editar_telefono" class="form-control" required>
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
<div class="modal fade" id="modalCrearProveedor" tabindex="-1">
  <div class="modal-dialog">
    <form id="formCrearProveedor">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Proveedor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?php if (isset($_SESSION['negocio']) && $_SESSION['negocio']) { ?>
              <input type="hidden" name="negocio" value="<?= $_SESSION['negocio']; ?>">
          <?php } else { ?>
              <div class="mb-3">
                  <label>Negocio</label>
                  <select name="negocio" class="form-select" required>
                      <option value="" disabled selected>Seleccione un negocio</option>
                      <?php foreach ($list_negocios as $n): ?>
                          <option value="<?= $n['id']; ?>"><?= htmlspecialchars($n['nombre']); ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
          <?php } ?>
          <div class="mb-3">
              <label>Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Teléfono</label>
              <input type="text" name="telefono" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Correo</label>
              <input type="email" name="correo" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Dirección</label>
              <input type="text" name="direccion" class="form-control" required>
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
function cambiarStatusProveedor(id, nuevoStatus) {
    showLoading();
    $.post('../controllers/proveedor_controller.php', {
        action: 'cambiar_status_proveedor',
        id: id,
        status: nuevoStatus
    }, function() {
        window.location.reload();
    });
}

function editarProveedor(id) {
    showLoading('Cargando proveedor...');
    $.get('../controllers/proveedor_controller.php', { action: 'obtener_proveedor', id: id }, function(data) {
        Swal.close();
        const proveedor = JSON.parse(data);
        $('#editar_proveedor_id').val(proveedor.id);
        $('#editar_nombre').val(proveedor.nombre);
        $('#editar_telefono').val(proveedor.telefono);
        $('#modalEditarProveedor').modal('show');
    });
}

$('#formEditarProveedor').submit(function(e) {
    e.preventDefault();
    const nombre = $('#editar_nombre').val();
    const telefono = $('#editar_telefono').val();

    if (!validateNotEmpty(nombre)) { showErrorAlert('Nombre obligatorio'); return; }
    if (!validateNotEmpty(telefono)) { showErrorAlert('Teléfono obligatorio'); return; }
    if (!validatePhone(telefono)) { showErrorAlert('Teléfono: 10 dígitos, inicia con 3'); return; }

    showLoading();
    $.post('../controllers/proveedor_controller.php', $(this).serialize() + '&action=editar_proveedor', function() {
        Swal.fire({
            icon: 'success',
            title: 'Proveedor actualizado',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#modalEditarProveedor').modal('hide');
            location.reload();
        });
    });
});

$('a[href="crear_proveedor.php"]').click(function(e) {
    e.preventDefault();
    $('#modalCrearProveedor').modal('show');
});

$('#formCrearProveedor').submit(function(e) {
    e.preventDefault();

    const negocio = $('select[name="negocio"]').val(); // Might be hidden input or select
    const nombre = $('#formCrearProveedor input[name="nombre"]').val();
    const telefono = $('#formCrearProveedor input[name="telefono"]').val();
    const correo = $('#formCrearProveedor input[name="correo"]').val();
    const direccion = $('#formCrearProveedor input[name="direccion"]').val();

    if ($('select[name="negocio"]').is(':visible') && !validateNotEmpty(negocio)) { showErrorAlert('Seleccione un negocio'); return; }
    if (!validateNotEmpty(nombre)) { showErrorAlert('Nombre obligatorio'); return; }
    if (!validateNotEmpty(telefono)) { showErrorAlert('Teléfono obligatorio'); return; }
    if (!validatePhone(telefono)) { showErrorAlert('Teléfono: 10 dígitos, inicia con 3'); return; }
    if (!validateNotEmpty(correo)) { showErrorAlert('Correo obligatorio'); return; }
    if (!validateEmail(correo)) { showErrorAlert('Correo inválido'); return; }
    if (!validateNotEmpty(direccion)) { showErrorAlert('Dirección obligatoria'); return; }

    showLoading();
    $.post('../controllers/proveedor_controller.php', $(this).serialize() + '&action=crear_proveedor', function() {
        Swal.fire({
            icon: 'success',
            title: 'Proveedor creado',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#modalCrearProveedor').modal('hide');
            location.reload();
        });
    });
});

function eliminarProveedor(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "El proveedor será marcado como eliminado y no aparecerá en los listados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading('Eliminando...');
            $.post('../controllers/proveedor_controller.php', { 
                action: 'eliminar_proveedor', 
                id: id 
            }, function(response) {
                if (response.trim() === 'ok') {
                    Swal.fire('Eliminado', 'El proveedor ha sido eliminado.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', 'Hubo un problema al eliminar el proveedor.', 'error');
                }
            }).fail(function() {
                Swal.fire('Error', 'Error de conexión.', 'error');
            });
        }
    });
}
</script>
