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

                        <?php echo form_open(uri_string()); ?>

                        <div class="mb-3">
                              <?php echo form_label(lang('Auth.edit_user_fname_label'), 'first_name', ['class' => 'form-label']); ?>
                              <?php echo form_input(array_merge($first_name, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('first_name'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?php echo form_label(lang('Auth.edit_user_lname_label'), 'last_name', ['class' => 'form-label']); ?>
                              <?php echo form_input(array_merge($last_name, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('last_name'); ?>
                              </div>

                        </div>

                        <div class="mb-3">
                              <?php echo form_label(lang('Auth.edit_user_company_label'), 'company', ['class' => 'form-label']); ?>
                              <?php echo form_input(array_merge($company, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('company'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?php echo form_label(lang('Auth.edit_user_phone_label'), 'phone', ['class' => 'form-label']); ?>
                              <?php echo form_input(array_merge($phone, ['class' => 'form-control'])); ?>
                              <div class="text-danger">
                                    <?= \Config\Services::validation()->getError('phone'); ?>
                              </div>
                        </div>

                        <div class="mb-3">
                              <?php echo form_label(lang('Auth.edit_user_password_label'), 'password', ['class' => 'form-label']); ?>
                              <?php echo form_input(array_merge($password, ['class' => 'form-control'])); ?>
                        </div>

                        <div class="mb-3">
                              <?php echo form_label(lang('Auth.edit_user_password_confirm_label'), 'password_confirm', ['class' => 'form-label']); ?>
                              <?php echo form_input(array_merge($password_confirm, ['class' => 'form-control'])); ?>
                        </div>


                        <?php if ($ionAuth->isAdmin()): ?>

                              <h3><?php echo lang('Auth.edit_user_groups_heading'); ?></h3>
                              <?php foreach ($groups as $group): ?>
                                    <div class="form-check mb-2">
                                          <?php
                                          $gID = $group['id'];
                                          $checked = '';
                                          foreach ($currentGroups as $grp) {
                                                if ($gID == $grp->id) {
                                                      $checked = 'checked';
                                                      break;
                                                }
                                          }
                                          ?>
                                          <input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>" class="form-check-input"
                                                id="group<?php echo $group['id']; ?>" <?php echo $checked; ?>>
                                          <label class="form-check-label" for="group<?php echo $group['id']; ?>">
                                                <?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?>
                                          </label>
                                    </div>
                              <?php endforeach ?>

                        <?php endif ?>



                        <?php echo form_hidden('id', $user->id); ?>
                        <div class="mb-4">
                              <?php echo form_submit('submit', lang('Auth.edit_user_submit_btn'), ['class' => 'btn btn-primary']); ?>
                        </div>
                        <?php echo form_close(); ?>
                  </div>
            </div>
      </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<?= $this->endSection() ?>