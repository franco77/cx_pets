<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<style>
    .custom-modal {
        max-width: 70%;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?= $title ?></div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <button class="btn btn-sm btn-danger bulk-delete">Eliminar</button>
                        <button class="btn btn-sm btn-primary refresh" purpose="add">Refrescar</button>
                    </div>
                    <button class="btn btn-sm btn-primary form-action" purpose="add">Agregar</button>
                </div>
                <table id="datatable" class="table table-sm activate-select dt-responsive nowrap w-100" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 0px"><input type="checkbox" class="check-items"></th>
                            <th>#</th>
                            <th>Client</th>
                            <th>Invoice Subtotal</th>
                            <th>Tax</th>
                            <th>Invoice Total</th>
                            <th>Amount Paid</th>
                            <th style="width: 0px">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal HTML -->
<div class="modal fade" id="invoicePaymentModal" tabindex="-1" aria-labelledby="invoicePaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="invoicePaymentModalLabel">Detalles del pago de factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Invoice Details Section -->
                <div class="mb-4">
                    <h6 class="text-uppercase fw-bold text-secondary">Detalles de la factura</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong># Factura:</strong> <span id="modalInvoiceId" class="text-primary"></span></p>
                            <p><strong>Total Factura:</strong> <span id="modalInvoiceTotal" class="text-success"></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Monto Pagado:</strong> <span id="modalAmountPaid" class="text-info"></span></p>
                            <p><strong>Monto Adeudado:</strong> <span id="modalAmountDue" class="text-danger"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Form Section -->
                <div id="paymentFormContainer" style="display:none;">
                    <h6 class="text-uppercase fw-bold text-secondary">Procesar pago</h6>
                    <form id="invoicePaymentForm">
                        <input type="hidden" id="invoiceIdInput" name="invoice_id">
                        <div class="mb-3">
                            <label for="paymentAmountInput" class="form-label">Monto del pago</label>
                            <input type="number" class="form-control" id="paymentAmountInput" name="payment_amount"
                                step="0.01" placeholder="Ingrese el monto del pago">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Procesar pago</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-form fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<!-- /.content -->

<!-- / Incluir la libreria toastr  -->
<!-- page script -->
<?= $this->section("js") ?>
<script>
    $(document).ready(function() {
        const host = "<?= $host ?>";
        const datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            scrollY: '45vh',
            responsive: false,
            orderCellsTop: true,
            ajax: {
                url: host + "data",
                type: "POST"
            },
            "columns": [{
                    "data": "column_bulk",
                    "searchable": false,
                    "orderable": false
                },
                {
                    "data": "uuid"
                },
                {
                    "data": "first_name"
                },
                {
                    "data": "invoice_subtotal"
                },
                {
                    "data": "tax"
                },
                {
                    "data": "invoice_total"
                },
                {
                    "data": "amount_paid"
                },
                {
                    "data": "column_action",
                    "searchable": false,
                    "orderable": false
                }
            ],
            "order": [
                [1, "DESC"]
            ]
        });

        function refreshTable() {
            datatable.ajax.reload();
        }

        function ajaxRequest(url, data, successMessage, errorMessage) {
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                cache: false,
                processData: false,
                contentType: false,
            }).done(function() {
                refreshTable();
                $('.modal-form').modal('hide');
                toastr.success(successMessage);
            }).fail(function(res) {
                $('.form-text').remove();
                $('.is-invalid').removeClass('is-invalid');
                const errors = jQuery.parseJSON(res.responseText);
                $.each(errors.messages, function(selector, value) {
                    $('[for="' + selector + '"]').after(
                        '<small class="form-text text-danger">' + value + '</small>');
                    $('[name="' + selector + '"]').addClass('is-invalid');
                });
                toastr.error(errorMessage);
            });
        }

        function deleteItems(ids) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, ¡eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const requests = ids.map(id => $.ajax({
                        url: host + 'delete/' + id,
                        type: 'POST',
                        dataType: 'json'
                    }));

                    $.when.apply($, requests).done(() => {
                        refreshTable();
                        Swal.fire(
                            '¡Eliminado!',
                            'Los registros han sido eliminados.',
                            'success'
                        );
                    }).fail((jqXHR, textStatus, errorThrown) => {
                        console.error('Error al eliminar los registros:', textStatus,
                            errorThrown);
                        Swal.fire(
                            'Error',
                            'Hubo un problema al eliminar los registros. Inténtalo de nuevo.',
                            'error'
                        );
                    });
                }
            });
        }


        datatable.on('draw', function() {
            $('.form-action').on('click', function() {
                const button = $(this);
                const modalForm = $('.modal-form');
                const itemId = button.attr('item-id');
                const purpose = button.attr('purpose');

                let title, url, submitUrl;
                if (purpose === "add") {
                    title = "Add Data";
                    url = host + 'new';
                    submitUrl = host + 'create';
                } else if (purpose === "edit") {
                    title = "Edit Data";
                    url = host + 'edit/' + itemId;
                    submitUrl = host + 'update/' + itemId;
                } else {
                    title = "Detail Data";
                    url = host + 'show/' + itemId;
                }

                $.ajax({
                    type: "GET",
                    url: url
                }).done(function(response) {
                    modalForm.find('.modal-title').text(title);
                    modalForm.find('.modal-body').html(response);
                    modalForm.modal('show');
                    initializePlugins();

                    $('#form input:text, #form textarea').first().focus();
                    $('#form').on('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        ajaxRequest(submitUrl, formData,
                            'Registro guardado con éxito',
                            'Error al guardar el registro');
                    });
                }).fail(function() {
                    alert("Data not found");
                });
            });
        });

        $('.refresh').on('click', refreshTable);

        $('.check-items').on('click', function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $('.bulk-delete').on('click', function() {
            const ids = $(".bulk-item:checked").map(function() {
                return $(this).val();
            }).get();

            if (ids.length) {
                deleteItems(ids);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Nada Seleccionado',
                    text: 'Por favor selecciona algún registro para borrar!'
                });
            }
        });

        function initializePlugins() {

            $('.select2').select2({
                dropdownParent: $("#form")
            });
            $('#notes').summernote({
                height: 250
            });
        }

    });


    $(document).ready(function() {

        // Capturamos el evento de click en el botón con clase .send-email
        $(document).on('click', '.send-email', function() {

            // Obtenemos el ID de la factura desde el atributo item-id
            var invoId = $(this).attr('item-id');

            // Hacemos una solicitud AJAX para enviar el correo
            $.ajax({
                url: '<?= base_url('invoices/sendVoucherByEmail/') ?>' +
                    invoId, // Genera la URL correctamente desde PHP
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Mostramos el SweetAlert en caso de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Correo Enviado',
                            text: 'El correo con el PDF adjunto fue enviado correctamente.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // SweetAlert para el caso de error en el envío
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al enviar',
                            text: 'Hubo un error al enviar el correo: ' + response
                                .message,
                            confirmButtonText: 'Reintentar'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // SweetAlert para el caso de error en la conexión
                    console.log('Error AJAX:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'Ocurrió un error al intentar enviar el correo. Por favor, inténtalo de nuevo.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
    });




    $(document).ready(function() {
        // Configuración inicial de Toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '5000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };

        // Mostrar detalles del modal de pago
        $(document).on('click', '.pay-invoice', function() {
            var invoiceId = $(this).attr('item-id');

            $.ajax({
                url: '<?= base_url('invoices/showInvoiceModal/') ?>' + invoiceId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Llenar los detalles en el modal
                    $('#modalInvoiceId').text(response.id);
                    $('#modalInvoiceTotal').text(response.invoice_total);
                    $('#modalAmountPaid').text(response.amount_paid);
                    $('#modalAmountDue').text(response.amount_due);
                    $('#invoiceIdInput').val(response.id);

                    // Mostrar formulario de pago si hay saldo pendiente
                    if (response.amount_due > 0) {
                        $('#paymentFormContainer').show();
                    } else {
                        $('#paymentFormContainer').hide();
                    }

                    $('#invoicePaymentModal').modal('show');
                },
                error: function() {
                    toastr.error(
                        'Error al cargar los detalles de la factura. Inténtalo de nuevo.');
                }
            });
        });

        // Procesar el formulario de pago
        $('#invoicePaymentForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url('invoices/processPayment') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Mostrar notificación de éxito con Toastr
                        toastr.success(response.message);

                        // Ocultar el modal
                        $('#invoicePaymentModal').modal('hide');

                        // Opcional: Refrescar la lista de facturas o actualizar los datos en la página
                    } else {
                        // Mostrar notificación de error con Toastr
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    // Mostrar mensaje de error en caso de fallo de la solicitud
                    toastr.error('Error al procesar el pago. Inténtalo de nuevo.');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>