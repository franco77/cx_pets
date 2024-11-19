<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="pet_tests_id" class="form-label">Pet Tests Id</label>
      <select name="pet_tests_id" class="custom-select">
         <?php foreach($data_pet_tests as $pet_tests => $pet_test): ?>
         <option value="<?= $pet_test['id'] ?>" <?= !empty($data_test_details['pet_tests_id']) && $data_test_details['pet_tests_id'] == $pet_test['id'] ? 'selected' : '' ?>><?= $pet_test['id'] ?></option>
         <?php endforeach ?>
      </select>
   </div>
   <div class="mb-3">
      <label for="test_id" class="form-label">Test Id</label>
      <select name="test_id" class="custom-select">
         <?php foreach($data_tests as $tests => $test): ?>
         <option value="<?= $test['id'] ?>" <?= !empty($data_test_details['test_id']) && $data_test_details['test_id'] == $test['id'] ? 'selected' : '' ?>><?= $test['id'] ?></option>
         <?php endforeach ?>
      </select>
   </div>
   <div class="mb-3">
      <label for="notes" class="form-label">Notes</label>
      <input type="text" name="notes" value="<?= !empty($data_test_details['notes']) ? $data_test_details['notes'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="result" class="form-label">Result</label>
      <input type="text" name="result" value="<?= !empty($data_test_details['result']) ? $data_test_details['result'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>