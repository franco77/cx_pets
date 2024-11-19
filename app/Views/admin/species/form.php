<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" value="<?= !empty($data_species['name']) ? $data_species['name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" ><?= !empty($data_species['description']) ? $data_species['description'] : '' ?></textarea>
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>