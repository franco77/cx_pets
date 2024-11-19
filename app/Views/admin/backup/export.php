<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?= $title ?></div>
            </div>
            <div class="card-body">
                <a href="<?php echo url('backup/exportDB') ?>" class="btn btn-sm btn-primary"> <i
                        class="fa fa-download"></i> &nbsp;&nbsp;&nbsp;
                    Generar Backup</a>
            </div>
            <!-- /.card-body -->

        </div>
    </div>
</div>
<!-- /.card -->



<?= $this->endSection() ?>
<!-- /.content -->

<?= $this->section("js") ?>
<?= $this->endSection() ?>