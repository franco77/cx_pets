    <link rel="icon" href="<?= assets_url('admin/img/kaiadmin/favicon.ico') ?>" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="<?= assets_url('admin/js/plugin/webfont/webfont.min.js') ?>"></script>
    <script>
WebFont.load({
    google: {
        families: ["Public Sans:300,400,500,600,700"]
    },
    custom: {
        families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
        ],
        urls: ["<?= assets_url('admin/css/fonts.min.css') ?>"],
    },
    active: function() {
        sessionStorage.fonts = true;
    },
});
    </script>

    <!-- CSS Files -->

    <!-- third party css -->
    <link href="<?= assets_url('admin/js/plugin/datatables.net-bs5/css/dataTables.bootstrap5.min.css') ?>"
        rel="stylesheet" type="text/css" />
    <link href="<?= assets_url('admin/js/plugin/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') ?>"
        rel="stylesheet" type="text/css" />
    <link href="<?= assets_url('admin/js/plugin/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') ?>"
        rel="stylesheet" type="text/css" />
    <link href="<?= assets_url('admin/js/plugin/datatables.net-select-bs5/css/select.bootstrap5.min.css') ?>"
        rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <!-- Summernote -->


    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="<?= assets_url('admin/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" href="<?= assets_url('admin/css/plugins.css') ?>" />
    <link rel="stylesheet" href="<?= assets_url('admin/css/kaiadmin.css') ?>" />