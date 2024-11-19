<form id="form" accept-charset="utf-8">

    <div class="mb-3">
        <label for="pet_id" class="form-label">Pet</label>
        <select name="pet_id" class="select2">
            <option value="">Selecciona una mascota</option>
            <?php foreach ($data_pets as $pets => $pet): ?>
                <option value="<?= $pet['id'] ?>"
                    <?= !empty($data_pet_tests['pet_id']) && $data_pet_tests['pet_id'] == $pet['id'] ? 'selected' : '' ?>>
                    <?= $pet['name'] ?></option>
            <?php endforeach ?>
        </select>
        <div id="pet-owner-info">
            <!-- Aquí se mostrará la información de la mascota y del propietario -->
        </div>
    </div>



    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="test_date" class="form-label">Test Date</label>
                <input type="text" name="test_date"
                    value="<?= !empty($data_pet_tests['test_date']) ? $data_pet_tests['test_date'] : '' ?>"
                    class="form-control" id="test_date" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table table-sm activate-select dt-responsive nowrap w-100" id="invoiceTable">
                <thead>
                    <tr>
                        <th width="2%"><input id="check_all" class="form-check-input" type="checkbox" /></th>
                        <th width="12%">Code</th>
                        <th width="30%">Test Name</th>
                        <th width="30%">Notes</th>
                        <th width="15%">Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($test_details)): ?>
                        <?php foreach ($test_details as $key => $detail): ?>
                            <input type="hidden" name="detail_id[]" value="<?= $detail['id'] ?>">
                            <tr>
                                <td><input class="form-check-input case" type="checkbox" /></td>
                                <td><input type="text" data-type="code" name="code[]" id="itemNo_<?= $key + 1 ?>"
                                        class="form-control autocomplete_txt" value="<?= $detail['code'] ?>" autocomplete="off">
                                </td>
                                <td><input type="text" data-type="name" name="name[]" id="itemName_<?= $key + 1 ?>"
                                        class="form-control autocomplete_txt" value="<?= $detail['name'] ?>" autocomplete="off">
                                </td>
                                <td><input type="text" name="notes[]" id="notes_<?= $key + 1 ?>"
                                        value="<?= isset($detail['notes']) ? htmlspecialchars($detail['notes'], ENT_QUOTES, 'UTF-8') : '' ?>"
                                        class="form-control" autocomplete="off">

                                </td>
                                <td><input type="text" name="result[]" id="result_<?= $key + 1 ?>" class="form-control"
                                        value="<?= $detail['result'] ?>" autocomplete="off"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td><input class="form-check-input case" type="checkbox" /></td>
                            <td><input type="text" data-type="code" name="code[]" id="itemNo_1"
                                    class="form-control autocomplete_txt" autocomplete="off"></td>
                            <td><input type="text" data-type="name" name="name[]" id="itemName_1"
                                    class="form-control autocomplete_txt" autocomplete="off"></td>
                            <td><input type="text" name="notes[]" id="notes_1" class="form-control" autocomplete="off"></td>
                            <td><input type="text" name="result[]" id="result_1" class="form-control" autocomplete="off">
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class='row'>
        <div class='col-xs-12 col-sm-6 col-md-6 col-lg-6'>
            <button id="delete" class="btn btn-danger delete" type="button">- Delete</button>
            <button id="addmore" class="btn btn-success addmore" type="button">+ Add More</button>
        </div>
    </div>


    <br />

    <div class="mb-3">
        <label for="notes_test" class="form-label">Notas</label>
        <textarea name="notes_test" id="summernote"
            class="form-control"><?= !empty($data_pet_tests['notes_test']) ? $data_pet_tests['notes_test'] : '' ?></textarea>
    </div>





    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>

<script>
    $(document).ready(function() {
        var i = <?= !empty($prescription_details) ? count($prescription_details) + 1 : 2 ?>; // Contador dinámico

        // Agregar nuevas filas
        $("#addmore").on('click', function() {
            html = '<tr>';
            html += '<td><input class="case" type="checkbox"/></td>';
            html += '<td><input type="text" data-type="code" name="code[]" id="itemNo_' + i +
                '" class="form-control autocomplete_txt" autocomplete="off"></td>';
            html += '<td><input type="text" data-type="name" name="name[]" id="itemName_' +
                i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';

            html += '<td><input type="text"  name="notes[]" id="notes_' + i +
                '" class="form-control" autocomplete="off"></td>';
            html += '<td><input type="text" name="result[]" id="result_' + i +
                '" class="form-control" autocomplete="off"></td>';
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
                                    <thead class="thead-dark">
                                        <tr>
                                            <th colspan="2" class="text-center">Información de la Mascota</th>
                                        </tr>
                                    </thead>
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
                                    <thead class="thead-dark">
                                        <tr>
                                            <th colspan="2" class="text-center">Información del Propietario</th>
                                        </tr>
                                    </thead>
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

        if (type == 'code') autoTypeNo = 0;
        if (type == 'name') autoTypeNo = 1;

        $(this).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '<?= base_url('tests/autocomplete') ?>', // Ruta a tu controlador
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