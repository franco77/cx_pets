<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>

<style>
.ui-timepicker-container {
    z-index: 3500 !important;
}
</style>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="ion ion-clipboard me-1"></i> To Do List
        </h3>
        <button type="button" class="btn btn-primary" id="openTaskModalButton">
            <i class="fas fa-plus"></i> Nueva Tarea
        </button>
    </div>
    <div class="card-body">
        <ul class="todo-list list-group" data-widget="todo-list">
            <?php foreach ($todos as $todo): ?>
            <li id="task-<?= $todo['id'] ?>" class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="handle me-2">
                        <i class="fas fa-ellipsis-v"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div class="form-check icheck-primary d-inline me-2">
                        <input type="checkbox" class="form-check-input toggle-complete" id="todoCheck<?= $todo['id'] ?>"
                            data-id="<?= $todo['id'] ?>" <?= $todo['is_completed'] ? 'checked' : '' ?>>
                        <label for="todoCheck<?= $todo['id'] ?>"></label>
                    </div>
                    <!-- Aplica la clase 'text-decoration-line-through' si la tarea está completada -->
                    <span class="text <?= $todo['is_completed'] ? 'text-decoration-line-through' : '' ?>">
                        <?= esc($todo['text']) ?>
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <small class="badge bg-<?= $todo['priority'] ?> me-2">
                        <i class="far fa-clock"></i> <?= esc($todo['due_time']) ?>
                    </small>
                    <div class="tools">
                        <i class="fas fa-edit edit-task me-2" data-id="<?= $todo['id'] ?>" role="button"></i>
                        <i class="fas fa-trash delete-task" data-id="<?= $todo['id'] ?>" role="button"></i>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

    </div>
</div>

<!-- Modal para agregar/editar tareas -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <input type="hidden" name="id" id="taskId">
                    <div class="mb-3">
                        <label for="taskText" class="form-label">Tarea</label>
                        <input type="text" name="text" id="taskText" class="form-control" placeholder="Task" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskPriority" class="form-label">Prioridad</label>
                        <select name="priority" id="taskPriority" class="form-select">
                            <option value="info">Normal</option>
                            <option value="danger">Urgente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskDueTime" class="form-label">Hora de Vencimiento</label>
                        <input type="text" name="due_time" id="taskDueTime" class="form-control timepicker"
                            placeholder="Due time">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveTaskButton">Guardar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<!-- Incluir la librería toastr y timepicker -->
<?= $this->section("js") ?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
$(document).ready(function() {
    // Abrir el modal en modo "Agregar" o "Editar"
    $('#openTaskModalButton, .edit-task').click(function() {
        var isEditMode = $(this).hasClass('edit-task');
        $('#taskModalLabel').text(isEditMode ? 'Editar Tarea' : 'Nueva Tarea');
        $('#taskForm')[0].reset();
        if (isEditMode) {
            var taskItem = $(this).closest('li');
            $('#taskId').val($(this).data('id'));
            $('#taskText').val(taskItem.find('.text').text());
            $('#taskPriority').val(taskItem.find('.badge').hasClass('bg-danger') ? 'danger' : 'info');
            $('#taskDueTime').val(taskItem.find('.badge').text().trim());
        } else {
            $('#taskId').val('');
        }
        $('#saveTaskButton').data('action', isEditMode ? 'edit' : 'add');
        $('#taskModal').modal('show');
    });

    // Manejar la acción de guardar (tanto para agregar como para editar)
    $('#saveTaskButton').click(function() {
        var action = $(this).data('action');
        var taskId = $('#taskId').val();
        var url = action === 'add' ? '<?= base_url('todo/add') ?>' : '<?= base_url('todo/edit') ?>/' +
            taskId;

        $.post(url, $('#taskForm').serialize(), function(response) {
            if (response.status === 'success') {
                if (action === 'add') {
                    // Agregar la nueva tarea a la lista
                    $('.todo-list').append(
                        '<li id="task-' + response.data.id +
                        '" class="list-group-item d-flex justify-content-between align-items-center">' +
                        '<div class="d-flex align-items-center">' +
                        '<span class="handle me-2">' +
                        '<i class="fas fa-ellipsis-v"></i>' +
                        '<i class="fas fa-ellipsis-v"></i>' +
                        '</span>' +
                        '<div class="form-check icheck-primary d-inline me-2">' +
                        '<input type="checkbox" id="todoCheck' + response.data.id +
                        '" data-id="' + response.data.id +
                        '" class="form-check-input toggle-complete">' +
                        '<label for="todoCheck' + response.data.id + '"></label>' +
                        '</div>' +
                        '<span class="text">' + response.data.text + '</span>' +
                        '</div>' +
                        '<div class="d-flex align-items-center">' +
                        '<small class="badge bg-' + response.data.priority +
                        ' me-2"><i class="far fa-clock"></i> ' + response.data.due_time +
                        '</small>' +
                        '<div class="tools">' +
                        '<i class="fas fa-edit edit-task me-2" data-id="' + response.data
                        .id + '" role="button"></i>' +
                        '<i class="fas fa-trash delete-task" data-id="' + response.data.id +
                        '" role="button"></i>' +
                        '</div>' +
                        '</div>' +
                        '</li>'
                    );
                } else {
                    // Actualizar la tarea en la lista
                    var taskItem = $('#task-' + response.data.id);
                    taskItem.find('.text').text(response.data.text);
                    taskItem.find('.badge')
                        .removeClass('bg-info bg-danger')
                        .addClass('bg-' + response.data.priority)
                        .text(response.data.due_time);
                }

                // Cerrar el modal
                $('#taskModal').modal('hide');
            } else {
                alert('No se pudo guardar la tarea');
            }
        }, 'json');
    });

    // Manejar el evento de clic para eliminar una tarea con SweetAlert2
    $(document).on('click', '.delete-task', function(e) {
        e.preventDefault();
        var taskId = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '¡Sí, bórrelo!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('<?= base_url('todo/delete') ?>/' + taskId, function(response) {
                    if (response.status === 'success') {
                        $('#task-' + taskId).remove();
                        Swal.fire('¡Eliminado!', 'Tu tarea ha sido eliminada.',
                            'success');
                    } else {
                        Swal.fire('¡Error!', 'No se pudo eliminar la tarea.', 'error');
                    }
                }, 'json');
            }
        });
    });

    // Manejar el evento de clic para marcar como completada una tarea
    $(document).on('click', '.toggle-complete', function() {
        var taskId = $(this).data('id');
        var taskItem = $('#task-' + taskId);
        $.get('<?= base_url('todo/toggleComplete') ?>/' + taskId, function(response) {
            if (response.status === 'success') {
                $('#todoCheck' + taskId).prop('checked', response.data.is_completed);
                // Agregar o quitar la clase que tacha el texto según el estado completado
                taskItem.find('.text').toggleClass('text-decoration-line-through', response.data
                    .is_completed);
            } else {
                alert('No se pudo actualizar el estado de la tarea');
            }
        }, 'json');
    });

    // Iniciar el timepicker
    $('input.timepicker').timepicker({});
});
</script>

<?= $this->endSection() ?>