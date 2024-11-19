<!--   Core JS Files   -->
<script src="<?= assets_url('admin/js/core/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/core/popper.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/core/bootstrap.min.js') ?>"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
<!-- jQuery Scrollbar -->
<script src="<?= assets_url('admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/kaiadmin.js') ?>"></script>


<!-- third party js -->
<script src="<?= assets_url('admin/js/plugin/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-bs5/js/dataTables.bootstrap5.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') ?>">
</script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-buttons/js/buttons.flash.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-keytable/js/dataTables.keyTable.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/datatables.net-select/js/dataTables.select.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/pdfmake/build/pdfmake.min.js') ?>"></script>
<script src="<?= assets_url('admin/js/plugin/pdfmake/build/vfs_fonts.js') ?>"></script>

<script src="<?= assets_url('admin/js/plugin/summernote/summernote-lite.min.js') ?>"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Sweet Alerts js -->
<script src="<?= assets_url('admin/js/plugin/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<!-- Select 2 -->
<script src="<?= assets_url('admin/js/plugin/select2/select2.full.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $("#datatables").DataTable({});
        $('.select2').select2();

    });
</script>
<?php $this->renderSection('js') ?>