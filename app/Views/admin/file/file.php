<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>

<div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <!-- Left Sidebar -->
                        <div class="col-lg-3">
                            <div class="btn-group d-block mb-3">
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-plus"></i> Create New
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item" href="#" id="createFolderBtn"><i
                                                class="far fa-folder-open me-2"></i>Folder</a></li>
                                    <li><a class="dropdown-item" href="#" id="uploadFileBtn"><i
                                                class="fas fa-upload me-2"></i>Upload File</a></li>
                                </ul>
                            </div>

                            <div class="list-group border-0" id="folderList">
                                <!-- Folders will be loaded here dynamically -->
                            </div>

                            <div class="mt-4">
                                <h6 class="text-uppercase fw-bold">Storage</h6>
                                <div class="progress mb-2" style="height: 10px;">
                                    <div id="storageProgressBar" class="progress-bar bg-success" role="progressbar"
                                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <span id="storageUsed"></span> (<span id="storagePercentage">0</span>%) of 10 GB
                                    used
                                </small>
                            </div>
                        </div>
                        <!-- End Left Sidebar -->

                        <!-- Right Content -->
                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="app-search">
                                    <form>
                                        <div class="input-group">
                                            <input type="text" class="form-control border-end-0"
                                                placeholder="Search files...">
                                            <button class="btn btn-outline-secondary" type="button"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary"><i
                                            class="fas fa-list"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i
                                            class="fas fa-th-large"></i></button>
                                </div>
                            </div>

                            <h5 class="mb-3">Files</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fileList">
                                        <!-- Files will be loaded here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- End Right Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal for creating folder -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Create New Folder or Subfolder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createFolderForm">
                    <div class="mb-3">
                        <label for="parentFolder" class="form-label">Select Parent Folder</label>
                        <select class="form-select" id="parentFolder">
                            <option value="">Root</option>
                            <!-- Folders will be dynamically loaded here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="newFolderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="newFolderName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Folder</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for uploading file -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadFileForm">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Choose File</label>
                        <input type="file" class="form-control" id="fileInput" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<!-- Agregar script de SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let currentFolder = ''; // Current folder path

    loadFolders(currentFolder); // Load initial folders (in root)
    loadFiles(currentFolder); // Load initial files (in root)

    function normalizePath(path) {
        // Ensure the path starts and ends with a slash
        return '/' + path.replace(/^\/+|\/+$/g, '').replace(/\/+/g, '/') + '/';
    }

    // Show modal for creating folders
    $('#createFolderBtn').on('click', function(e) {
        e.preventDefault();
        loadParentFolders(); // Load available folders in the modal
        $('#createFolderModal').modal('show');
    });

    // Show modal for uploading files
    $('#uploadFileBtn').on('click', function(e) {
        e.preventDefault();
        $('#uploadFileModal').modal('show');
    });

    // Handle form submission for creating folders
    $('#createFolderForm').on('submit', function(e) {
        e.preventDefault();
        $('button[type="submit"]').prop('disabled', true);
        createFolder();
    });

    // Handle form submission for uploading files
    $('#uploadFileForm').on('submit', function(e) {
        e.preventDefault();
        $('button[type="submit"]').prop('disabled', true);
        uploadFile();
    });

    async function loadParentFolders() {
        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/getFolders') ?>',
                method: 'GET'
            });
            $('#parentFolder').empty().append('<option value="">Root</option>');
            response.forEach(function(folder) {
                $('#parentFolder').append(`<option value="${folder}">${folder}</option>`);
            });
        } catch (error) {
            console.error('Error loading folders:', error);
        }
    }

    async function createFolder() {
        const folderName = $('#newFolderName').val();
        const parentFolder = normalizePath($('#parentFolder').val());

        if (folderName.trim() === "") {
            Swal.fire("Error", "Folder name cannot be empty", "error");
            $('button[type="submit"]').prop('disabled', false);
            return;
        }

        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/createFolder') ?>',
                method: 'POST',
                data: {
                    folderName: folderName,
                    parentFolder: parentFolder
                }
            });

            if (response.success) {
                Swal.fire("Success", response.message, "success");
                loadFolders(currentFolder);
                loadFiles(currentFolder);
                $('#newFolderName').val('');
                $('#createFolderModal').modal('hide');
            } else {
                Swal.fire("Error", response.message, "error");
            }
        } catch (error) {
            Swal.fire("Error", "There was an error processing your request.", "error");
        } finally {
            $('button[type="submit"]').prop('disabled', false);
        }
    }

    async function loadFolders(folderPath = '') {
        try {
            const response = await $.ajax({
                url: `<?= base_url('FileManager/getFolders') ?>?folder=${encodeURIComponent(normalizePath(folderPath))}`,
                method: 'GET'
            });
            $('#folderList').empty();

            if (folderPath !== '') {
                $('#folderList').append(`
                    <a href="#" class="list-group-item list-group-item-action go-back" data-folder="${getParentFolderPath(folderPath)}">
                        <i class="fas fa-arrow-left me-2"></i>.. Go Back
                    </a>
                `);
            }

            response.forEach(function(folder) {
                $('#folderList').append(`
                    <a href="#" class="list-group-item list-group-item-action folder-item d-flex justify-content-between align-items-center" data-folder="${normalizePath(folderPath + folder)}">
                        <div>
                            <i class="fas fa-folder me-2"></i>${folder}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-warning rename-folder" data-folder="${normalizePath(folderPath + folder)}" style="padding: 4px 7px;" aria-label="Rename folder">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-folder" data-folder="${normalizePath(folderPath + folder)}" style="padding: 4px 7px;" aria-label="Delete folder">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </a>
                `);
            });

            attachFolderEventListeners();
        } catch (error) {
            console.error('Error loading folders:', error);
        }
    }

    async function loadFiles(folder = '') {
        folder = normalizePath(folder);

        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/getFiles') ?>',
                method: 'GET',
                data: {
                    folder: folder
                }
            });
            $('#fileList').empty();

            if (!response.success) {
                $('#fileList').append('<tr><td colspan="3">Error: ' + response.message + '</td></tr>');
                return;
            }

            if (response.data.length === 0) {
                $('#fileList').append('<tr><td colspan="3">No files found.</td></tr>');
            } else {
                response.data.forEach(function(file) {
                    if (file.type === 'folder') {
                        $('#fileList').append(`
            <tr>
                <td>${file.name}</td>
                <td>${file.type}</td>
                <td><div class="pe-1"></div><i class="far fa-folder"></i></div></td> <!-- No mostrar acciones para las carpetas -->
            </tr>
        `);
                    } else {
                        $('#fileList').append(`
            <tr>
                <td>${file.name}</td>
                <td>${file.type}</td>
                <td>
                    <button class="btn btn-sm btn-danger delete-file" data-file="${file.name}" aria-label="Delete file"><i class="fas fa-trash"></i></button>
                    <a href="<?= base_url('FileManager/downloadFile') ?>?folder=${encodeURIComponent(folder)}&fileName=${encodeURIComponent(file.name)}" class="btn btn-sm btn-primary" aria-label="Download file">
                        <i class="fas fa-download"></i>
                    </a>
                </td>
            </tr>
        `);
                    }
                });
            }

            attachFileEventListeners();
        } catch (error) {
            console.error('Error loading files:', error);
            $('#fileList').empty().append(
                '<tr><td colspan="3">Error loading files. Check console for details.</td></tr>');
        }
    }

    async function uploadFile() {
        const fileInput = $('#fileInput')[0];
        const file = fileInput.files[0];
        if (!file) {
            Swal.fire("Error", "Please select a file to upload.", "error");
            $('button[type="submit"]').prop('disabled', false);
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder', currentFolder);

        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/uploadFile') ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });

            if (response.success) {
                Swal.fire("Success", response.message, "success");
                loadFiles(currentFolder);
                fileInput.value = '';
                $('#uploadFileModal').modal('hide');
            } else {
                Swal.fire("Error", response.message, "error");
            }
        } catch (error) {
            Swal.fire("Error", "There was an error processing your request.", "error");
        } finally {
            $('button[type="submit"]').prop('disabled', false);
        }
    }

    function attachFolderEventListeners() {
        $('.folder-link, .folder-item').off('click').on('click', function(e) {
            e.preventDefault();
            currentFolder = normalizePath($(this).data('folder'));
            loadFolders(currentFolder);
            loadFiles(currentFolder);
            $('.folder-item').removeClass('active');
            $(this).addClass('active');
        });

        $('.go-back').off('click').on('click', function(e) {
            e.preventDefault();
            currentFolder = $(this).data('folder');
            loadFolders(currentFolder);
            loadFiles(currentFolder);
        });

        $('.delete-folder').off('click').on('click', function(e) {
            e.stopPropagation();
            const folderName = $(this).data('folder');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => deleteFolder(folderName)
            });
        });

        $('.rename-folder').off('click').on('click', function(e) {
            e.stopPropagation();
            const oldName = $(this).data('folder');
            Swal.fire({
                title: 'Rename Folder',
                input: 'text',
                inputValue: oldName.split('/').pop(),
                showCancelButton: true,
                confirmButtonText: 'Rename',
                showLoaderOnConfirm: true,
                preConfirm: (newName) => renameFolder(oldName, newName)
            });
        });
    }

    function attachFileEventListeners() {
        $('.delete-file').off('click').on('click', function() {
            const fileName = $(this).data('file');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => deleteFile(fileName)
            });
        });
    }

    function getParentFolderPath(folderPath) {
        const pathArray = folderPath.split('/').filter(Boolean);
        pathArray.pop();
        return pathArray.length > 0 ? '/' + pathArray.join('/') + '/' : '/';
    }

    async function deleteFolder(folderName) {
        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/deleteFolder') ?>',
                method: 'POST',
                data: {
                    folderName: folderName
                }
            });

            if (response.success) {
                Swal.fire("Deleted", response.message, "success");
                if (currentFolder.startsWith(folderName)) {
                    currentFolder = getParentFolderPath(folderName);
                }
                loadFolders(currentFolder);
                loadFiles(currentFolder);
            } else {
                Swal.fire("Error", response.message, "error");
            }
        } catch (error) {
            Swal.fire("Error", "There was an error deleting the folder.", "error");
        }
    }

    async function deleteFile(fileName) {
        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/deleteFile') ?>',
                method: 'POST',
                data: {
                    fileName: fileName,
                    folder: currentFolder
                }
            });

            if (response.success) {
                Swal.fire("Deleted", response.message, "success");
                loadFiles(currentFolder);
            } else {
                Swal.fire("Error", response.message, "error");
            }
        } catch (error) {
            Swal.fire("Error", "There was an error deleting the file.", "error");
        }
    }

    async function renameFolder(oldName, newName) {
        try {
            const response = await $.ajax({
                url: '<?= base_url('FileManager/renameFolder') ?>',
                method: 'POST',
                data: {
                    oldName: oldName,
                    newName: newName
                }
            });

            if (response.success) {
                Swal.fire("Success", response.message, "success");
                loadFolders(currentFolder);
                loadFiles(currentFolder);
            } else {
                Swal.fire("Error", response.message, "error");
            }
        } catch (error) {
            Swal.fire("Error", "There was an error renaming the folder.", "error");
        }
    }


});
</script>


<script>
$(document).ready(function() {
    const maxStorage = 15 * 1024 * 1024 * 1024; // 15 GB in bytes

    $.ajax({
        url: '<?= base_url('FileManager/getTotalSize') ?>',
        method: 'GET',
        success: function(response) {
            // Asegúrate de que 'totalSizeInBytes' esté presente y sea un número
            const totalUsed = parseInt(response.totalSizeInBytes, 10);

            if (isNaN(totalUsed)) {
                console.error("Invalid size in bytes returned from server.");
                $('#storageUsed').text('Error');
                $('#storagePercentage').text('0');
                return;
            }

            // Calcular el porcentaje de almacenamiento utilizado
            const percentageUsed = ((totalUsed / maxStorage) * 100).toFixed(2);

            // Convertir el tamaño usado a un formato legible
            const formattedUsed = response.totalSizeFormatted || 'Unknown size';

            // Actualizar los elementos de la interfaz
            $('#storageProgressBar').css('width', percentageUsed + '%');
            $('#storageProgressBar').attr('aria-valuenow', percentageUsed);
            $('#storagePercentage').text(percentageUsed);
            $('#storageUsed').text(formattedUsed);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching storage data:', error);
            $('#storageUsed').text('Error');
            $('#storagePercentage').text('0');
        }
    });
});
</script>

<?= $this->endSection() ?>