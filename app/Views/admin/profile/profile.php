<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>
<style>
    .avatar-container {
        width: 150px;
        height: 150px;
        position: relative;
        cursor: pointer;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        transition: opacity 0.3s ease;
    }

    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .avatar-text {
        font-size: 16px;
        font-weight: bold;
    }

    .avatar-container:hover .avatar-overlay {
        opacity: 1;
    }

    .avatar-container:hover .avatar-img {
        opacity: 0.7;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="card-title mb-0"><?= $title; ?></h5>
                </div>



                <div class="container rounded bg-white mt-5 mb-5">
                    <div class="row">
                        <!-- Sección de perfil del usuario -->
                        <div class="col-md-3 border-right">
                            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                                <div class="avatar-container" style="position: relative;">
                                    <img id="avatarPreview" class="rounded-circle avatar-img" width="150px"
                                        src="<?= base_url('uploads/avatars/' . $user->avatar); ?>" alt="Avatar">
                                    <div class="avatar-overlay">
                                        <span class="avatar-text">Cambiar</span>
                                    </div>
                                </div>
                                <form id="avatarForm" enctype="multipart/form-data" style="display:none;">
                                    <input type="file" name="avatar" id="avatar" accept="image/*">
                                    <input type="hidden" name="user_id" id="user_id" value="<?= $user->id; ?>">
                                </form>
                                <br>
                                <span class="font-weight-bold" id="displayUsername">
                                    <?= logged('first_name') . ' ' . logged('last_name'); ?>
                                </span>
                                <span class="text-black-50" id="displayEmail"><?= logged('email'); ?></span>
                            </div>
                        </div>


                        <!-- Sección para editar perfil -->
                        <div class="col-md-7 border-right">
                            <div class="p-3 py-5">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="text-right">Configuración de Perfil</h4>
                                </div>

                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                                <?php endif; ?>

                                <?php if (session()->getFlashdata('message')): ?>
                                    <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                                <?php endif; ?>
                                <form action="<?= base_url('profile/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="labels">Nombre</label>
                                            <input type="text"
                                                class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>"
                                                name="first_name" id="first_name" placeholder="Nombre"
                                                value="<?= old('first_name', $user->first_name) ?>">
                                            <?php if (session('errors.first_name')): ?>
                                                <div class="text-danger">
                                                    <?= session('errors.first_name') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="labels">Apellido</label>
                                            <input type="text"
                                                class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>"
                                                name="last_name" id="last_name" placeholder="Apellido"
                                                value="<?= old('last_name', $user->last_name) ?>">
                                            <?php if (session('errors.last_name')): ?>
                                                <div class="text-danger">
                                                    <?= session('errors.last_name') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="col-md-12">
                                            <label class="labels">Nombre de Usuario</label>
                                            <input type="text"
                                                class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
                                                name="username" id="username" placeholder="Nombre de Usuario"
                                                value="<?= old('username', $user->username) ?>">
                                            <?php if (session('errors.username')): ?>
                                                <div class="text-danger">
                                                    <?= session('errors.username') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="col-md-12">
                                            <label class="labels">Correo Electrónico</label>
                                            <input type="email"
                                                class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                                name="email" id="email" placeholder="Correo Electrónico"
                                                value="<?= old('email', $user->email) ?>">
                                            <?php if (session('errors.email')): ?>
                                                <div class="text-danger">
                                                    <?= session('errors.email') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="col-md-12">
                                            <label class="labels">Teléfono</label>
                                            <input type="text"
                                                class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>"
                                                name="phone" id="phone" placeholder="Teléfono"
                                                value="<?= old('phone', $user->phone) ?>">
                                            <?php if (session('errors.phone')): ?>
                                                <div class="text-danger">
                                                    <?= session('errors.phone') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="col-md-12">
                                            <label class="labels">Compañía</label>
                                            <input type="text"
                                                class="form-control <?= session('errors.company') ? 'is-invalid' : '' ?>"
                                                name="company" id="company" placeholder="Compañía"
                                                value="<?= old('company', $user->company) ?>">
                                            <?php if (session('errors.company')): ?>
                                                <div class="text-danger">
                                                    <?= session('errors.company') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <button class="btn btn-primary profile-button" type="submit">Guardar
                                            Perfil</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
<?= $this->section('js') ?>
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {

        $('.avatar-container').on('click', function() {
            $('#avatar').trigger('click');
        });

        $('#avatar').on('change', function() {
            var formData = new FormData($('#avatarForm')[0]);

            $.ajax({
                url: '<?= site_url('profile/updateAvatar'); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {

                        $('#avatarPreview').attr('src', response.new_image_url);

                        toastr.success('Avatar actualizado correctamente.');
                    } else {

                        toastr.error(response.message || 'Error al actualizar el avatar.');
                    }
                },
                error: function() {
                    toastr.error('Error al procesar la solicitud. Intenta de nuevo.');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>