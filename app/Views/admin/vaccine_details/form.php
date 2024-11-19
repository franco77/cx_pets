<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="vaccine_id" class="form-label">Vaccine Id</label>
      <select name="vaccine_id" class="custom-select">
         <?php foreach($data_vaccines as $vaccines => $vaccine): ?>
         <option value="<?= $vaccine['id'] ?>" <?= !empty($data_vaccine_details['vaccine_id']) && $data_vaccine_details['vaccine_id'] == $vaccine['id'] ? 'selected' : '' ?>><?= $vaccine['id'] ?></option>
         <?php endforeach ?>
      </select>
   </div>
   <div class="mb-3">
      <label for="product_id" class="form-label">Product Id</label>
      <input type="text" name="product_id" value="<?= !empty($data_vaccine_details['product_id']) ? $data_vaccine_details['product_id'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" name="product_name" value="<?= !empty($data_vaccine_details['product_name']) ? $data_vaccine_details['product_name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="dosage" class="form-label">Dosage</label>
      <input type="text" name="dosage" value="<?= !empty($data_vaccine_details['dosage']) ? $data_vaccine_details['dosage'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>