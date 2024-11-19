<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>



<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="card-title mb-0"><?= $title; ?></h5>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="card-title mb-0">Info!</h5>
                </div>
                <div>
                    <br>
                    <p class="text-muted">Aca una lista del Tipo de evento según su color!</p>
                    <div class="external-event bg-primary">
                        <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i> Evento Normal
                    </div>
                    <div class="external-event bg-success">
                        <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i> Evento Sin Afán
                    </div>
                    <div class="external-event bg-danger">
                        <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i> Evento Urgente
                    </div>
                    <div class="external-event bg-warning">
                        <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i> Evento Importante
                    </div>
                    <div class="external-event bg-info">
                        <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i> Evento Informativo
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="event-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-event">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event-title" class="form-label">Titulo</label>
                        <input type="text" class="form-control" id="event-title" required>
                    </div>
                    <div class="mb-3">
                        <label for="event-category" class="form-label">Categoría</label>
                        <select class="select2" id="event-category" required>
                            <option value="bg-primary">Normal</option>
                            <option value="bg-success">Sin Afán</option>
                            <option value="bg-danger">Urgente</option>
                            <option value="bg-warning">Importante</option>
                            <option value="bg-info">Informativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btn-delete-event"
                        data-bs-dismiss="modal">Delete</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-event">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<!-- plugin js -->
<link href="<?= assets_url('admin/js/plugin/fullcalendar/main.min.css') ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= assets_url('admin/js/plugin/moment/min/moment.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/fullcalendar/main.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        var calendarEl = document.getElementById('calendar');
        var selectedDate;
        var eventToEdit = null; // Variable global para almacenar el evento en edición

        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: {
                url: '<?= base_url('calendar/loadEvents') ?>',
                method: 'GET'
            },
            selectable: true,
            editable: true,
            dateClick: function(info) {
                $('#form-event')[0].reset();
                $('#modal-title').text('Add New Event');
                $('#event-modal').modal('show');
                selectedDate = info.date;
                eventToEdit = null; // Resetear la variable para creación de un nuevo evento
            },
            eventClick: function(info) {
                $('#form-event')[0].reset();
                $('#modal-title').text('Edit Event');
                $('#event-title').val(info.event.title);
                $('#event-category').val(info.event.classNames[0]);
                $('#event-modal').modal('show');
                eventToEdit = info.event; // Guardar el evento seleccionado para su edición
            },
            eventDrop: function(info) {
                Swal.fire({
                    title: 'Confirm Date Change',
                    text: "Do you want to change the date of this event?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url('calendar/updateEventDate') ?>',
                            type: 'POST',
                            data: {
                                id: info.event.id,
                                start: info.event.start.toISOString(),
                                end: info.event.end ? info.event.end.toISOString() : null
                            },
                            success: function() {
                                Swal.fire(
                                    'Date Updated!',
                                    'The event date has been updated.',
                                    'success'
                                );
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'There was an error updating the event date.',
                                    'error'
                                );
                                info
                                    .revert(); // Revert the event if the AJAX call fails
                            }
                        });
                    } else {
                        info.revert(); // Revert the event if the user cancels
                    }
                });
            }
        });

        calendar.render();

        // Cambia el código de envío del formulario para manejar tanto creación como edición
        $('#form-event').on('submit', function(e) {
            e.preventDefault();

            if (eventToEdit) {
                // Modo de edición
                $.ajax({
                    url: '<?= base_url('calendar/updateEvent') ?>',
                    type: 'POST',
                    data: {
                        id: eventToEdit.id,
                        title: $('#event-title').val(),
                        className: $('#event-category').val()
                    },
                    success: function() {
                        eventToEdit.setProp('title', $('#event-title').val());
                        eventToEdit.setProp('classNames', [$('#event-category').val()]);
                        $('#event-modal').modal('hide');
                        Swal.fire(
                            'Updated!',
                            'Your event has been updated.',
                            'success'
                        );
                        eventToEdit = null; // Resetear la variable
                    }
                });
            } else {
                // Modo de creación
                if (!selectedDate) {
                    Swal.fire('Error', 'No date selected.', 'error');
                    return;
                }

                $.ajax({
                    url: '<?= base_url('calendar/addEvent') ?>',
                    type: 'POST',
                    data: {
                        title: $('#event-title').val(),
                        start: selectedDate.toISOString(),
                        className: $('#event-category').val()
                    },
                    success: function() {
                        calendar.refetchEvents();
                        $('#event-modal').modal('hide');
                        Swal.fire(
                            'Added!',
                            'Your event has been added.',
                            'success'
                        );
                    }
                });
            }
        });

        // Lógica de eliminación de eventos permanece igual
        $('#btn-delete-event').on('click', function() {
            if (!eventToEdit) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('calendar/deleteEvent') ?>',
                        type: 'POST',
                        data: {
                            id: eventToEdit.id
                        },
                        success: function() {
                            eventToEdit.remove();
                            $('#event-modal').modal('hide');
                            Swal.fire(
                                'Deleted!',
                                'Your event has been deleted.',
                                'success'
                            );
                            eventToEdit = null; // Resetear la variable
                        }
                    });
                }
            });
        });
    });
</script>


</body>

</html>


<?= $this->endSection() ?>