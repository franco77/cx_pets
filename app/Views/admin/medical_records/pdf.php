<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica de <?= $pet['name']; ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin: 0;
        padding: 5px 0;
    }

    .container {
        width: 100%;
        margin: 0 auto;
        padding: 20px;
    }

    .section {
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    .header {
        background-color: #007bff;
        color: white;
        padding: 10px;
        text-align: center;
    }

    .sub-header {
        background-color: #6c757d;
        color: white;
        padding: 5px;
        margin-bottom: 10px;
    }

    .info {
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Historia Clínica de <?= $pet['name']; ?></h1>
        </div>

        <div class="info">
            <h3>Información del Dueño</h3>
            <p><strong>Nombre:</strong> <?= $owner['first_name'] . ' ' . $owner['last_name']; ?></p>
            <p><strong>Email:</strong> <?= $owner['email']; ?></p>
            <p><strong>Teléfono:</strong> <?= $owner['phone']; ?></p>
            <p><strong>Dirección:</strong> <?= $owner['address']; ?></p>
        </div>

        <div class="section">
            <div class="sub-header">
                <h3>Mascota: <?= $pet['name']; ?></h3>
            </div>
            <p><strong>Especie:</strong> <?= $species['name']; ?></p> <!-- Mostrar nombre de la especie -->
            <p><strong>Raza:</strong> <?= $breed['name']; ?></p> <!-- Mostrar nombre de la raza -->
            <p><strong>Edad:</strong> <?= $pet['age']; ?></p>
            <p><strong>Peso:</strong> <?= $pet['weight'] . ' ' . $pet['weight_unit']; ?></p>

            <!-- Sección de pruebas -->
            <h4>Pruebas</h4>
            <?php if (!empty($tests)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Resultado</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tests as $test): ?>
                    <?php foreach ($test['details'] as $detail): ?>
                    <tr>
                        <td><?= $test['test_date']; ?></td>
                        <td><?= $detail['code']; ?></td>
                        <td><?= $detail['name']; ?></td>
                        <td><?= $detail['description']; ?></td>
                        <td><?= $detail['result']; ?></td>
                        <td><?= $detail['notes']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No hay pruebas para esta mascota.</p>
            <?php endif; ?>

            <!-- Sección de recetas -->
            <h4>Recetas</h4>
            <?php if (!empty($prescriptions)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Inicio</th>
                        <th>Producto</th>
                        <th>Dosificación</th>
                        <th>Frecuencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                    <?php foreach ($prescription['details'] as $detail): ?>
                    <tr>
                        <td><?= $prescription['start_date']; ?></td>
                        <td><?= $detail['product_name']; ?></td>
                        <td><?= $detail['dosage']; ?></td>
                        <td><?= $detail['frequency']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No hay recetas para esta mascota.</p>
            <?php endif; ?>

            <!-- Sección de vacunas -->
            <h4>Vacunas</h4>
            <?php if (!empty($vaccines)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Dosificación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vaccines as $vaccine): ?>
                    <?php foreach ($vaccine['details'] as $detail): ?>
                    <tr>
                        <td><?= $vaccine['vaccine_date']; ?></td>
                        <td><?= $detail['product_name']; ?></td>
                        <td><?= $detail['dosage']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No hay vacunas para esta mascota.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>