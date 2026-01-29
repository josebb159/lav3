<?php
// Conexión a base de datos (ajusta con tus datos)


// Consulta
$sql = "SELECT * FROM terminos_condiciones LIMIT 1";
$result = $conn->query($sql);

$terminos = "";
$terminos_uso = "";
$terminos_delivery = "";
$terminos_uso_delivery = "";

if ($result && $row = $result->fetch_assoc()) {
    $terminos = $row['terminos'] ?? "";
    $terminos_uso = $row['terminos_uso'] ?? "";
    $terminos_delivery = $row['terminos_delivery'] ?? "";
    $terminos_uso_delivery = $row['terminos_uso_delivery'] ?? "";
}
?>

<div id="contenedorFormulario" class="mt-4">
  <form id="formTerminos">
    <div class="mb-3">
      <label for="terminos" class="form-label">Términos y Condiciones</label>
      <textarea class="form-control" id="terminos" name="terminos" rows="6" required><?= htmlspecialchars($terminos) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="terminos_uso" class="form-label">Términos de Uso</label>
      <textarea class="form-control" id="terminos_uso" name="terminos_uso" rows="6" required><?= htmlspecialchars($terminos_uso) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="terminos_delivery" class="form-label">Términos de Delivery</label>
      <textarea class="form-control" id="terminos_delivery" name="terminos_delivery" rows="6" required><?= htmlspecialchars($terminos_delivery) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="terminos_uso_delivery" class="form-label">Términos de Uso de Delivery</label>
      <textarea class="form-control" id="terminos_uso_delivery" name="terminos_uso_delivery" rows="6" required><?= htmlspecialchars($terminos_uso_delivery) ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#formTerminos').submit(function(e) {
  e.preventDefault();
  
  const terminos = $('#terminos').val();
  const terminos_uso = $('#terminos_uso').val();
  const terminos_delivery = $('#terminos_delivery').val();
  const terminos_uso_delivery = $('#terminos_uso_delivery').val();

  if (!validateNotEmpty(terminos)) { showErrorAlert('Términos y Condiciones obligatorio'); return; }
  if (!validateNotEmpty(terminos_uso)) { showErrorAlert('Términos de Uso obligatorio'); return; }
  if (!validateNotEmpty(terminos_delivery)) { showErrorAlert('Términos de Delivery obligatorio'); return; }
  if (!validateNotEmpty(terminos_uso_delivery)) { showErrorAlert('Términos de Uso de Delivery obligatorio'); return; }


  
  showLoading();
  $.post('../controllers/terminos_controller.php', $(this).serialize() + '&action=guardar_config', function(response) {
    Swal.fire({
        icon: 'success',
        title: 'Guardado',
        text: 'Términos actualizados correctamente',
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        location.reload();
    });
  }).fail(function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al guardar los términos.',
    });
  });
});
</script>
