<?php
$limit = 10;  // Número de usuarios por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Página actual
$offset = ($page - 1) * $limit;

// Filtro por nombre o correo - SANITIZADO
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Obtener los usuarios filtrados
$sql = "SELECT * FROM lavadoras WHERE status != 'eliminado' AND codigo LIKE '%$search%' LIMIT $limit OFFSET $offset";
if (isset($_SESSION['negocio']) && $_SESSION['negocio']) {
    $negocio_id = (int) $_SESSION['negocio'];
    $sql = "SELECT * FROM lavadoras WHERE negocio_id = '$negocio_id' AND status != 'eliminado' AND codigo LIKE '%$search%' LIMIT $limit OFFSET $offset";
}
$result = $conn->query($sql);

// Contar el total de usuarios para la paginación
$sql_count = "SELECT COUNT(*) as total FROM lavadoras WHERE status != 'eliminado' AND codigo LIKE '%$search%' ";
if (isset($_SESSION['negocio']) && $_SESSION['negocio']) {
    $negocio_id = (int) $_SESSION['negocio'];
    $sql_count = "SELECT COUNT(*) as total FROM lavadoras WHERE negocio_id = '$negocio_id' AND status != 'eliminado' AND codigo LIKE '%$search%' ";
}
$count_result = $conn->query($sql_count);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);


$negocios  = "SELECT * FROM negocios ";
$list_negocios = $conn->query($negocios);
$tipos_lavadora = [
    'Manual doble tina sin bomba',
    'Manual doble tina con bomba',
    'Automática de 18 libras',
    'Automática de 24 libras'
];

// Inicializar $list_usuarios para evitar undefined variable
$list_usuarios = [];
if (isset($_SESSION['negocio']) && $_SESSION['negocio']) {
    $negocio_id = (int) $_SESSION['negocio'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE conductor_negocio = ?");
    $stmt->bind_param("i", $negocio_id);
    $stmt->execute();
    $list_usuarios = $stmt->get_result();
}


?>

<h1>Listado de Lavadoras</h1>
<form action="home.php?m=l" method="GET" class="mb-3">
    <input type="hidden" name="m" value="l">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Buscar por direccion o telefono" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </div>
</form>

<!-- Botón para Crear Negocio -->
<a href="crear_negocio.php" class="btn btn-primary mb-3">Crear Nueva lavadora</a>

<!-- Tabla de Negocios -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h5>Lista de Negocios</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Codigo</th>
                    <th>Tipo de lavadora</th>
                    <th>En</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['codigo']; ?></td>
                        <td><?= $row['type']; ?></td>
                        <td><?= $row['en']; ?></td>
                        <td>
                            <?php
                            $status = strtolower($row['status']);
                            if ($status === "disponible" || $status === "activo") {
                                echo '<span class="badge bg-success">Activo</span>';
                            } else {
                                echo '<span class="badge bg-danger">Inactivo</span>';
                            }
                            ?>
                        </td>

                        <td>
                            <!-- Botones para editar y cambiar estado -->
                            <button class="btn btn-warning btn-sm" onclick="editarNegocio(<?php echo $row['id']; ?>)">Editar</button>
                            <?php if ($row['status'] == "disponible") { ?>
                                <button class="btn btn-danger btn-sm" onclick="cambiarStatus(<?php echo $row['id']; ?>, 'inactivo')">Bloquear</button>
                            <?php } else { ?>
                                <button class="btn btn-success btn-sm" onclick="cambiarStatus(<?php echo $row['id']; ?>, 'disponible')">Activar</button>
                            <?php } ?>
                            <button class="btn btn-info btn-sm" onclick="ver_informe(<?php echo $row['id']; ?>, 'disponible')">Info</button>
                            
                            <?php if($row['en'] == "bodega"){ ?>
                            <button class="btn btn-success btn-sm" onclick="asignar(<?php echo $row['id']; ?>)">Asignar</button>
                            <?php }else{ ?>
                                   <button class="btn btn-success btn-sm" onclick="devolver(<?php echo $row['id']; ?>)">Devolver</button>
                            <?php } ?>
                            <button class="btn btn-danger btn-sm" onclick="eliminarLavadora(<?php echo $row['id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?m=l&page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?m=l&page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?m=l&page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- Modal de edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formeditarNegocio">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar lavadora</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editar_id">
                    <div class="form-group">
                        <label>Codigo</label>
                        <input type="text" name="codigo" id="editar_codigo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de lavadora</label>
                        <select name="type" id="editar_type" class="form-control" required>
                            <option value="" disabled selected>Seleccione un tipo</option>
                            <?php foreach ($tipos_lavadora as $tipo) { ?>
                                <option value="<?= $tipo ?>"><?= $tipo ?></option>
                            <?php } ?>
                        </select>
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


<!-- Modal Crear Lavadora -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formCrearNegocio">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel">Crear Nueva Lavadora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <?php if (isset($_SESSION['negocio']) && $_SESSION['negocio']) { ?>
                        <input type="hidden" name="id" id="negocio_id" value="<?= $_SESSION['negocio']; ?>">
                    <?php } ?>

                    <div class="form-floating mb-3">
                        <input type="text" name="codigo" id="codigo" class="form-control" placeholder="Código de la lavadora" required>
                        <label for="codigo">Código</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="type" id="editar_type" class="form-control" required>
                            <option value="" disabled selected>Seleccione un tipo</option>
                            <?php foreach ($tipos_lavadora as $tipo) { ?>
                                <option value="<?= $tipo ?>"><?= $tipo ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if (!isset($_SESSION['negocio'])) { ?>
                        <div class="form-floating mb-3">
                            <select name="negocio" id="negocio" class="form-select" required>
                                <option value="" disabled selected>Seleccione un negocio</option>
                                <?php foreach ($list_negocios as $negocio): ?>
                                    <option value="<?= $negocio['id'] ?>"><?= htmlspecialchars($negocio['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="negocio">Negocio</label>
                        </div>
                    <?php } ?>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal de Transacciones -->
<div class="modal fade" id="modalTransacciones" tabindex="-1" aria-labelledby="modalTransaccionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transacciones de la Lavadora</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoTransacciones">
                Cargando transacciones...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Transacciones -->
<div class="modal fade" id="modalAsginar" tabindex="-1" aria-labelledby="modalTransaccionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Lavadora</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formAsignarLavadora">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="hidden" name="id_lavadora_asignar" id="id_lavadora_asignar" value="">
                        <select id="id_user" name="id_user" class="form-control" required>
                            <option value="id" disabled selected>Seleccione un tipo</option>
                            <?php foreach ($list_usuarios as $usuarios) { ?>
                                <option value="<?= $usuarios['id'] ?>"><?= $usuarios['nombre'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Asignar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal de Transacciones -->
<div class="modal fade" id="modaldevolver" tabindex="-1" aria-labelledby="modalTransaccionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Devolver Lavadora</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formdevolverLavadora">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="hidden" name="id_lavadora_devolver" id="id_lavadora_devolver" value="">
                        <input type="text" name="observacion" id="observacion" class="form-control" required>
                        <label for="codigo">Observación</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Devolver</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function asignar(id) {

        $('#modalAsginar').modal('show');
        $('#id_lavadora_asignar').val(id);
    }

    $('#formAsignarLavadora').submit(function(e) {
        e.preventDefault();
        const user = $('#id_user').val();
        if (!validateNotEmpty(user)) { showErrorAlert('Seleccione un usuario'); return; }

        showLoading('Asignando...');
        $.post('../controllers/lavadora_controller.php', $(this).serialize() + '&action=asginar', function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Asignada',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                $('#modalAsginar').modal('hide');
                location.reload();
            });
        });
    });


        function devolver(id) {
  
        $('#modaldevolver').modal('show');
        $('#id_lavadora_devolver').val(id);
    }

    $('#formdevolverLavadora').submit(function(e) {
        e.preventDefault();
        const obs = $('#observacion').val();
        if (!validateNotEmpty(obs)) { showErrorAlert('La observación es obligatoria'); return; }

        showLoading('Devolviendo...');
        $.post('../controllers/lavadora_controller.php', $(this).serialize() + '&action=devolver', function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Devuelta',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                $('#modaldevolver').modal('hide');
                location.reload();
            });
        });
    });



        function ver_informe(id) {
        $('#contenidoTransacciones').html("Cargando transacciones...");
        $('#modalTransacciones').modal('show');

        $.get('../controllers/lavadora_controller.php', {
            action: 'obtener_transacciones',
            lavadora_id: id
        }, function(response) {
            $('#contenidoTransacciones').html(response);
        });
    }

    function cambiarStatus(id, nuevoStatus) {
        showLoading();
        $.post('../controllers/lavadora_controller.php', {
            action: 'cambiar_status',
            id: id,
            status: nuevoStatus
        }, function(response) {
            window.location.reload();
        });
    }

    function editarNegocio(id) {
        showLoading('Cargando...');
        $.get('../controllers/lavadora_controller.php', {
            action: 'obtener_lavadora',
            id: id
        }, function(data) {
            Swal.close();
            const negocio = JSON.parse(data);
            $('#editar_id').val(negocio.id);
            $('#editar_codigo').val(negocio.codigo);
            $('#editar_type').val(negocio.type);
            $('#modalEditar').modal('show');
        });
    }

    $('#formeditarNegocio').submit(function(e) {
        e.preventDefault();
        const codigo = $('#editar_codigo').val();
        const type = $('#editar_type').val();

        if (!validateNotEmpty(codigo)) { showErrorAlert('Código obligatorio'); return; }
        if (!validateNotEmpty(type)) { showErrorAlert('Seleccione un tipo'); return; }

        showLoading();
        $.post('../controllers/lavadora_controller.php', $(this).serialize() + '&action=editar_lavadora', function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                $('#modalEditar').modal('hide');
                location.reload();
            });
        });
    });

    // Mostrar modal al hacer clic en "Crear Nuevo Negocio"
    $('a[href="crear_negocio.php"]').click(function(e) {
        e.preventDefault();
        $('#modalCrear').modal('show');
    });

    // Enviar formulario con AJAX
    // Enviar formulario con AJAX
    $('#formCrearNegocio').submit(function(e) {
        e.preventDefault();
        const codigo = $('#codigo').val();
        const type = $('#formCrearNegocio select[name="type"]').val();
        const negocio = $('#negocio').val();

        if (!validateNotEmpty(codigo)) { showErrorAlert('Código obligatorio'); return; }
        if (!validateNotEmpty(type)) { showErrorAlert('Seleccione un tipo'); return; }
        if ($('#negocio').is(':visible') && !validateNotEmpty(negocio)) { showErrorAlert('Seleccione un negocio'); return; }

        showLoading();
        $.post('../controllers/lavadora_controller.php', $(this).serialize() + '&action=crear_lavadora', function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Lavadora creada',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                $('#modalCrear').modal('hide');
                location.reload();
            });
        });
    });

function eliminarLavadora(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "La lavadora será marcada como eliminada y no aparecerá en los listados. El código quedará disponible para reutilizar.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading('Eliminando...');
            $.post('../controllers/lavadora_controller.php', { 
                action: 'eliminar_lavadora', 
                id: id 
            }, function(response) {
                if (response.trim() === 'ok') {
                    Swal.fire('Eliminado', 'La lavadora ha sido eliminada.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', 'Hubo un problema al eliminar la lavadora.', 'error');
                }
            }).fail(function() {
                Swal.fire('Error', 'Error de conexión.', 'error');
            });
        }
    });
}
</script>