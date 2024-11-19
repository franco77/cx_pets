<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>

<div class="mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white ">Historia Clínica de <?= $pet['name']; ?>
                </div>
                <div class="card-body p-4">
                    <!-- Información del dueño -->
                    <div class="mb-5">
                        <h5 class="bg-dark text-white p-2">Información del Dueño</h5>
                        <p><strong>Nombre:</strong> <?= $owner['first_name'] . ' ' . $owner['last_name']; ?></p>
                        <p><strong>Email:</strong> <?= $owner['email']; ?></p>
                        <p><strong>Teléfono:</strong> <?= $owner['phone']; ?></p>
                        <p><strong>Dirección:</strong> <?= $owner['address']; ?></p>
                    </div>

                    <!-- Información de la mascota -->
                    <div class="mb-5">
                        <h5 class="bg-dark text-white p-2">Mascota: <?= $pet['name']; ?></h5>
                        <p><strong>Especie:</strong> <?= $species['name']; ?></p>
                        <p><strong>Raza:</strong> <?= $breed['name']; ?></p>
                        <p><strong>Edad:</strong> <?= $pet['age']; ?></p>
                        <p><strong>Peso:</strong> <?= $pet['weight'] . ' ' . $pet['weight_unit']; ?></p>
                    </div>

                    <!-- Sección de pruebas -->
                    <h6 class="mt-4 mb-3">Pruebas</h6>
                    <?php if (!empty($tests)): ?>
                    <div class="accordion accordion-flush" id="accordionPruebas">
                        <?php foreach ($tests as $index => $test): ?>
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header" id="headingTest<?= $index ?>">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseTest<?= $index ?>"
                                    aria-expanded="false" aria-controls="collapseTest<?= $index ?>">
                                    Prueba del <?= $test['test_date']; ?>
                                </button>
                            </h2>
                            <div id="collapseTest<?= $index ?>" class="accordion-collapse collapse"
                                aria-labelledby="headingTest<?= $index ?>" data-bs-parent="#accordionPruebas">
                                <div class="accordion-body">
                                    <strong>Fecha:</strong> <?= $test['test_date']; ?><br>
                                    <strong>Notas:</strong> <?= $test['notes_test']; ?>
                                    <?php if (!empty($test['details'])): ?>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Resultado</th>
                                                    <th>Notas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($test['details'] as $detail): ?>
                                                <tr>
                                                    <td><?= $detail['code']; ?></td>
                                                    <td><?= $detail['name']; ?></td>
                                                    <td><?= $detail['description']; ?></td>
                                                    <td><?= $detail['result']; ?></td>
                                                    <td><?= $detail['notes']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p>No hay pruebas para esta mascota.</p>
                    <?php endif; ?>

                    <!-- Sección de recetas -->
                    <h6 class="mt-4 mb-3">Recetas</h6>
                    <?php if (!empty($prescriptions)): ?>
                    <div class="accordion accordion-flush" id="accordionRecetas">
                        <?php foreach ($prescriptions as $index => $prescription): ?>
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header" id="headingPrescription<?= $index ?>">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapsePrescription<?= $index ?>"
                                    aria-expanded="false" aria-controls="collapsePrescription<?= $index ?>">
                                    Receta del <?= $prescription['start_date']; ?>
                                </button>
                            </h2>
                            <div id="collapsePrescription<?= $index ?>" class="accordion-collapse collapse"
                                aria-labelledby="headingPrescription<?= $index ?>" data-bs-parent="#accordionRecetas">
                                <div class="accordion-body">
                                    <strong>Inicio:</strong> <?= $prescription['start_date']; ?><br>
                                    <strong>Fin:</strong> <?= $prescription['end_date']; ?><br>
                                    <strong>Notas:</strong> <?= $prescription['notes']; ?>
                                    <?php if (!empty($prescription['details'])): ?>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Dosificación</th>
                                                    <th>Frecuencia</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($prescription['details'] as $detail): ?>
                                                <tr>
                                                    <td><?= $detail['product_name']; ?></td>
                                                    <td><?= $detail['dosage']; ?></td>
                                                    <td><?= $detail['frequency']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p>No hay recetas para esta mascota.</p>
                    <?php endif; ?>

                    <!-- Sección de vacunas -->
                    <h6 class="mt-4 mb-3">Vacunas</h6>
                    <?php if (!empty($vaccines)): ?>
                    <div class="accordion accordion-flush" id="accordionVacunas">
                        <?php foreach ($vaccines as $index => $vaccine): ?>
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header" id="headingVaccine<?= $index ?>">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseVaccine<?= $index ?>"
                                    aria-expanded="false" aria-controls="collapseVaccine<?= $index ?>">
                                    Vacuna del <?= $vaccine['vaccine_date']; ?>
                                </button>
                            </h2>
                            <div id="collapseVaccine<?= $index ?>" class="accordion-collapse collapse"
                                aria-labelledby="headingVaccine<?= $index ?>" data-bs-parent="#accordionVacunas">
                                <div class="accordion-body">
                                    <strong>Fecha:</strong> <?= $vaccine['vaccine_date']; ?><br>
                                    <strong>Notas:</strong> <?= $vaccine['notes']; ?>
                                    <?php if (!empty($vaccine['details'])): ?>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Dosificación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($vaccine['details'] as $detail): ?>
                                                <tr>
                                                    <td><?= $detail['product_name']; ?></td>
                                                    <td><?= $detail['dosage']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p>No hay vacunas para esta mascota.</p>
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <a class="btn btn-danger"
                        href="<?= base_url('MedicalRecords/generatePdf/') . $pet['id'] ?>">Imprimir PDF <i
                            class="fas fa-file-pdf"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>