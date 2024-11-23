<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>
<style>
    /* Estilos previos aquí... */
    .badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        text-transform: capitalize;
        cursor: pointer;
    }

    .badge.success {
        background-color: #27ae60;
        color: white;
    }

    .badge.confirmed {
        background-color: #f39c12;
        color: white;
    }

    .badge.pending {
        background-color: #3498db;
        color: white;
    }

    .badge.completed {
        background-color: #8e44ad;
        color: white;
    }

    .badge.cancelled {
        background-color: #e74c3c;
        color: white;
    }
</style>


<!-- Row Card No Padding -->
<div class="row row-card-no-pd">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="icon-heart text-warning"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Pets</p>
                            <h4 class="card-title"><?= esc($total_pets) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="icon-user text-success"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Owners</p>
                            <h4 class="card-title"><?= esc($total_owners) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="icon-diamond text-danger"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Products</p>
                            <h4 class="card-title"><?= esc($total_products) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="icon-calendar text-primary"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Appointments Today</p>
                            <h4 class="card-title"><?= esc($total_appointments) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?= $title ?></div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped-columns table-sm">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>Propietario</th>
                                <th>Mascota</th>
                                <th>Hora</th>
                                <th>Razón</th>
                                <th>Estado</th>
                                <th>Cambiar Estado</th>
                                <th>Acciones</th> <!-- Nueva columna para acciones -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="alert alert-warning">No Hay Citas!</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <!-- Bucle de las citas -->
                                <?php foreach ($appointments as $index => $appointment): ?>
                                    <tr id="appointment-<?= $appointment['id'] ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></td>
                                        <td><?= $appointment['name'] ?></td>
                                        <td><?= $appointment['appointment_time'] ?></td>
                                        <td><?= $appointment['reason'] ?></td>
                                        <td>
                                            <span class="badge <?= $appointment['status'] ?>">
                                                <?= ucfirst($appointment['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <select class="form-control change-status" data-id="<?= $appointment['id'] ?>">
                                                <option value="pending"
                                                    <?= $appointment['status'] == 'pending' ? 'selected' : '' ?>>Pending
                                                </option>
                                                <option value="confirmed"
                                                    <?= $appointment['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed
                                                </option>
                                                <option value="completed"
                                                    <?= $appointment['status'] == 'completed' ? 'selected' : '' ?>>Completed
                                                </option>
                                                <option value="cancelled"
                                                    <?= $appointment['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary send-reminder btn-sm"
                                                data-id="<?= $appointment['id'] ?>"
                                                data-email="<?= $appointment['email'] ?>">Enviar Recordatorio</button>
                                        </td> <!-- Columna para el botón de recordatorio -->
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>


            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>


</div>
</div>
</div>
</div>




<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        $('.change-status').on('change', function() {
            var appointmentId = $(this).data('id');
            var newStatus = $(this).val();

            // Enviar solicitud AJAX al servidor para actualizar el estado
            $.ajax({
                url: '<?= base_url('appointments/updateAppointmentStatus') ?>', // Asegúrate de cambiar esta URL a la correcta en tu aplicación
                method: 'POST',
                data: {
                    id: appointmentId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        // Actualizar el badge en la tabla
                        var badge = $('#appointment-' + appointmentId + ' .badge');
                        badge.removeClass().addClass('badge ' + newStatus);
                        badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    } else {
                        alert('Error al actualizar el estado.');
                    }
                },
                error: function() {
                    alert('Error en la solicitud.');
                }
            });
        });
    });

    $(document).ready(function() {
        // Capturamos el clic en el botón de enviar recordatorio
        $('.send-reminder').on('click', function() {
            const appointmentId = $(this).data('id'); // Obtenemos el ID de la cita
            const email = $(this).data('email'); // Obtenemos el correo del cliente

            // Enviamos la solicitud AJAX
            $.ajax({
                url: '<?= base_url('appointments/sendReminder') ?>', // URL del controlador en CodeIgniter
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: appointmentId,
                    email: email
                }),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Enviado!',
                            text: 'El recordatorio fue enviado con éxito.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al enviar el recordatorio: ' +
                                response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el servidor',
                        text: 'Hubo un problema con el servidor. Inténtelo de nuevo más tarde.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>