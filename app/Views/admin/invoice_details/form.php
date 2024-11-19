<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="invoice_id" class="form-label">Invoice Id</label>
      <select name="invoice_id" class="custom-select">
         <?php foreach($data_invoices as $invoices => $invoice): ?>
         <option value="<?= $invoice['id'] ?>" <?= !empty($data_invoice_details['invoice_id']) && $data_invoice_details['invoice_id'] == $invoice['id'] ? 'selected' : '' ?>><?= $invoice['id'] ?></option>
         <?php endforeach ?>
      </select>
   </div>
   <div class="mb-3">
      <label for="product_id" class="form-label">Product Id</label>
      <input type="text" name="product_id" value="<?= !empty($data_invoice_details['product_id']) ? $data_invoice_details['product_id'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" name="product_name" value="<?= !empty($data_invoice_details['product_name']) ? $data_invoice_details['product_name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="quantity" class="form-label">Quantity</label>
      <input type="text" name="quantity" value="<?= !empty($data_invoice_details['quantity']) ? $data_invoice_details['quantity'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="price" class="form-label">Price</label>
      <input type="text" name="price" value="<?= !empty($data_invoice_details['price']) ? $data_invoice_details['price'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>