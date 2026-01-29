<?php

$lavadoras = [];

// CAMBIO: Obtener precios globales (id_negocio = 0) en lugar de precios por negocio
$sql = "SELECT * FROM precios_lavado WHERE id_negocio = 0";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $lavadoras[$row['tipo_lavadora']][$row['tipo_servicio']] = $row['precio'];
}

$tipos_lavadora = [
    'Manual doble tina sin bomba',
    'Manual doble tina con bomba',
    'Automática de 18 libras',
    'Automática de 24 libras'
];
$tipos_servicio = ['normal', '24horas', 'nocturno'];
?>

<!-- Formulario -->
<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle"></i> <strong>Precios Globales:</strong> Estos precios se aplicarán a todas las empresas del sistema.
</div>

<div id="contenedorFormulario" class="mt-4">
  <form id="formPrecioLavadoras">
    <?php foreach ($tipos_lavadora as $lavadora): ?>
      <div class="mb-3 border p-3 rounded">
        <h5><?= $lavadora ?></h5>
        <?php foreach ($tipos_servicio as $servicio): 
          $valor = $lavadoras[$lavadora][$servicio] ?? '';
        ?>
          <div class="mb-2">
            <label class="form-label"><?= ucfirst($servicio) ?></label>
            <input 
              type="number" step="0.01" class="form-control"
              name='precios[<?= $lavadora ?>][<?= $servicio ?>]'
              value='<?= htmlspecialchars($valor) ?>' required>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-success">Guardar Precios Globales</button>
  </form>
</div>

<script>
$('#formPrecioLavadoras').submit(function(e) {
    e.preventDefault();
    showLoading();
    
    $.post('../controllers/precio_controller.php', $(this).serialize() + '&action=guardar_precios_lavadoras', function(response) {
        Swal.fire({
            icon: 'success',
            title: 'Precios globales guardados',
            text: 'Los precios se aplicarán a todas las empresas',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            location.reload();
        });
    }).fail(function(xhr) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: xhr.responseText || 'Error al guardar los precios',
        });
    });
});
</script>
