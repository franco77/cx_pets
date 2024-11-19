<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="code" class="form-label">Code</label>
      <input type="text" name="code" value="<?= !empty($data_tests['code']) ? $data_tests['code'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" value="<?= !empty($data_tests['name']) ? $data_tests['name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" ><?= !empty($data_tests['description']) ? $data_tests['description'] : '' ?></textarea>
   </div>
   <div class="mb-3">
      <label for="created_at" class="form-label">Created At</label>
      <input type="text" name="created_at" value="<?= !empty($data_tests['created_at']) ? $data_tests['created_at'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="updated_at" class="form-label">Updated At</label>
      <input type="text" name="updated_at" value="<?= !empty($data_tests['updated_at']) ? $data_tests['updated_at'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>