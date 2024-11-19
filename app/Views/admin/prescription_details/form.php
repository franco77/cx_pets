<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="prescription_id" class="form-label">Prescription Id</label>
      <select name="prescription_id" class="custom-select">
         <?php foreach($data_prescriptions as $prescriptions => $prescription): ?>
         <option value="<?= $prescription['id'] ?>" <?= !empty($data_prescription_details['prescription_id']) && $data_prescription_details['prescription_id'] == $prescription['id'] ? 'selected' : '' ?>><?= $prescription['id'] ?></option>
         <?php endforeach ?>
      </select>
   </div>
   <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" name="product_name" value="<?= !empty($data_prescription_details['product_name']) ? $data_prescription_details['product_name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="dosage" class="form-label">Dosage</label>
      <input type="text" name="dosage" value="<?= !empty($data_prescription_details['dosage']) ? $data_prescription_details['dosage'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="frequency" class="form-label">Frequency</label>
      <input type="text" name="frequency" value="<?= !empty($data_prescription_details['frequency']) ? $data_prescription_details['frequency'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>