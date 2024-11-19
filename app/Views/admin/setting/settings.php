<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="col-xl-12">
    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <h5 class="card-title mb-0"><?= $title; ?></h5>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <div class="nav flex-column nav-pills nav-secondary" id="v-pills-tab" role="tablist"
                        aria-orientation="vertical">
                        <a class="nav-link active show mb-1" id="v-pills-dataCompany-tab" data-bs-toggle="pill"
                            href="#v-pills-dataCompany" role="tab" aria-controls="v-pills-dataCompany"
                            aria-selected="true">Compañia</a>
                        <a class="nav-link mb-1" id="v-pills-logo-tab" data-bs-toggle="pill" href="#v-pills-logo"
                            role="tab" aria-controls="v-pills-logo" aria-selected="false">Logo</a>
                        <a class="nav-link mb-1" id="v-pills-formats-tab" data-bs-toggle="pill" href="#v-pills-formats"
                            role="tab" aria-controls="v-pills-formats" aria-selected="false">Formatear</a>
                        <a class="nav-link mb-1" id="v-pills-otherSettings-tab" data-bs-toggle="pill"
                            href="#v-pills-otherSettings" role="tab" aria-controls="v-pills-otherSettings"
                            aria-selected="false">Otros Ajustes</a>
                    </div>
                </div> <!-- end col-->

                <div class="col-sm-9">
                    <div class="tab-content pt-0">
                        <div class="tab-pane fade active show" id="v-pills-dataCompany" role="tabpanel"
                            aria-labelledby="v-pills-dataCompany-tab">
                            <div id="message-container"></div>
                            <form id="form-dataCompany" action="<?= base_url('settings/dataCompanySettings') ?>"
                                method="post">

                                <div class="mb-3">
                                    <label for="vat" class="form-label">Vat</label>
                                    <input type="text" class="form-control" name="vat" id="vat"
                                        placeholder="placeholder" value="<?= setting()->get('App.vat'); ?>"
                                        autocomplete="off">
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="siteName" class="form-label">Titulo</label>
                                            <input type="text" class="form-control" name="siteName" id="siteName"
                                                placeholder="placeholder" value="<?= setting()->get('App.siteName'); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Dirección</label>
                                            <input type="text" class="form-control" name="address" id="address"
                                                placeholder="placeholder" value="<?= setting()->get('App.address'); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="placeholder" value="<?= setting()->get('App.email'); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Teléfono Empresa</label>
                                            <input type="tel" class="form-control" name="phone" id="phone"
                                                placeholder="placeholder" value="<?= setting()->get('App.phone'); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="corporateColor" class="form-label">Color Corporativo</label>
                                            <input type="color" class="form-control-color" name="corporateColor"
                                                id="corporateColor" style="width: 100%;"
                                                value="<?= setting()->get('App.corporateColor'); ?>">
                                        </div>

                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label" for="sidebar">Barra De Navegacion</label>
                                            <select class="select2" name="sidebar" id="sidebar" style="width:100%;">
                                                <?php
                                                $sidebar = array(
                                                    'dark' => 'Oscuro',
                                                    'white' => 'Claro',
                                                );

                                                foreach ($sidebar as $value => $display_text) {
                                                    $selected = ($value == setting()->get('App.sidebar')) ? ' selected="selected"' : "";
                                                    echo '<option value="' . $value . '" ' . $selected . '>' . $display_text . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="v-pills-logo" role="tabpanel" aria-labelledby="v-pills-logo-tab">
                            <div id="message-container"></div>
                            <?php if (session()->has('errors')): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach (session('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->has('msg')): ?>
                                <div class="alert alert-success">
                                    <?= esc(session('msg')) ?>
                                </div>
                            <?php endif; ?>

                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h5 class="card-title mb-0">Logo</h5>
                                    </div>
                                    <img src="<?= base_url('uploads/logo/') . setting()->get('App.file') ?>"
                                        class="img-thumbnail mb-3" alt="Logo" width="170" height="170">
                                    <form method="post" action="<?= base_url('settings/upload') ?>"
                                        enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Logo</label>
                                            <div class="input-group">
                                                <input type="file" name="file" class="form-control" id="file">
                                                <button type="submit" class="btn btn-primary">Cargar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="v-pills-formats" role="tabpanel"
                            aria-labelledby="v-pills-formats-tab">
                            <div id="message-container"></div>
                            <form id="form-formats" action="<?= base_url('settings/formatsSettings') ?>" method="post">
                                <div class="mb-3">
                                    <label for="currency_symbol" class="control-label">Moneda</label>
                                    <select name="currency_symbol" class="select2" data-placeholder="Select an option"
                                        style="width:100%;">
                                        <option value=""></option>
                                        <?php
                                        foreach ($all_currency as $currency) {
                                            $selected = ($currency['code'] == setting()->get('App.currency_symbol')) ? ' selected="selected"' : "";
                                            echo '<option value="' . $currency['code'] . '" ' . $selected . '>' . $currency['code'] . ' - ' . $currency['country'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="date_format" class="control-label">Formatear Fechas</label>
                                    <select name="date_format" class="select2" data-placeholder="Select an option"
                                        style="width:100%;">
                                        <option></option>
                                        <?php
                                        $date_format_values = array(
                                            '1' => '25/06/2014',
                                            '2' => '25-06-2014',
                                            '3' => '25.06.2014',
                                            '4' => '25 Jun 2014',
                                            '5' => '25 Junio 2014',
                                            '6' => 'Mar 25 Jun 2014',
                                            '7' => 'Martes 25 Junio 2014',
                                            '8' => 'March 25, 2020 - 12:00 am',
                                        );

                                        foreach ($date_format_values as $value => $display_text) {
                                            $selected = ($value == setting()->get('App.date_format')) ? ' selected="selected"' : "";
                                            echo '<option value="' . $value . '" ' . $selected . '>' . $display_text . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="number_format" class="control-label">Formatear Cifras</label>
                                    <select name="number_format" class="select2" data-placeholder="Select an option"
                                        style="width:100%;">
                                        <option></option>
                                        <?php
                                        $number_format_values = array(
                                            '1' => '1,000,000.00',
                                            '2' => '1,000,000',
                                            '3' => '1.000.000',
                                            '4' => '1.000.000,00',
                                        );

                                        foreach ($number_format_values as $value => $display_text) {
                                            $selected = ($value == setting()->get('App.number_format')) ? ' selected="selected"' : "";
                                            echo '<option value="' . $value . '" ' . $selected . '>' . $display_text . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>




                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="v-pills-otherSettings" role="tabpanel"
                            aria-labelledby="v-pills-otherSettings-tab">
                            <div id="message-container"></div>
                            <form id="form-otherSettings" action="<?= base_url('settings/otherSettings') ?>"
                                method="post">

                                <div class="mb-3">
                                    <label class="form-label">Descuento de inventario </label>
                                    <div class="form-check form-check-inline">
                                        <?php $selectedYes = (setting()->get('App.inventory') == 'yes') ? ' checked' : ''; ?>
                                        <input class="form-check-input" type="radio" name="inventory" id="inventory_0"
                                            value="yes" aria-describedby="inventoryHelpBlock" <?= $selectedYes; ?>>
                                        <label class="form-check-label" for="inventory_0">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <?php $selectedNo = (setting()->get('App.inventory') == 'no') ? ' checked' : ''; ?>
                                        <input class="form-check-input" type="radio" name="inventory" id="inventory_1"
                                            value="no" aria-describedby="inventoryHelpBlock" <?= $selectedNo; ?>>
                                        <label class="form-check-label" for="inventory_1">No</label>
                                    </div>
                                    <small id="inventoryHelpBlock" class="form-text text-muted">
                                        <p>By selecting Yes the inventory will automatically be updated every time
                                            material orders are generated.</p>
                                    </small>
                                </div>

                                <hr>
                                <div class="mb-3">
                                    <label for="footer_voucher" class="form-label">Text Footer Voucher</label>
                                    <textarea class="form-control" id="footer_voucher" name="footer_voucher"
                                        rows="3"><?= setting()->get('App.footer_voucher'); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="footer_invoice" class="form-label">Text Footer Invoice</label>
                                    <textarea class="form-control" id="footer_invoice" name="footer_invoice"
                                        rows="3"><?= setting()->get('App.footer_invoice'); ?></textarea>
                                </div>



                                <div class="mb-3">
                                    <label for="templateAppoint" class="form-label">Text Footer Invoice</label>
                                    <textarea class="form-control" id="templateAppoint" name="templateAppoint"
                                        rows="10"><?= setting()->get('App.templateAppoint'); ?></textarea>
                                </div>


                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col-->
            </div> <!-- end row-->
        </div>
    </div> <!-- end card-->
</div> <!-- end col -->

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(form[0]);
            var tabPane = form.closest('.tab-pane');
            var messageContainer = tabPane.find('#message-container');

            messageContainer.find('.alert').remove();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log("Respuesta recibida:", response);

                    if (response.status === 'success') {
                        var alertType = 'alert-success';
                    } else {
                        var alertType = 'alert-danger';
                    }

                    mostrarAlerta(alertType, response.message);
                },
                error: function() {
                    mostrarAlerta('alert-danger', 'Hubo un error al guardar los ajustes.');
                }
            });

            function mostrarAlerta(tipo, mensaje) {
                console.log("Mostrando alerta:", tipo, mensaje);
                messageContainer.html(`
                <div class="alert ${tipo} alert-dismissible fade show" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);

                setTimeout(function() {
                    messageContainer.find('.alert').alert('close');
                }, 5000);
            }
        });
    });
</script>

<?= $this->endSection() ?>