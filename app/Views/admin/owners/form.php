<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="ic" class="form-label">Identification</label>
        <input type="text" name="ic" value="<?= !empty($data_owners['ic']) ? $data_owners['ic'] : '' ?>"
            class="form-control" />
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name"
                    value="<?= !empty($data_owners['first_name']) ? $data_owners['first_name'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name"
                    value="<?= !empty($data_owners['last_name']) ? $data_owners['last_name'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" name="email"
                    value="<?= !empty($data_owners['email']) ? $data_owners['email'] : '' ?>" class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone"
                    value="<?= !empty($data_owners['phone']) ? $data_owners['phone'] : '' ?>" class="form-control" />
            </div>
        </div>
    </div>


    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" name="address" value="<?= !empty($data_owners['address']) ? $data_owners['address'] : '' ?>"
            class="form-control" />
    </div>

    <div class="mb-3">
        <label for="profile" class="form-label">Profile</label>
        <textarea name="profile" rows="5"
            class="form-control"><?= !empty($data_owners['profile']) ? $data_owners['profile'] : '' ?></textarea>
    </div>

    <input type="hidden" name="id" value="<?= !empty($data_owners['id']) ? $data_owners['id'] : '' ?>">

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>