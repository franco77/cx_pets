<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" value="<?= !empty($data_vendors['name']) ? $data_vendors['name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <input type="text" name="description" value="<?= !empty($data_vendors['description']) ? $data_vendors['description'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="created_at" class="form-label">Created At</label>
      <input type="text" name="created_at" value="<?= !empty($data_vendors['created_at']) ? $data_vendors['created_at'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>