<?php
// Conexión a base de datos aquí

// Obtener negocios para el select
$negocios = $conn->query("SELECT id, nombre FROM negocios WHERE status = 1");
?>

<h3>Generar Reporte de Alquileres</h3>
<form action="generar_reporte_pdf.php" method="GET" target="_blank">
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Desde:</label>
            <input type="date" name="fecha_inicio" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Hasta:</label>
            <input type="date" name="fecha_fin" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Negocio:</label>
            <select name="negocio_id" class="form-control">
                <option value="">-- Todos --</option>
                <?php while ($row = $negocios->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <button type="submit" id="btnGenerar" class="btn btn-success">Generar PDF</button>
</form>

<script>
$('form').submit(function(e) {
    const inicio = $('input[name="fecha_inicio"]').val();
    const fin = $('input[name="fecha_fin"]').val();

    if (!validateNotEmpty(inicio)) { showErrorAlert('Fecha inicio obligatoria'); e.preventDefault(); return; }
    if (!validateNotEmpty(fin)) { showErrorAlert('Fecha fin obligatoria'); e.preventDefault(); return; }
    
    if (new Date(inicio) > new Date(fin)) {
        showErrorAlert('La fecha de inicio no puede ser mayor a la fecha fin');
        e.preventDefault();
        return;
    }
});
</script>
