<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescripción Médica</title>
    <style>
    :root {
        --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        --background-color: <?=setting('App.corporateColor') ?>;
        --color: <?=setting('App.corporateColor') ?>;
    }

    body {
        font-family: var(--font-family-monospace);
        margin: 0;
        padding: 0;
        font-size: 12px;
        color: #333;
    }

    .info {
        line-height: 0.40;
    }

    .container {
        width: 100%;

        padding: -8px;
    }

    /* Header Section */
    .header {
        margin-bottom: 10px;
        text-align: center;
    }

    .header h1 {
        font-size: 16px;
        font-weight: bold;
        margin: 0;
        color: var(--color);
    }

    .header img {
        max-width: 150px;
        margin-bottom: 10px;
    }

    .clinic-info {
        font-size: 14px;
        color: #555;
    }

    /* Información del propietario y mascota */
    .info-section {
        margin-bottom: 30px;
    }

    .info-section h2 {
        font-size: 15px;
        border-bottom: 1px solid var(--color);
        padding-bottom: 5px;
        margin-bottom: 15px;
        color: var(--color);
    }

    .info-section table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .info-section th {
        padding: 8px 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-section td {
        padding: 8px 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-section th {
        text-align: left;
        background-color: #f9f9f9;
    }

    .info-section td {
        vertical-align: top;
    }

    /* Tabla para colocar información de mascota y propietario en paralelo */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .info-table td {
        vertical-align: top;
        padding: 6px;
    }

    /* Prescription Details */
    .prescription-details table {
        width: 100%;
        border-collapse: collapse;
    }

    .prescription-details th,
    .prescription-details td {
        padding: 7px;
        border: 1px solid #e0e0e0;
        text-align: left;
    }

    .prescription-details th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    /* Footer Section */
    .footer {
        line-height: 0.60;
        text-align: center;
        margin-top: 40px;
        font-size: 12px;
        color: #777;
    }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" width="250" />
            <h1>Prescripción Médica</h1>
            <div class="clinic-info">
                <p class="info"><?= setting('App.siteName'); ?></p>
                <p class="info"><?= setting('App.phone'); ?> - <?= setting('App.email'); ?></p>
                <p class="info"><?= setting('App.address'); ?></p>
            </div>
        </div>

        <!-- Información de la mascota y propietario en paralelo utilizando tabla -->
        <table class="info-table">
            <tr>
                <!-- Información de la mascota -->
                <td width="30%">
                    <div class="info-section">
                        <h2>Información de la Mascota</h2>
                        <table class="table">
                            <tr>
                                <th>Nombre</th>
                                <td><?= $data['pet_name'] ?></td>
                            </tr>
                            <tr>
                                <th>Especie</th>
                                <td><?= $data['specie_name'] ?></td>
                            </tr>
                            <tr>
                                <th>Raza</th>
                                <td><?= $data['breed_name'] ?></td>
                            </tr>
                            <!-- Agregar más filas según sea necesario -->
                        </table>
                    </div>
                </td>

                <!-- Información del propietario -->
                <td width="30%">
                    <div class="info-section">
                        <h2>Información del Propietario</h2>
                        <table class="table">
                            <tr>
                                <th>Nombre</th>
                                <td><?= $data['first_name'] . ' ' . $data['last_name'] ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?= $data['email'] ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono</th>
                                <td><?= $data['phone'] ?></td>
                            </tr>
                            <!-- Agregar más filas según sea necesario -->
                        </table>
                    </div>
                </td>
            </tr>
        </table>


        <?= $data['notes'] ?>

        <!-- Prescripción de Medicamentos -->
        <div class="prescription-details">
            <h3>Detalles de la Prescripción</h3>
            <table>
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['details'] as $detail): ?>
                    <tr>
                        <td><?= $detail['product_name'] ?></td>
                        <td><?= $detail['dosage'] ?></td>
                        <td><?= $detail['frequency'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer con información adicional -->
        <div class="footer">
            <p>Fecha de la Prescripción: [<?= $data['created_at'] ?>]</p>
            <p style="color: var(--color);">Fecha valida del tratamiento: Desde el [<?= $data['start_date'] ?>] - Hasta
                el
                [<?= $data['end_date'] ?>]
            </p>
            <p>Esta prescripción es válida solo para la mascota mencionada arriba.</p>
            <p>Si tiene alguna pregunta, póngase en contacto con la clínica.</p>
        </div>
    </div>

</body>

</html>