<form id="form" enctype="multipart/form-data" accept-charset="utf-8">
    <div class="mb-3">
        <label for="owner_id" class="form-label">Propietario</label>
        <select name="owner_id" class="select2">
            <?php foreach ($data_owners as $owners): ?>
                <option value="<?= $owners['id'] ?>"
                    <?= !empty($data_pets['owner_id']) && $data_pets['owner_id'] == $owners['id'] ? 'selected' : '' ?>>
                    <?= $owners['first_name'] . ' ' . $owners['last_name'] ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="specie_id" class="form-label">Specie</label>
                <select name="specie_id" id="specie_id" class="select2">
                    <option value="">Seleccione una especie</option>
                    <?php foreach ($data_species as $specie): ?>
                        <option value="<?= $specie['id'] ?>"
                            <?= !empty($data_pets['specie_id']) && $data_pets['specie_id'] == $specie['id'] ? 'selected' : '' ?>>
                            <?= $specie['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="breed_id" class="form-label">Breed</label>
                <select name="breed_id" id="breed_id" class="select2">
                    <option value="">Seleccione una raza</option>
                    <?php foreach ($data_breeds as $breed): ?>
                        <option value="<?= $breed['id'] ?>"
                            <?= !empty($data_pets['breed_id']) && $data_pets['breed_id'] == $breed['id'] ? 'selected' : '' ?>>
                            <?= $breed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" value="<?= !empty($data_pets['name']) ? $data_pets['name'] : '' ?>"
            class="form-control" />
    </div>

    <div class="mb-3">
        <label for="sex" class="form-label">Sex</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sex1" name="sex" value="male"
                <?= !empty($data_pets['sex']) && $data_pets['sex'] == 'male' ? 'checked' : '' ?> />
            <label class="form-check-label" for="sex1">Male</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sex2" name="sex" value="female"
                <?= !empty($data_pets['sex']) && $data_pets['sex'] == 'female' ? 'checked' : '' ?> />
            <label class="form-check-label" for="sex2">Female</label>
        </div>
    </div>
    <div class="mb-3">
        <label for="age" class="form-label">Age</label>
        <input type="text" name="age" value="<?= !empty($data_pets['age']) ? $data_pets['age'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="weight" class="form-label">Weight</label>
        <div class="input-group">
            <!-- Campo para el valor numérico del peso -->
            <input type="number" step="any" id="weightValue" name="weight"
                value="<?= !empty($data_pets['weight']) ? preg_replace('/[a-zA-Z]/', '', $data_pets['weight']) : '' ?>"
                class="form-control" placeholder="Enter weight" aria-label="Weight" required />

            <!-- Selector para la unidad -->
            <select id="weightUnit" name="weight_unit" class="form-select" aria-label="Weight Unit" required>
                <option value="kg"
                    <?= !empty($data_pets['weight']) && strpos($data_pets['weight'], 'kg') !== false ? 'selected' : '' ?>>
                    Kg</option>
                <option value="g"
                    <?= !empty($data_pets['weight']) && strpos($data_pets['weight'], 'g') !== false ? 'selected' : '' ?>>
                    Gramos (g)</option>
                <option value="lb"
                    <?= !empty($data_pets['weight']) && strpos($data_pets['weight'], 'lb') !== false ? 'selected' : '' ?>>
                    Libras (lb)</option>
            </select>
        </div>
    </div>



    <div class="mb-3">
        <label for="photo" class="form-label">Photo</label>
        <input type="file" name="photo" id="photo" class="form-control">
    </div>


    <div class="mb-3">
        <label for="profile" class="form-label">Profile</label>
        <textarea name="profile" rows="5"
            class="form-control"><?= !empty($data_pets['profile']) ? $data_pets['profile'] : '' ?></textarea>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>
<script src="<?= base_url('assets/admin/js/plugin/jQuery-Mask-Plugin/jquery.mask.js') ?>">
</script>
<script>
    $(document).ready(function() {
        $('.weight').mask("#0.000", {
            reverse: true
        });
    })
</script>
<script>
    $(document).ready(function() {
        // Cuando el usuario seleccione una especie, hacer una solicitud AJAX
        $('#specie_id').on('change', function() {
            var specieId = $(this).val(); // Obtener el ID de la especie seleccionada

            if (specieId) {
                // Realizar una solicitud AJAX
                $.ajax({
                    url: "<?= base_url('pets/getBreedsBySpecie') ?>/" +
                        specieId, // URL a la ruta de CodeIgniter
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        // Limpiar el select de razas
                        $('#breed_id').empty();
                        $('#breed_id').append('<option value="">Seleccione una raza</option>');

                        // Iterar sobre los resultados y agregar las opciones al select
                        $.each(data, function(key, value) {
                            $('#breed_id').append('<option value="' + value.id + '">' +
                                value.name + '</option>');
                        });
                    }
                });
            } else {
                // Si no se selecciona ninguna especie, limpiar el select de razas
                $('#breed_id').empty();
                $('#breed_id').append('<option value="">Seleccione una raza</option>');
            }
        });
    });
</script>