<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="id_specie" class="form-label">Specie</label>
        <select name="id_specie" class="select2">
            <?php foreach ($data_species as $specie): ?>
            <option value="<?= $specie['id'] ?>"
                <?= !empty($data_breeds['id_specie']) && $data_breeds['id_specie'] == $specie['id'] ? 'selected' : '' ?>>
                <?= $specie['name'] ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" value="<?= !empty($data_breeds['name']) ? $data_breeds['name'] : '' ?>"
            class="form-control" />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>