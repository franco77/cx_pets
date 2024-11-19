<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div id="infoMessage"><?php echo $message; ?></div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?= $title ?></div>
            </div>
            <div class="card-body">
                <table id="datatables" class="table table-sm activate-select dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th><?php echo lang('Auth.index_fname_th'); ?></th>
                            <th><?php echo lang('Auth.index_lname_th'); ?></th>
                            <th><?php echo lang('Auth.index_email_th'); ?></th>
                            <th><?php echo lang('Auth.index_groups_th'); ?></th>
                            <th><?php echo lang('Auth.index_status_th'); ?></th>
                            <th><?php echo lang('Auth.index_action_th'); ?></th>
                        </tr>
                    </thead>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user->first_name, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user->last_name, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php foreach ($user->groups as $group): ?>
                                    <?php echo anchor('auth/edit_group/' . $group->id, htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8')); ?>
                                    |
                                <?php endforeach ?>
                            </td>
                            <td><?php echo ($user->active) ? anchor('auth/deactivate/' . $user->id, lang('Auth.index_active_link')) : anchor("auth/activate/" . $user->id, lang('Auth.index_inactive_link')); ?>
                            </td>
                            <td><?php echo anchor('auth/edit_user/' . $user->id, lang('Auth.index_edit_link')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
<p class="mt-3">
    <?php echo anchor('auth/create_user', lang('Auth.index_create_user_link'), ['class' => 'btn btn-primary me-2']); ?>
    <?php echo anchor('auth/create_group', lang('Auth.index_create_group_link'), ['class' => 'btn btn-secondary']); ?>
</p>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<?= $this->endSection() ?>