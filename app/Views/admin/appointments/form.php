<style>
   .class-time-red {
      background-color: #ff4d4d !important;
      color: white;
   }

   .class-time-red[disabled] {
      opacity: 0.5;
   }
</style>
<form id="form" accept-charset="utf-8">
   <div class="row">
      <div class="col">
         <div class="mb-3">
            <label for="owner_id" class="form-label">Owner</label>
            <select name="owner_id" class="select2" id="owner_id">
               <option value="">Select Owner</option>
               <?php foreach ($data_owners as $owner): ?>
                  <option value="<?= $owner['id'] ?>"
                     <?= !empty($data_appointments['owner_id']) && $data_appointments['owner_id'] == $owner['id'] ? 'selected' : '' ?>>
                     <?= $owner['first_name'] . ' ' . $owner['last_name'] ?> - <?= $owner['ic'] ?></option>
               <?php endforeach ?>
            </select>
         </div>
      </div>
      <div class="col">
         <div class="mb-3">
            <label for="pet_id" class="form-label">Pets</label>
            <select name="pet_id" class="select2" id="pet_id">
               <option value="">Select Pet</option>
               <!-- Las mascotas se cargarán dinámicamente aquí -->
            </select>
         </div>

      </div>
   </div>

   <!-- Selector de fecha -->
   <div class="mb-3">
      <label for="appointment_date" class="form-label">Appointment Date</label>
      <input type="text" name="appointment_date" class="form-control" id="appointment_date" autocomplete="off"
         placeholder="Click to select a date" />
   </div>

   <!-- Selector de hora -->
   <div class="mb-3">
      <label for="appointment_time" class="form-label">Available Times</label>
      <select name="appointment_time" class="form-select" id="appointment_time" required>
         <option value="">Select Time</option>
         <!-- Las horas disponibles se llenarán dinámicamente -->
      </select>
   </div>

   <div class="mb-3">
      <label for="reason" class="form-label">Reason</label>
      <textarea name="reason" class="form-control" rows="7"
         required><?= !empty($data_appointments['reason']) ? $data_appointments['reason'] : '' ?></textarea>
   </div>

   <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status1" name="status" value="pending"
            <?= !empty($data_appointments['status']) && $data_appointments['status'] == 'pending' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status1">Pending</label>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status2" name="status" value="confirmed"
            <?= !empty($data_appointments['status']) && $data_appointments['status'] == 'confirmed' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status2">Confirmed</label>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status3" name="status" value="completed"
            <?= !empty($data_appointments['status']) && $data_appointments['status'] == 'completed' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status3">Completed</label>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status4" name="status" value="cancelled"
            <?= !empty($data_appointments['status']) && $data_appointments['status'] == 'cancelled' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status4">Cancelled</label>
      </div>
   </div>

   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
   </div>
</form>



<script>
   $(function() {
      // Inicializar el datepicker para seleccionar la fecha
      $('#appointment_date').datepicker({
         dateFormat: 'yy-mm-dd',
         changeMonth: true,
         changeYear: true,
         minDate: 0,
         beforeShowDay: function(date) {
            var day = date.getDay();
            return [(day != 0 && day != 6), ''];
         },
         onSelect: function(dateText) {
            // Limpiar el selector de horas
            let timeSelect = $('#appointment_time');
            timeSelect.empty(); // Limpiar las opciones anteriores
            timeSelect.append('<option value="">Select Time</option>'); // Opción predeterminada

            // Obtener las horas ocupadas y disponibles para la fecha seleccionada
            $.getJSON('<?= base_url('appointments/getAvailableTimes') ?>/' + dateText, function(
               times) {
               // Recorrer todas las horas y crear las opciones en el select
               $.each(times, function(index, timeObj) {
                  let option = $('<option></option>').val(timeObj.time).text(
                     timeObj.time);

                  // Si la hora está ocupada, la deshabilitamos y la marcamos en rojo
                  if (timeObj.status === 'busy') {
                     option.prop('disabled', true).addClass('class-time-red');
                  }

                  timeSelect.append(option);
               });
            });
         }
      });
   });

   $('#owner_id').on('change', function() {
      var ownerId = $(this).val();

      if (ownerId) {
         // Realizar la solicitud AJAX para obtener las mascotas
         $.ajax({
            url: '<?= base_url('owners/getPetsByOwner') ?>/' +
               ownerId, // Endpoint que devuelve las mascotas
            type: 'GET',
            dataType: 'json',
            success: function(pets) {
               var petSelect = $('#pet_id');
               petSelect.empty(); // Limpiar el select de mascotas
               petSelect.append('<option value="">Select Pet</option>'); // Opción por defecto

               // Rellenar el select con las mascotas recibidas
               $.each(pets, function(index, pet) {
                  petSelect.append('<option value="' + pet.id + '">' + pet.name +
                     '</option>');
               });
            },
            error: function() {
               alert('Error loading pets');
            }
         });
      } else {
         // Si no hay dueño seleccionado, limpiar el select de mascotas
         $('#pet_id').empty();
         $('#pet_id').append('<option value="">Select Pet</option>');
      }
   });
</script>