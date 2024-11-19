<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="pet_id" class="form-label">Pet</label>
      <select name="pet_id" class="select2">
         <option value="">Selecciona Una Mascota</option>
         <?php foreach ($data_pets as $pets => $pet): ?>
            <option value="<?= $pet['id'] ?>"
               <?= !empty($data_vaccines['pet_id']) && $data_vaccines['pet_id'] == $pet['id'] ? 'selected' : '' ?>>
               <?= $pet['name'] ?></option>
         <?php endforeach ?>
      </select>
      <div id="pet-owner-info">
         <!-- Aquí se mostrará la información de la mascota y del propietario -->
      </div>
   </div>
   <div class="mb-3">
      <label for="vaccine_date" class="form-label">Vaccine Date</label>
      <input type="text" name="vaccine_date"
         value="<?= !empty($data_vaccines['vaccine_date']) ? $data_vaccines['vaccine_date'] : '' ?>"
         class="form-control datetime" id="vaccine_date" data-toggle="datetimepicker" data-target="#vaccine_date" />
   </div>



   <div class='row'>
      <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
         <table class="table table-bordered table-hover" id="invoiceTable">
            <thead>
               <tr>
                  <th width="2%"><input id="check_all" class="formcontrol" type="checkbox" /></th>
                  <th width="15%">SKU</th>
                  <th width="38%">Medication</th>
                  <th width="15%">Dosage</th>
               </tr>
            </thead>
            <tbody>
               <?php if (!empty($vaccine_details)): ?>
                  <!-- Pre-cargar los detalles de la prescripción en modo de edición -->
                  <?php foreach ($vaccine_details as $key => $detail): ?>
                     <tr>
                        <td><input class="form-check-input case" type="checkbox" /></td>
                        <td><input type="text" data-type="productCode" name="product_id[]" id="itemNo_<?= $key + 1 ?>"
                              class="form-control autocomplete_txt" value="<?= $detail['product_id'] ?>"
                              autocomplete="off"></td>
                        <td><input type="text" data-type="productName" name="product_name[]"
                              id="itemName_<?= $key + 1 ?>" class="form-control autocomplete_txt"
                              value="<?= $detail['product_name'] ?>" autocomplete="off"></td>
                        <td><input type="text" name="dosage[]" value="<?= $detail['dosage'] ?>"
                              class="form-control changesNo" id="dosage_<?= $key + 1 ?>" autocomplete="off"></td>
                     </tr>
                  <?php endforeach; ?>
               <?php else: ?>
                  <!-- En modo de creación de una nueva prescripción -->
                  <tr>
                     <td><input class="form-check-input case" type="checkbox" /></td>
                     <td><input type="text" data-type="productCode" name="product_id[]" id="itemNo_1"
                           class="form-control autocomplete_txt" autocomplete="off"></td>
                     <td><input type="text" data-type="productName" name="product_name[]" id="itemName_1"
                           class="form-control autocomplete_txt" autocomplete="off"></td>
                     <td><input type="text" name="dosage[]" class="form-control changesNo" autocomplete="off"></td>
                     </td>
                  </tr>
               <?php endif; ?>
            </tbody>
         </table>
      </div>
   </div>

   <!-- Botones para agregar/eliminar filas dinámicas -->
   <div class='row'>
      <div class='col-xs-12 col-sm-6 col-md-6 col-lg-6'>
         <button id="delete" class="btn btn-danger delete" type="button">- Delete</button>
         <button id="addmore" class="btn btn-success addmore" type="button">+ Add More</button>
      </div>
   </div>

   <br />
   <hr>





   <div class="mb-3">
      <label for="notes" class="form-label">Notes</label>
      <textarea name="notes" id="summernote"
         class="form-control"><?= !empty($data_vaccines['notes']) ? $data_vaccines['notes'] : '' ?></textarea>
   </div>

   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>





<script>
   //adds extra table rows
   $(document).ready(function() {
      var i = <?= !empty($vaccine_details) ? count($vaccine_details) + 1 : 2 ?>; // Contador dinámico

      // Agregar nuevas filas
      $("#addmore").on('click', function() {
         html = '<tr>';
         html += '<td><input class="case" type="checkbox"/></td>';
         html += '<td><input type="text" data-type="productCode" name="product_id[]" id="itemNo_' + i +
            '" class="form-control autocomplete_txt" autocomplete="off"></td>';
         html += '<td><input type="text" data-type="productName" name="product_name[]" id="itemName_' +
            i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';

         html += '<td><input type="text" name="dosage[]" id="dosage_' + i +
            '" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';
         html += '</tr>';
         $('table#invoiceTable').append(html);
         i++;
      });

      //to check all checkboxes
      $(document).on('change', '#check_all', function() {
         $('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
      });

      //deletes the selected table rows
      $("#delete").on('click', function() {
         $('.case:checkbox:checked').parents("tr").remove();
         $('#check_all').prop("checked", false);
      });

      $('#summernote').summernote({
         height: 250
      });

   });


   $(document).ready(function() {
      // Capturar el valor del `pet_id` si ya está seleccionado (en modo de edición)
      var initialPetId = $('select[name="pet_id"]').val();

      // Si existe un `pet_id` (modo de edición), cargar la información automáticamente
      if (initialPetId) {
         loadPetOwnerInfo(initialPetId);
      }

      // Capturar el evento 'change' del select de mascotas para cargar una nueva selección
      $('select[name="pet_id"]').change(function() {
         var petId = $(this).val();

         // Limpiar el div si no se selecciona ninguna mascota
         if (petId == "") {
            $('#pet-owner-info').html('');
            return;
         }

         // Cargar la información de la mascota y el propietario
         loadPetOwnerInfo(petId);
      });

      // Función para cargar la información del propietario y la mascota vía AJAX
      function loadPetOwnerInfo(petId) {
         $.ajax({
            url: '<?= base_url('pets/getPetDetails') ?>/' + petId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
               if (response.pet && response.owner) {
                  var petInfo = `
                        <br />
                        <div class="row">
                            <div class="col">
                                <table class="table table-sm activate-select dt-responsive nowrap w-100">
                                   
                                    <tbody>
                                        <tr>
                                            <td><strong>Nombre</strong></td>
                                            <td>${response.pet.pName}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Edad</strong></td>
                                            <td>${response.pet.age} años</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sexo</strong></td>
                                            <td>${response.pet.sex}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Peso</strong></td>
                                            <td>${response.pet.weight} ${response.pet.weight_unit}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Especie</strong></td>
                                            <td>${response.pet.spName}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Raza</strong></td>
                                            <td>${response.pet.bname}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <table class="table table-sm activate-select dt-responsive nowrap w-100">
                                <tbody>
                                        <tr>
                                            <td><strong>Identificación</strong></td>
                                            <td>${response.owner.ic}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre</strong></td>
                                            <td>${response.owner.first_name} ${response.owner.last_name}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td>${response.owner.email}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Teléfono</strong></td>
                                            <td>${response.owner.phone}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dirección</strong></td>
                                            <td>${response.owner.address}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Creado el</strong></td>
                                            <td>${response.owner.created_at}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;

                  // Mostrar la información en el div
                  $('#pet-owner-info').html(petInfo);
               } else {
                  $('#pet-owner-info').html(
                     '<p>No se encontró la información de la mascota o el propietario.</p>');
               }
            },
            error: function() {
               $('#pet-owner-info').html(
                  '<p>Ocurrió un error al obtener la información de la mascota.</p>');
            }
         });
      }
   });


   //autocomplete script
   $(document).on('focus', '.autocomplete_txt', function() {
      type = $(this).data('type');

      if (type == 'productCode') autoTypeNo = 0;
      if (type == 'productName') autoTypeNo = 1;

      $(this).autocomplete({
         source: function(request, response) {
            $.ajax({
               url: '<?= base_url('products/autocomplete') ?>', // Ruta a tu controlador
               dataType: "json",
               method: 'post',
               data: {
                  name_startsWith: request.term,
                  type: type
               },
               success: function(data) {
                  response($.map(data, function(item) {
                     var code = item.split("|");
                     return {
                        label: code[autoTypeNo],
                        value: code[autoTypeNo],
                        data: item
                     }
                  }));
               }
            });
         },
         autoFocus: true,
         minLength: 0,
         appendTo: ".modal-form",
         select: function(event, ui) {
            var names = ui.item.data.split("|");
            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            $('#itemNo_' + id[1]).val(names[0]);
            $('#itemName_' + id[1]).val(names[1]);
         }
      });
   });
</script>