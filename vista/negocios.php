<?php
$limit = 10;  // Número de usuarios por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Página actual
$offset = ($page - 1) * $limit;

// Filtro por nombre o correo - SANITIZADO
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Obtener los usuarios filtrados
$sql = "SELECT * FROM negocios WHERE nombre LIKE '%$search%' OR direccion LIKE '%$search%' OR telefono LIKE '%$search%' OR ciudad LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Contar el total de usuarios para la paginación
$sql_count = "SELECT COUNT(*) as total FROM negocios WHERE nombre LIKE '%$search%' OR direccion LIKE '%$search%' OR telefono LIKE '%$search%' OR ciudad LIKE '%$search%'";
$count_result = $conn->query($sql_count);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);


?>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<h1>Listado de Negocios</h1>
<form action="home.php?m=n" method="GET" class="mb-3">
                <input type="hidden" name="m" value="n">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por direccion o telefono" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>

            <!-- Botón para Crear Negocio -->
            <a href="crear_negocio.php" class="btn btn-primary mb-3">Crear Nuevo Negocio</a>

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
                                <th>Nombre del Negocio</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['direccion']; ?></td>
                                <td><?php echo $row['telefono']; ?></td>
                                <td>
                                    <?php
                                    // Mostrar el estado con color dependiendo del valor
                                    if ($row['status'] == 1) {
                                        echo '<span class="badge bg-success">Activo</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Inactivo</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <!-- Botones para editar y cambiar estado -->
                                    <button class="btn btn-warning btn-sm" onclick="editarNegocio(<?php echo $row['id']; ?>)">Editar</button>
                                    <?php if ($row['status'] == 1) { ?>
                                        <button class="btn btn-danger btn-sm" onclick="cambiarStatus(<?php echo $row['id']; ?>, 0)">Bloquear</button>
                                    <?php } else { ?>
                                        <button class="btn btn-success btn-sm" onclick="cambiarStatus(<?php echo $row['id']; ?>, 1)">Activar</button>
                                    <?php } ?>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarNegocio(<?php echo $row['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?m=n&page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?m=n&page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?m=n&page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Modal de edición -->
            <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formeditarNegocio">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Negocio</h5>
          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editar_id">
          <input type="hidden" name="latitud" id="editar_latitud">
          <input type="hidden" name="longitud" id="editar_longitud">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" id="editar_nombre" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" id="editar_direccion" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" id="editar_telefono" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Ciudad</label>
                <input type="text" name="ciudad" id="editar_ciudad" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Ubicación</label>
                <div id="mapEditar" style="height: 300px; border-radius: 5px;"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Modal Crear Negocio -->
<div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"> <!-- Modal más grande -->
    <form id="formCrearNegocio" class="w-100">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalCrearLabel">Crear Nuevo Negocio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <!-- Sección: Datos del Negocio -->
          <h5 class="mb-3 text-primary">Datos del Negocio</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Dirección</label>
              <input type="text" name="direccion" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Teléfono</label>
              <input type="text" name="telefono" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Ciudad</label>
              <input type="text" name="ciudad" class="form-control" required>
            </div>
          </div>

          <!-- Sección: Mapa -->
          <h5 class="mb-3 text-primary">Ubicación en el Mapa</h5>
          <div class="form-group mb-3">
            <div id="map" style="height: 300px; border: 1px solid #ccc; border-radius: 6px;"></div>
            <input type="hidden" name="latitud" id="latitud">
            <input type="hidden" name="longitud" id="longitud">
          </div>

          <!-- Sección: Datos del Usuario -->
          <h5 class="mt-4 mb-3 text-primary">Datos del Usuario</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Nombre</label>
              <input type="text" name="usuario_nombre" id="editar_usuario_nombre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Apellido</label>
              <input type="text" name="usuario_apellido" id="editar_usuario_apellido" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Teléfono</label>
              <input type="text" name="usuario_telefono" id="editar_usuario_telefono" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Correo</label>
              <input type="email" name="usuario_correo" id="editar_usuario_correo" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Usuario</label>
              <input type="text" name="usuario_usuario" id="editar_usuario_usuario" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Contraseña</label>
              <input type="text" name="contrasena" id="editar_contrasena" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Crear Negocio</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


            <script>

document.addEventListener('DOMContentLoaded', function () {
  var map = L.map('map').setView([10.96854, -74.78132], 13); // Coordenadas por defecto (ej: Barranquilla)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  var marker;

  map.on('click', function(e) {
      var lat = e.latlng.lat;
      var lng = e.latlng.lng;

      document.getElementById('latitud').value = lat;
      document.getElementById('longitud').value = lng;

      if (marker) {
          marker.setLatLng(e.latlng);
      } else {
          marker = L.marker(e.latlng).addTo(map);
      }
  });

  // Mostrar el mapa cuando se abre el modal
  $('#modalCrear').on('shown.bs.modal', function () {
      setTimeout(function () {
          map.invalidateSize();
      }, 200);
  });
});

function cambiarStatus(id, nuevoStatus) {
    showLoading();
    $.post('../controllers/negocio_controller.php', {
        action: 'cambiar_status',
        id: id,
        status: nuevoStatus
    }, function(response) {
        window.location.reload();
    });
}

function editarNegocio(id) {
    showLoading('Cargando datos...');
    $.get('../controllers/negocio_controller.php', { action: 'obtener_negocio', id: id }, function(data) {
        Swal.close();
        const negocio = JSON.parse(data);
        $('#editar_id').val(negocio.id);
        $('#editar_nombre').val(negocio.nombre);
        $('#editar_direccion').val(negocio.direccion);
        $('#editar_telefono').val(negocio.telefono);
        $('#editar_ciudad').val(negocio.ciudad);

        $('#editar_latitud').val(negocio.latitud);
        $('#editar_longitud').val(negocio.longitud);
    
    // Esperar a que se muestre el modal antes de cargar el mapa
    $('#modalEditar').modal('show');

     // Esperar a que el modal esté visible para renderizar el mapa
     $('#modalEditar').on('shown.bs.modal', function () {
      initMapEditar(negocio.latitud, negocio.longitud);
    });
    });
}

$('#formeditarNegocio').submit(function(e) {
    e.preventDefault();
    
    const nombre = $('#editar_nombre').val();
    const direccion = $('#editar_direccion').val();
    const telefono = $('#editar_telefono').val();
    const ciudad = $('#editar_ciudad').val();

    if (!validateNotEmpty(nombre)) { showErrorAlert('El nombre es obligatorio'); return; }
    if (!validateNotEmpty(direccion)) { showErrorAlert('La dirección es obligatoria'); return; }
    if (!validateNotEmpty(telefono)) { showErrorAlert('El teléfono es obligatorio'); return; }
    if (!validatePhone(telefono)) { showErrorAlert('El teléfono debe tener 10 dígitos y comenzar con 3'); return; }
    if (!validateNotEmpty(ciudad)) { showErrorAlert('La ciudad es obligatoria'); return; }

    showLoading();
    $.post('../controllers/negocio_controller.php', $(this).serialize() + '&action=editar_negocio', function(response) {
        Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: 'Negocio actualizado correctamente',
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
$('#formCrearNegocio').submit(function(e) {
    e.preventDefault();

    // Validaciones
    // Validaciones - Scoped to this form to avoid Edit modal conflicts
    const nombre = $('#formCrearNegocio input[name="nombre"]').val();
    const direccion = $('#formCrearNegocio input[name="direccion"]').val();
    const telefono = $('#formCrearNegocio input[name="telefono"]').val();
    const ciudad = $('#formCrearNegocio input[name="ciudad"]').val();
    
    // Usuario validation
    const uNombre = $('#formCrearNegocio input[name="usuario_nombre"]').val();
    const uApellido = $('#formCrearNegocio input[name="usuario_apellido"]').val();
    const uTelefono = $('#formCrearNegocio input[name="usuario_telefono"]').val();
    const uCorreo = $('#formCrearNegocio input[name="usuario_correo"]').val();
    const uUsuario = $('#formCrearNegocio input[name="usuario_usuario"]').val();
    const uPass = $('#formCrearNegocio input[name="contrasena"]').val();

    if (!validateNotEmpty(nombre)) { showErrorAlert('Nombre del negocio obligatorio'); return; }
    if (!validateNotEmpty(direccion)) { showErrorAlert('Dirección obligatoria'); return; }
    if (!validateNotEmpty(telefono)) { showErrorAlert('Teléfono del negocio obligatorio'); return; }
    if (!validatePhone(telefono)) { showErrorAlert('Teléfono negocio: 10 dígitos, inicia con 3'); return; }
    if (!validateNotEmpty(ciudad)) { showErrorAlert('Ciudad obligatoria'); return; }

    if (!validateNotEmpty(uNombre)) { showErrorAlert('Nombre usuario obligatorio'); return; }
    if (!validateNotEmpty(uApellido)) { showErrorAlert('Apellido usuario obligatorio'); return; }
    if (!validateNotEmpty(uTelefono)) { showErrorAlert('Teléfono usuario obligatorio'); return; }
    if (!validatePhone(uTelefono)) { showErrorAlert('Teléfono usuario: 10 dígitos, inicia con 3'); return; }
    if (!validateNotEmpty(uCorreo)) { showErrorAlert('Correo usuario obligatorio'); return; }
    if (!validateEmail(uCorreo)) { showErrorAlert('Correo inválido'); return; }
    if (!validateNotEmpty(uUsuario)) { showErrorAlert('Usuario obligatorio'); return; }
    if (!validateNotEmpty(uPass)) { showErrorAlert('Contraseña obligatoria'); return; }

    showLoading();
    $.post('../controllers/negocio_controller.php', 
        $(this).serialize() + '&action=crear_negocio', 
        function(response) {

            if (response === 'ok') {
                $('#modalCrear').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Negocio creado correctamente',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    location.reload();
                }, 1500);

            } else if (response === 'error_correo') {
                Swal.fire({
                    icon: 'error',
                    title: 'Correo ya registrado',
                    text: 'El correo ingresado ya existe en el sistema.',
                });

            } else if (response === 'error_usuario') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear usuario',
                    text: 'No fue posible registrar el usuario.',
                });

            } else if (response === 'error_negocio') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear negocio',
                    text: 'El usuario fue creado, pero falló el registro del negocio.',
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error desconocido',
                    text: 'Respuesta del servidor: ' + response
                });
            }
        }
    );
});



let mapEditar;
let markerEditar;

function initMapEditar(lat, lng) {
  const latNum = parseFloat(lat) || 20.659698; // México por defecto
  const lngNum = parseFloat(lng) || -103.349609;

  if (mapEditar) {
    mapEditar.remove(); // Reiniciar si ya existe
  }

  mapEditar = L.map('mapEditar').setView([latNum, lngNum], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(mapEditar);

  markerEditar = L.marker([latNum, lngNum], { draggable: true }).addTo(mapEditar);

  // Actualizar los campos cuando se mueva el marcador
  markerEditar.on('dragend', function (e) {
    const position = markerEditar.getLatLng();
    $('#editar_latitud').val(position.lat);
    $('#editar_longitud').val(position.lng);
  });

  // Inicializar los campos con valores actuales
  $('#editar_latitud').val(latNum);
  $('#editar_longitud').val(lngNum);
}

function eliminarNegocio(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto! Se eliminará el negocio y el usuario asociado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading('Eliminando...');
            $.post('../controllers/negocio_controller.php', {
                action: 'eliminar_negocio',
                id: id
            }, function(response) {
                if (response === 'ok') {
                    Swal.fire(
                        '¡Eliminado!',
                        'El negocio y el usuario han sido eliminados.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error',
                        'Hubo un problema al eliminar el negocio.',
                        'error'
                    );
                }
            });
        }
    })
}
</script>
