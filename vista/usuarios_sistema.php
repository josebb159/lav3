<?php
// Conectar a la base de datos

// Paginación
$limit = 10;  // Número de usuarios por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Página actual
$offset = ($page - 1) * $limit;

// Filtro por nombre o correo - SANITIZADO
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Obtener los usuarios filtrados
$sql = "SELECT * FROM usuarios WHERE rol_id in (1,2) AND (nombre LIKE '%$search%' OR correo LIKE '%$search%') ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Contar el total de usuarios para la paginación
$sql_count = "SELECT COUNT(*) as total FROM usuarios WHERE rol_id in (1,2) AND ( nombre LIKE '%$search%' OR correo LIKE '%$search%')";
$count_result = $conn->query($sql_count);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);


?>

<h1>Usuarios de la App</h1>
            
            <!-- Filtro de búsqueda -->
            <form action="home.php?m=us" method="GET" class="mb-3">
                <input type="hidden" name="m" value="us">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o correo" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>

            <!-- Tabla de usuarios -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Usuarios</h5>
                    <button class="btn btn-success" onclick="abrirModalCrear()"><i class="fas fa-plus"></i> Crear Usuario</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['correo']; ?></td>
                                <td>
                                    <?php echo $row['status'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                </td>
                                <td>
                                    <!-- Botones para Editar, Cambiar Contraseña y Bloquear -->
                                    <button class="btn btn-warning btn-sm" onclick="editarUsuario(<?php echo $row['id']; ?>)">Editar</button>
                                    <button class="btn btn-info btn-sm" onclick="cambiarContrasena(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nombre']); ?>')"><i class="fas fa-key"></i></button>
                                    <?php if ($row['status'] == 1) { ?>
                                        <button class="btn btn-danger btn-sm" onclick="cambiarStatus(<?php echo $row['id']; ?>, 0)">Bloquear</button>
                                    <?php } else { ?>
                                        <button class="btn btn-success btn-sm" onclick="cambiarStatus(<?php echo $row['id']; ?>, 1)">Activar</button>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?m=us&page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?m=us&page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?m=us&page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Modal de edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditarUsuario">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editar_id">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="editar_nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="correo" id="editar_correo" class="form-control" required>
                </div>
                <!-- Puedes añadir más campos si lo deseas -->
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal de creación de usuario -->
<div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formCrearUsuario">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Usuario del Sistema</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" id="crear_nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Correo Electrónico *</label>
                    <input type="email" name="correo" id="crear_correo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contraseña *</label>
                    <input type="password" name="contrasena" id="crear_contrasena" class="form-control" required minlength="6">
                    <small class="form-text text-muted">Mínimo 6 caracteres</small>
                </div>
                <div class="form-group">
                    <label>Confirmar Contraseña *</label>
                    <input type="password" name="confirmar_contrasena" id="crear_confirmar" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Rol *</label>
                    <select name="rol_id" id="crear_rol" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Crear Usuario</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal de cambio de contraseña -->
<div class="modal fade" id="modalContrasena" tabindex="-1" role="dialog" aria-labelledby="modalContrasenaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formCambiarContrasena">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Contraseña</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="contrasena_id">
                <p>Usuario: <strong id="contrasena_nombre"></strong></p>
                <div class="form-group">
                    <label>Nueva Contraseña *</label>
                    <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control" required minlength="6">
                    <small class="form-text text-muted">Mínimo 6 caracteres</small>
                </div>
                <div class="form-group">
                    <label>Confirmar Nueva Contraseña *</label>
                    <input type="password" name="confirmar_nueva" id="confirmar_nueva" class="form-control" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>

function cambiarStatus(id, nuevoStatus) {
    showLoading();
    $.post('../controllers/usuario_controller.php', {
        action: 'cambiar_status',
        id: id,
        status: nuevoStatus
    }, function(response) {
        window.location.reload();
    });
}

function editarUsuario(id) {
    showLoading('Cargando usuario...');
    $.get('../controllers/usuario_controller.php', { action: 'obtener_usuario', id: id }, function(data) {
        Swal.close();
        const usuario = JSON.parse(data);
        $('#editar_id').val(usuario.id);
        $('#editar_nombre').val(usuario.nombre);
        $('#editar_correo').val(usuario.correo);
        $('#modalEditar').modal('show');
    });
}

$('#formEditarUsuario').submit(function(e) {
    e.preventDefault();
    const nombre = $('#editar_nombre').val();
    const correo = $('#editar_correo').val();

    if (!validateNotEmpty(nombre)) { showErrorAlert('El nombre es obligatorio'); return; }
    if (!validateNotEmpty(correo)) { showErrorAlert('El correo es obligatorio'); return; }
    if (!validateEmail(correo)) { showErrorAlert('Correo inválido'); return; }

    showLoading();
    $.post('../controllers/usuario_controller.php', $(this).serialize() + '&action=editar_usuario', function(response) {
        if (response.trim() === 'error_correo_duplicado') {
            Swal.fire({
                icon: 'error',
                title: 'Correo ya registrado',
                text: 'El correo electrónico ya está registrado en el sistema. Por favor, utiliza otro correo.',
            });
            return;
        }
        Swal.fire({
            icon: 'success',
            title: 'Usuario actualizado',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#modalEditar').modal('hide');
            location.reload();
        });
    });
});

function abrirModalCrear() {
    $('#formCrearUsuario')[0].reset();
    $('#modalCrear').modal('show');
}

$('#formCrearUsuario').submit(function(e) {
    e.preventDefault();
    const nombre = $('#crear_nombre').val();
    const correo = $('#crear_correo').val();
    const contrasena = $('#crear_contrasena').val();
    const confirmar = $('#crear_confirmar').val();
    const rol_id = $('#crear_rol').val();

    if (!validateNotEmpty(nombre)) { showErrorAlert('El nombre es obligatorio'); return; }
    if (!validateNotEmpty(correo)) { showErrorAlert('El correo es obligatorio'); return; }
    if (!validateEmail(correo)) { showErrorAlert('Correo inválido'); return; }
    if (!validateNotEmpty(contrasena)) { showErrorAlert('La contraseña es obligatoria'); return; }
    if (contrasena.length < 6) { showErrorAlert('La contraseña debe tener al menos 6 caracteres'); return; }
    if (contrasena !== confirmar) { showErrorAlert('Las contraseñas no coinciden'); return; }
    if (!validateNotEmpty(rol_id)) { showErrorAlert('Debe seleccionar un rol'); return; }

    showLoading();
    $.post('../controllers/usuario_controller.php', $(this).serialize() + '&action=crear_usuario_sistema', function(response) {
        if (response.trim() === 'error_correo_duplicado') {
            Swal.fire({
                icon: 'error',
                title: 'Correo ya registrado',
                text: 'El correo electrónico ya está registrado en el sistema. Por favor, utiliza otro correo.',
            });
            return;
        }
        if (response.trim() === 'ok') {
            Swal.fire({
                icon: 'success',
                title: 'Usuario creado exitosamente',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                $('#modalCrear').modal('hide');
                location.reload();
            });
        } else {
            showErrorAlert('Error al crear usuario');
        }
    });
});

function cambiarContrasena(id, nombre) {
    $('#contrasena_id').val(id);
    $('#contrasena_nombre').text(nombre);
    $('#formCambiarContrasena')[0].reset();
    $('#contrasena_id').val(id); // Restaurar después del reset
    $('#modalContrasena').modal('show');
}

$('#formCambiarContrasena').submit(function(e) {
    e.preventDefault();
    const nueva = $('#nueva_contrasena').val();
    const confirmar = $('#confirmar_nueva').val();

    if (!validateNotEmpty(nueva)) { showErrorAlert('La nueva contraseña es obligatoria'); return; }
    if (nueva.length < 6) { showErrorAlert('La contraseña debe tener al menos 6 caracteres'); return; }
    if (nueva !== confirmar) { showErrorAlert('Las contraseñas no coinciden'); return; }

    showLoading();
    $.post('../controllers/usuario_controller.php', $(this).serialize() + '&action=cambiar_contrasena', function(response) {
        if (response.trim() === 'ok') {
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                text: 'La contraseña ha sido cambiada exitosamente',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                $('#modalContrasena').modal('hide');
            });
        } else {
            showErrorAlert('Error al cambiar la contraseña');
        }
    });
});

</script>


