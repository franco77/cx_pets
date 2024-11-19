<div class="sidebar" data-background-color="<?= setting('App.sidebar') ?>">
    <div class="sidebar-logo">
        <!-- Logo Header -->

        <div class="logo-header" data-background-color="<?= setting('App.sidebar') ?>">
            <a href="<?= base_url('Dashboard') ?>" class="logo">
                <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" alt="navbar brand"
                    class="navbar-brand" height="40" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item">
                    <a href="<?= base_url('dashboard/') ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('auth/') ?>">
                        <i class="fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('pets/') ?>">
                        <i class="fas fa-paw"></i>
                        <p>Pets</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('pettests/') ?>">
                        <i class="far fa-edit"></i>
                        <p>Pet Test</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('prescriptions/') ?>">
                        <i class="fas fa-user-edit"></i>
                        <p>Prescription</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('vaccines/') ?>">
                        <i class="fas fa-syringe"></i>
                        <p>Vaccines</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('owners/') ?>">
                        <i class="fas fa-user-shield"></i>
                        <p>Owners</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('products/') ?>">
                        <i class="fab fa-product-hunt"></i>
                        <p>Products</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('invoices/') ?>">
                        <i class="fas fa-paste"></i>
                        <p>Invoices</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('invoicedetails/') ?>">
                        <i class="fas fa-paste"></i>
                        <p>Invoices Details</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('appointments/') ?>">
                        <i class="fas fa-calendar-plus"></i>
                        <p>Appointments</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarLayouts">
                        <i class="fas fa-th-list"></i>
                        <p>Fianzas</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="sidebarLayouts">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?= base_url('incomes/') ?>">
                                    <span class="sub-item">Incomes</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('expenses/') ?>">
                                    <span class="sub-item">Expenses</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('FileManager/') ?>">
                        <i class="far fa-folder"></i>
                        <p>File Manager</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('backup/') ?>">
                        <i class="fas fa-download"></i>
                        <p>Backup</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('calendar/') ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Calendars</p>
                    </a>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarOthers">
                        <i class="fas fa-th-list"></i>
                        <p>Otros</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="sidebarOthers">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?= base_url('species/') ?>">
                                    <span class="sub-item">Species</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('breeds/') ?>">
                                    <span class="sub-item">Breeds</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('tests/') ?>">
                                    <span class="sub-item">Tests</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('vendors/') ?>">
                                    <span class="sub-item">Vendors</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>





            </ul>
        </div>
    </div>
</div>