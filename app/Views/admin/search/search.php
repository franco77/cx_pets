<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title text-white">Resultados para "<?= esc($query) ?>"</h4>
                </div>
                <div class="card-body">

                    <!-- Dueños Encontrados -->
                    <h5 class="mb-3">Dueños Encontrados</h5>
                    <?php if (!empty($owners)): ?>
                        <div class="list-group mb-4">
                            <?php foreach ($owners as $owner): ?>
                                <a href="#"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc($owner['first_name']) ?> <?= esc($owner['last_name']) ?></strong><br>
                                        <small>Email: <?= esc($owner['email']) ?></small><br>
                                        <small>Teléfono: <?= esc($owner['phone']) ?></small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?= esc($owner['ic']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No se encontraron dueños.</p>
                    <?php endif; ?>

                    <!-- Mascotas Encontradas -->
                    <h5 class="mb-3">Mascotas Encontradas</h5>
                    <?php if (!empty($pets)): ?>
                        <div class="list-group mb-4">
                            <?php foreach ($pets as $pet): ?>
                                <a href="#"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc($pet['name']) ?></strong> - <?= esc($pet['sex']) ?>
                                        (<?= esc($pet['age']) ?> años)<br>
                                        <small>Perfil: <?= esc($pet['profile']) ?></small><br>
                                        <small>Peso: <?= esc($pet['weight']) ?> <?= esc($pet['weight_unit']) ?></small>
                                    </div>
                                    <span class="badge bg-info rounded-pill"><?= esc($pet['specie_id']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No se encontraron mascotas.</p>
                    <?php endif; ?>

                    <!-- Pruebas Encontradas -->
                    <h5 class="mb-3">Pruebas Encontradas</h5>
                    <?php if (!empty($tests)): ?>
                        <div class="list-group mb-4">
                            <?php foreach ($tests as $test): ?>
                                <a href="#"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Fecha de Prueba:</strong> <?= esc($test['test_date']) ?><br>
                                        <small>Notas: <?= esc($test['notes_test']) ?></small>
                                    </div>
                                    <span class="badge bg-success rounded-pill"><?= esc($test['id']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No se encontraron pruebas.</p>
                    <?php endif; ?>

                    <!-- Prescripciones Encontradas -->
                    <h5 class="mb-3">Prescripciones Encontradas</h5>
                    <?php if (!empty($prescriptions)): ?>
                        <div class="list-group mb-4">
                            <?php foreach ($prescriptions as $prescription): ?>
                                <a href="#"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Inicio:</strong> <?= esc($prescription['start_date']) ?><br>
                                        <strong>Fin:</strong> <?= esc($prescription['end_date']) ?><br>
                                        <small>Notas: <?= esc($prescription['notes']) ?></small>
                                    </div>
                                    <span class="badge bg-warning rounded-pill"><?= esc($prescription['id']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No se encontraron prescripciones.</p>
                    <?php endif; ?>

                    <!-- Vacunas Encontradas -->
                    <h5 class="mb-3">Vacunas Encontradas</h5>
                    <?php if (!empty($vaccines)): ?>
                        <div class="list-group mb-4">
                            <?php foreach ($vaccines as $vaccine): ?>
                                <a href="#"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Fecha de Vacunación:</strong> <?= esc($vaccine['vaccine_date']) ?><br>
                                        <small>Notas: <?= esc($vaccine['notes']) ?></small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill"><?= esc($vaccine['id']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No se encontraron vacunas.</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>