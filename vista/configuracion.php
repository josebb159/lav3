<?php
// vista/configuracion.php

$sql = "SELECT * FROM config_general";
if(isset($_SESSION['negocio']) && $_SESSION['negocio']){
    $where = " id_negocio = '{$_SESSION['negocio']}'  ";
    $sql = "SELECT * FROM config WHERE $where";
}
$result = $conn->query($sql);

$tarifa = ""; 
$telefono = ""; 
$km = "";
$valor_minimo = "";
$porcentaje = "";
$max_intentos_cancelacion = 0;
$multa_cliente = 0;
$multa_domiciliario = 0;
$banner = ""; // Para imagen

// Pago Defaults
$payu_habilitado = 0; $payu_checkout_url=""; $payu_merchant_id=""; $payu_account_id=""; $payu_response_url=""; $payu_confirmation_url=""; $email_pay="";
$bancolombia_habilitado=0; $bancolombia_cuenta="";
$nequi_habilitado=0; $nequi_cuenta="";
$daviplata_habilitado=0; $daviplata_cuenta="";

// Contacto
$whatsapp_contacto=""; $correo_contacto="";


if(isset($_SESSION['negocio']) && $_SESSION['negocio']){
  // Negocio Logic
  $row = $result->fetch_assoc();
  if ($row) {
    $tarifa = $row['tarifa'] ?? '';
    $telefono = $row['telefono'] ?? '';
  }
}else{
  // Admin Logic
  $row = $result->fetch_assoc();
  if ($row) {
    $km = $row['km'] ?? '';
    $tarifa = $row['global_tarifa'] ?? ''; // Ojo: nombre en BD es global_tarifa
    $valor_minimo = $row['valor_minimo'] ?? '';
    $porcentaje = $row['porcentaje'] ?? '';
    
    $max_intentos_cancelacion = $row['max_intentos_cancelacion'] ?? 0;
    $multa_cliente = $row['multa_cliente'] ?? 0;
    $multa_domiciliario = $row['multa_domiciliario'] ?? 0;
    
    $banner = $row['banner'] ?? '';

    // Pagos
    $payu_habilitado = $row['payu_habilitado'] ?? 0;
    $payu_checkout_url = $row['payu_checkout_url'] ?? '';
    $payu_merchant_id = $row['payu_merchant_id'] ?? '';
    $payu_account_id = $row['payu_account_id'] ?? '';
    $payu_response_url = $row['payu_response_url'] ?? '';
    $payu_confirmation_url = $row['payu_confirmation_url'] ?? '';
    $email_pay = $row['email_pay'] ?? '';

    $bancolombia_habilitado = $row['bancolombia_habilitado'] ?? 0;
    $bancolombia_cuenta = $row['bancolombia_cuenta'] ?? '';

    $nequi_habilitado = $row['nequi_habilitado'] ?? 0;
    $nequi_cuenta = $row['nequi_cuenta'] ?? '';

    $daviplata_habilitado = $row['daviplata_habilitado'] ?? 0;
    $daviplata_cuenta = $row['daviplata_cuenta'] ?? '';

    // Contacto
    $whatsapp_contacto = $row['whatsapp_contacto'] ?? '';
    $correo_contacto = $row['correo_contacto'] ?? '';
  }
}
?>

<div class="row mb-4 animate__animated animate__fadeIn">
    <div class="col-12">
        <h2 class="text-gray-800 border-bottom pb-2">Configuración <small class="text-muted text-sm"><?= (isset($_SESSION['negocio']) && $_SESSION['negocio']) ? '(Perfil de Negocio)' : '(General del Sistema)' ?></small></h2>
    </div>
</div>

<form id="formCrearNegocio" enctype="multipart/form-data">
    <?php if(isset($_SESSION['negocio']) && $_SESSION['negocio']): ?>
        <!-- VISTA NEGOCIO -->
        <input type="hidden" name="id" id="id" value="<?php echo $_SESSION['negocio']; ?>">
        
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-store me-2"></i>Información del Negocio</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tarifa" class="form-label fw-bold">Tarifa por Hora ($)</label>
                        <input type="number" step="0.01" class="form-control form-control-lg" id="tarifa" name="tarifa" value="<?= $tarifa; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telefono" class="form-label fw-bold">Teléfono de Contacto</label>
                        <input type="text" class="form-control form-control-lg" id="telefono" name="telefono" value="<?= $telefono; ?>" required>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- VISTA ADMIN -->
        <div class="row">
            
            <!-- Columna Izquierda: General y Tarifas -->
            <div class="col-lg-6">
                <!-- Tarjeta General -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h6 class="m-0 fw-bold"><i class="fas fa-cogs me-2"></i>General y Tarifas</h6>
                    </div>
                    <div class="card-body">
                        <!-- Banner -->
                        <div class="mb-4 text-center">
                            <label class="form-label d-block fw-bold text-start">Banner Promocional</label>
                            <div class="position-relative d-inline-block">
                                <img id="preview_banner" 
                                     src="<?= !empty($banner) ? '../upload/' . $banner : 'https://via.placeholder.com/400x150?text=Sin+Banner' ?>" 
                                     class="img-fluid rounded border shadow-sm mb-2" 
                                     style="max-height: 150px; width: 100%; object-fit: cover;" 
                                     alt="Banner Preview">
                            </div>
                            <input type="file" class="form-control mt-2" id="logo_negocio" name="logo_negocio" accept="image/*" onchange="previewImage(this)">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Rango KM</label>
                                <input type="number" step="0.01" class="form-control" name="km" id="km" value="<?= $km ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tarifa Hora Global ($)</label>
                                <input type="number" step="0.01" class="form-control" name="precio" id="precio" value="<?= $tarifa ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Porcentaje Cobro (%)</label>
                                <input type="number" step="0.01" class="form-control" name="porcentaje" id="porcentaje" value="<?= $porcentaje ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Valor Min. Servicio ($)</label>
                                <input type="number" step="0.01" class="form-control" name="min_servicio" id="min_servicio" value="<?= $valor_minimo ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta Multas y Políticas -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="m-0 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Multas y Políticas</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Max. Intentos Cancelación</label>
                            <input type="number" class="form-control" name="max_intentos_cancelacion" id="max_intentos_cancelacion" value="<?= $max_intentos_cancelacion ?>" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Multa Cliente ($)</label>
                                <input type="number" step="0.01" class="form-control" name="multa_cliente" id="multa_cliente" value="<?= $multa_cliente ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Multa Domiciliario ($)</label>
                                <input type="number" step="0.01" class="form-control" name="multa_domiciliario" id="multa_domiciliario" value="<?= $multa_domiciliario ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Pagos y Contacto -->
            <div class="col-lg-6">
                <!-- Tarjeta Métodos de Pago -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0 fw-bold"><i class="fas fa-credit-card me-2"></i>Métodos de Pago</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- PayU Toggle -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-check-label fw-bold" for="payu_habilitado">PayU (Pasarela)</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="payu_habilitado" name="payu_habilitado" <?= $payu_habilitado ? 'checked' : '' ?>>
                            </div>
                        </div>
                        
                        <div id="payu_config" class="bg-light p-3 rounded mb-3 border" style="<?= $payu_habilitado ? '' : 'display:none;' ?>">
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" name="payu_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars($payu_merchant_id) ?>">
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" name="payu_account_id" placeholder="Account ID" value="<?= htmlspecialchars($payu_account_id) ?>">
                            </div>
                            <div class="mb-2">
                                <input type="email" class="form-control form-control-sm" name="email_pay" id="email_pay" placeholder="Correo PayU" value="<?= htmlspecialchars($email_pay) ?>">
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="collapse" data-bs-target="#payuAdvanced">Ver configuración avanzada</button>
                            <div class="collapse mt-2" id="payuAdvanced">
                                <input type="text" class="form-control form-control-sm mb-2" name="payu_checkout_url" placeholder="URL Checkout" value="<?= htmlspecialchars($payu_checkout_url) ?>">
                                <input type="text" class="form-control form-control-sm mb-2" name="payu_response_url" placeholder="URL Respuesta" value="<?= htmlspecialchars($payu_response_url) ?>">
                                <input type="text" class="form-control form-control-sm" name="payu_confirmation_url" placeholder="URL Confirmación" value="<?= htmlspecialchars($payu_confirmation_url) ?>">
                            </div>
                        </div>

                        <hr>

                        <!-- Otras Billeteras -->
                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="bancolombia_habilitado" name="bancolombia_habilitado" <?= $bancolombia_habilitado ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="bancolombia_habilitado">Bancolombia Ahorros</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" name="bancolombia_cuenta" placeholder="No. Cuenta" value="<?= $bancolombia_cuenta ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="nequi_habilitado" name="nequi_habilitado" <?= $nequi_habilitado ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="nequi_habilitado">Nequi</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" name="nequi_cuenta" placeholder="No. Nequi" value="<?= $nequi_cuenta ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="daviplata_habilitado" name="daviplata_habilitado" <?= $daviplata_habilitado ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="daviplata_habilitado">Daviplata</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" name="daviplata_cuenta" placeholder="No. Daviplata" value="<?= $daviplata_cuenta ?>">
                        </div>

                    </div>
                </div>

                <!-- Tarjeta Contacto -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0 fw-bold"><i class="fas fa-address-book me-2"></i>Contacto Soporte</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fab fa-whatsapp text-success me-1"></i>WhatsApp</label>
                            <input type="text" class="form-control" name="whatsapp_contacto" id="whatsapp_contacto" value="<?= $whatsapp_contacto ?>">
                        </div>
                        <div class="">
                            <label class="form-label fw-bold"><i class="fas fa-envelope text-secondary me-1"></i>Correo</label>
                            <input type="email" class="form-control" name="correo_contacto" id="correo_contacto" value="<?= $correo_contacto ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>
    
    <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 100;">
        <button type="submit" class="btn btn-success btn-lg shadow-lg rounded-pill px-5"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
    </div>

</form>

<script>
// Preview de imagen
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#preview_banner').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle PayU
 document.getElementById('payu_habilitado').addEventListener('change', function() {
    const payuConfig = document.getElementById('payu_config');
    payuConfig.style.display = this.checked ? 'block' : 'none';
});

// Submit Form
$('#formCrearNegocio').submit(function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('action', 'guardar_config'); 

    // Validaciones básicas (se mantienen)
    if ($('#tarifa').length) {
        if (!validateNotEmpty($('#tarifa').val())) { showErrorAlert('Tarifa obligatoria'); return; }
    }
    
    // Admin Validations
    if ($('#payu_habilitado').length && $('#payu_habilitado').is(':checked')) {
         const emailPay = $('#email_pay').val();
         if (!validateNotEmpty(emailPay)) { showErrorAlert('Correo PayU obligatorio'); return; }
         if (!validateEmail(emailPay)) { showErrorAlert('Correo PayU inválido'); return; }
    }

    showLoading();
    $.ajax({
        url: '../controllers/config_controller.php',
        type: 'POST',
        data: formData,
        contentType: false,  
        processData: false,  
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Configuración actualizada',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo guardar la configuración.',
            });
        }
    });
});
</script>
