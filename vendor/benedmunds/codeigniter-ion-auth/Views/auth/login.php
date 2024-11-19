<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Log In | Dashtrap - Responsive Bootstrap 5 Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
  <meta content="Myra Studio" name="author" />
  <link rel="stylesheet" href="<?= base_url('assets/admin/css/bootstrap.min.css') ?>" />
  <style>
    .login {
      min-height: 100vh;
    }

    .bg-image {
      background-image: url('https://wallpapers.com/images/hd/coding-background-9izlympnd0ovmpli.jpg');
      background-size: cover;
      background-position: center;
    }

    .login-heading {
      font-weight: 300;
    }

    .btn-login {
      font-size: 0.9rem;
      letter-spacing: 0.05rem;
      padding: 0.75rem 1rem;
    }

    /* Ajustar la imagen para centrado */
    .navbar-brand {
      display: block;
      margin-left: auto;
      margin-right: auto;
      margin-bottom: 20px;
      text-align: center;
    }

    .form-container {
      max-width: 400px;
      margin: 0 auto;
    }
  </style>
</head>

<body>
  <div class="container-fluid ps-md-0">
    <div class="row g-0">
      <div class="d-none d-md-flex col-md-4 col-lg-8 bg-image"></div>
      <div class="col-md-8 col-lg-4">
        <div class="login d-flex align-items-center py-5">
          <div class="container">
            <div class="row">
              <div class="form-container col-md-9 col-lg-8 mx-auto">
                <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" alt="navbar brand"
                  class="navbar-brand" height="70" />
                <div id="infoMessage" class="text-center"><?php echo $message; ?></div>
              </div>
              <div class="form-container col-md-9 col-lg-8 mx-auto mt-4">
                <?php echo form_open('auth/login'); ?>

                <div class="form-group mb-3">
                  <?php echo form_input(array_merge($identity, ['class' => 'form-control mb-3', 'placeholder' => 'Enter your email'])); ?>
                </div>

                <div class="form-group mb-3">
                  <a class='text-muted float-end' href='pages-recoverpw.html'><small></small></a>
                  <?php echo form_input(array_merge($password, ['class' => 'form-control mb-3', 'placeholder' => 'Enter your password'])); ?>
                </div>

                <div class="form-group mb-3">
                  <div class="form-check mb-3">
                    <?php echo form_checkbox('remember', '1', false, 'id="remember" class="form-check-input"'); ?>
                    <label class="form-check-label" for="remember">Recuerdame</label>
                  </div>
                </div>

                <div class="form-group mb-0 text-center">
                  <?php echo form_submit('submit', lang('Auth.login_submit_btn'), ['class' => 'btn btn-primary w-100']); ?>
                </div>

                <?php echo form_close(); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- App js -->
  <script src="<?= base_url('admin/assets/js/core/jquery-3.7.1.min.js') ?>"></script>
  <script src="<?= base_url('admin/assets/js/core/popper.min.js') ?>"></script>
  <script src="<?= base_url('admin/assets/js/core/bootstrap.min.js') ?>"></script>

</body>

</html>