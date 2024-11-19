<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>



<div class="row">
      <div class="col-md-12">
            <div id="infoMessage"><?php //echo $message; 
                                    ?></div>
            <div class="card">
                  <div class="card-body">
                        <div class="mb-4">
                              <h5 class="card-title mb-0"><?= $title; ?></h5>
                        </div>
                        <?= form_open('auth/create_user'); ?>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_fname_label'), 'first_name', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($first_name, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('first_name'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_lname_label'), 'last_name', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($last_name, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('last_name'); ?>
                              </div>
                        </div>

                        <?php if ($identity_column !== 'email'): ?>
                              <div class="mb-3">
                                    <?= form_label(lang('Auth.create_user_identity_label'), 'identity', ['class' => 'form-label']); ?>
                                    <div class="text-danger">
                                          <?= \Config\Services::validation()->getError('identity'); ?>
                                    </div>
                                    <?= form_input(array_merge($identity, ['class' => 'form-control'])); ?>
                              </div>
                        <?php endif; ?>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_company_label'), 'company', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($company, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('company'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_email_label'), 'email', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($email, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('email'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_phone_label'), 'phone', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($phone, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('phone'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_password_label'), 'password', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($password, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('password'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?= form_label(lang('Auth.create_user_password_confirm_label'), 'password_confirm', ['class' => 'form-label']); ?>
                              <?= form_input(array_merge($password_confirm, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('password_confirm'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?= form_submit('submit', lang('Auth.create_user_submit_btn'), ['class' => 'btn btn-primary']); ?>
                        </div>


                        <?= form_close(); ?>
                  </div>
            </div>
      </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<?= $this->endSection() ?>