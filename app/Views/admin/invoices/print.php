<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    :root {
        --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        --background-color: <?= setting('App.corporateColor') ?>;
        --color: #fff;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        padding: 15px;
        position: relative;
        line-height: 1.5;
        background-color: #FFF;
        font-family: var(--font-family-monospace);
    }

    .invoice-container {
        min-height: 680px;
        background-color: white;
        padding: 10px;
        width: 98%;
    }

    header {
        border-bottom: 2px solid #e5e5e5;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .logo h1 {
        font-size: 24px;
        color: #333;
    }

    .highlight {
        color: var(--background-color);
    }

    .invoice-title {
        margin-top: 10px;
        text-align: right;
    }

    .invoice-title h2 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .invoice-header {
        margin-bottom: 20px;
    }

    .company-info,
    .client-info {
        width: 45%;
        font-size: 14px;
        color: #555;
        display: inline-block;
        vertical-align: top;
    }

    .company-info {
        float: left;
    }

    .client-info {
        float: right;
        text-align: right;
    }

    .invoice-details {
        margin-bottom: 20px;
        clear: both;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .invoice-table th,
    .invoice-table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
        font-size: 14px;

    }

    .invoice-table th {
        background-color: var(--background-color);
        font-weight: bold;
        color: var(--color)
    }

    .invoice-summary {
        float: right;
    }

    .summary-table {
        border-collapse: collapse;
        margin-top: 20px;
    }

    .summary-table th {
        background-color: var(--background-color);
        font-weight: bold;
        border: 1px solid #ccc;
        padding-left: 10px;
        padding-right: 10px;
        color: var(--color)
    }

    .summary-table td {
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        width: 182px;
    }

    .summary-table th,
    .summary-table td {
        text-align: left;
    }

    footer {
        width: 100%;
        margin-top: 50px;
        color: #b8b6b6;
        border-top: 1px solid #aaa;
        padding: 20px 0;
    }

    footer p {
        display: inline-block;
        border-left: 20px solid #0e9bed;
        padding-left: 20px;
    }


    @media print {
        footer {
            position: absolute;
            bottom: 10px;
            page-break-after: always;
        }
    }
</style>

<body>
    <div class="invoice-container" style="min-width: 600px">
        <header>
            <div class="logo">
                <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" width="110" />
            </div>
            <div class="invoice-title">
                <h2>Factura</h2>
            </div>
        </header>

        <section class="invoice-header">
            <div class="company-info" style="margin-bottom: 20px;">
                <p><?= setting('App.siteName'); ?></p>
                <p><?= setting('App.address'); ?></p>
                <p><?= setting('App.phone'); ?></p>
                <p><?= setting('App.email'); ?></p>
                <br>
                <table class="summary-table data-invoices">
                    <tr>
                        <th>Número</th>
                        <td><?= htmlspecialchars($data['uuid']) ?></td> <!-- Número de la factura -->
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($data['date_invoice']))) ?></td>
                        <!-- Fecha de la factura -->
                    </tr>
                </table>
            </div>

            <div class="client-info">
                <p><strong><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></strong></p>
                <!-- Nombre del cliente -->
                <p><?= htmlspecialchars($data['address']) ?></p> <!-- Dirección del cliente -->
                <p><?= htmlspecialchars($data['phone']) ?></p> <!-- Teléfono del cliente -->
                <p>NIF: <?= htmlspecialchars($data['ic']) ?></p> <!-- NIF del cliente -->
            </div>
        </section>

        <section class="invoice-details">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['details'] as $detail): ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['product_name']) ?></td>
                            <td><?= htmlspecialchars($detail['quantity']) ?></td>
                            <td><?= htmlspecialchars(number_format($detail['price'], 2)) ?>€</td>
                            <td><?= htmlspecialchars(number_format($detail['quantity'] * $detail['price'], 2)) ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="invoice-summary">
            <table class="summary-table">
                <tr>
                    <th>Base Imponible</th>
                    <td><?= htmlspecialchars(number_format($data['invoice_subtotal'], 2)) ?>€</td>
                    <!-- Base imponible -->
                </tr>
                <tr>
                    <th>IVA (<?= setting('App.vat') ?> %)</th>
                    <td><?= htmlspecialchars(number_format($data['tax'], 2)) ?>€</td> <!-- IVA -->
                </tr>
                <tr>
                    <th>Total</th>
                    <td><?= htmlspecialchars(number_format($data['invoice_total'], 2)) ?>€</td>
                    <!-- Total de la factura -->
                </tr>
            </table>
        </section>


    </div>
    <footer>
        <p><?= setting('App.footer_invoice') ?></p>
    </footer>
</body>

</html>