<form id="form" enctype="multipart/form-data" accept-charset="utf-8">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" value="<?= !empty($data_incomes['title']) ? $data_incomes['title'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label">Amount</label>
        <input type="text" name="amount" value="<?= !empty($data_incomes['amount']) ? $data_incomes['amount'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="support" class="form-label">Support</label>
        <input type="file" name="support" class="form-control" id="support" />
    </div>

    <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea name="notes" rows="7"
            class="form-control"><?= !empty($data_incomes['notes']) ? $data_incomes['notes'] : '' ?></textarea>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>