<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= setting('App.siteName') ?></title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <?= $this->include("admin/layout/partials/styles") ?>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?= $this->include('admin/layout/partials/aside-nav') ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <?= $this->include("admin/layout/partials/top_bar") ?>
            <!-- End Navbar -->


            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="<?= base_url('/') ?>">
                                    <i class="icon-home"></i>
                                </a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#"><?= $title ?></a>
                            </li>
                        </ul>
                    </div>


                    <?= $this->renderSection("content") ?>


                </div>
            </div>

            <?= $this->include("admin/layout/partials/footer") ?>
        </div>


        <!-- End Custom template -->
    </div>
    <?= $this->include("admin/layout/partials/scripts") ?>
</body>

</html>