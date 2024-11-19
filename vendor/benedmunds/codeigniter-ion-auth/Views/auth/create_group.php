<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div id="infoMessage"><?php echo $message; ?></div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="card-title mb-0"><?= $title; ?></h5>
                </div>


                <?php echo form_open("auth/create_group"); ?>

                <div class="mb-3">
                    <?php echo form_label(lang('Auth.create_group_name_label'), 'group_name', ['class' => 'form-label']); ?>
                    <?php echo form_input(array_merge($group_name, ['class' => 'form-control'])); ?>
                </div>

                <div class="mb-3">
                    <?php echo form_label(lang('Auth.create_group_desc_label'), 'description', ['class' => 'form-label']); ?>
                    <?php echo form_input(array_merge($description, ['class' => 'form-control'])); ?>
                </div>

                <div class="text-end">
                    <?php echo form_submit('submit', lang('Auth.create_group_submit_btn'), ['class' => 'btn btn-primary']); ?>
                </div>

            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<?= $this->endSection() ?>