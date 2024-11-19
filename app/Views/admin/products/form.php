<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="productVendor" class="form-label">Proveedor</label>
      <select name="productVendor" class="select2">
         <option value="">Seleccione una opción</option>
         <?php foreach ($data_vendors as $vendors): ?>
            <option value="<?= $vendors['name'] ?>"
               <?= !empty($data_products['productVendor']) && $data_products['productVendor'] == $vendors['name'] ? 'selected' : '' ?>>
               <?= $vendors['name'] ?></option>
         <?php endforeach ?>
      </select>
   </div>



   <div class="mb-3">
      <label for="productName" class="form-label">ProductName</label>
      <input type="text" name="productName"
         value="<?= !empty($data_products['productName']) ? $data_products['productName'] : '' ?>"
         class="form-control" />
   </div>
   <div class="mb-3">
      <label for="productLine" class="form-label">ProductLine</label>
      <input type="text" name="productLine"
         value="<?= !empty($data_products['productLine']) ? $data_products['productLine'] : '' ?>"
         class="form-control" />
   </div>

   <div class="row">
      <div class="col">
         <div class="mb-3">
            <label for="quantityInStock" class="form-label">QuantityInStock</label>
            <input type="text" name="quantityInStock"
               value="<?= !empty($data_products['quantityInStock']) ? $data_products['quantityInStock'] : '' ?>"
               class="form-control" />
         </div>
      </div>
      <div class="col">
         <div class="mb-3">
            <label for="buyPrice" class="form-label">BuyPrice</label>
            <input type="text" name="buyPrice"
               value="<?= !empty($data_products['buyPrice']) ? $data_products['buyPrice'] : '' ?>"
               class="form-control" />
         </div>
      </div>
   </div>




   <div class="mb-3">
      <label for="productDescription" class="form-label">ProductDescription</label>
      <textarea name="productDescription" id="summernote"
         class="form-control"><?= !empty($data_products['productDescription']) ? $data_products['productDescription'] : '' ?></textarea>
   </div>

   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>