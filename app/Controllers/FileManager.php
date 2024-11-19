<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FileManager extends BaseController
{
    public function index()
    {
        $data = ['title' => 'File Manager'];
        return view('admin/file/file', $data);
    }

    public function getFolders()
    {
        // Obtener la carpeta actual desde la solicitud GET
        $currentFolder = $this->request->getGet('folder') ?? '';

        // Construir la ruta de la carpeta actual dentro del directorio de uploads
        $folderPath = rtrim(WRITEPATH . 'uploads/' . $currentFolder, '/') . '/';

        // Verificar si la ruta es un directorio válido
        if (!is_dir($folderPath)) {
            return $this->response->setJSON(['error' => 'Invalid folder path'])->setStatusCode(400);
        }

        // Utilizar glob para listar solo directorios dentro de la carpeta
        $folders = array_filter(glob($folderPath . '*', GLOB_ONLYDIR), 'is_dir');

        // Obtener los nombres de las carpetas y devolverlos en un formato adecuado
        $folderNames = array_map(function ($path) use ($currentFolder) {
            return basename($path) . '/';
        }, $folders);

        // Devolver las carpetas encontradas en formato JSON
        return $this->response->setJSON($folderNames);
    }


    public function getFiles()
    {
        $folder = $this->request->getGet('folder') ?? '';
        $folder = $this->normalizePath($folder);

        $basePath = WRITEPATH . 'uploads/';
        $fullPath = $basePath . $folder;

        if (!is_dir($fullPath)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Folder not found',
                'debug' => $fullPath
            ]);
        }

        $files = [];
        $directories = [];

        $items = scandir($fullPath);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $itemPath = $fullPath . '/' . $item;
            if (is_dir($itemPath)) {
                $directories[] = ['name' => $item, 'type' => 'folder'];
            } else {
                $files[] = ['name' => $item, 'type' => 'file'];
            }
        }

        $result = array_merge($directories, $files);

        return $this->response->setJSON([
            'success' => true,
            'data' => $result
        ]);
    }

    private function normalizePath($path)
    {
        // Remove leading and trailing slashes, replace backslashes with forward slashes
        $path = trim($path, '/');
        $path = str_replace('\\', '/', $path);

        // Ensure the path doesn't try to navigate above the base uploads directory
        $parts = explode('/', $path);
        $safeParts = [];
        foreach ($parts as $part) {
            if ($part === '..') {
                array_pop($safeParts);
            } elseif ($part !== '.') {
                $safeParts[] = $part;
            }
        }

        return implode('/', $safeParts);
    }





    public function createFolder()
    {
        $folderName = $this->request->getPost('folderName');
        $parentFolder = $this->request->getPost('parentFolder');

        $folderPath = WRITEPATH . 'uploads/' . $parentFolder . $folderName;

        if (is_dir($folderPath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Folder already exists in this location.']);
        }

        if (mkdir($folderPath, 0755, true)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Folder created successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create the folder.']);
        }
    }

    public function renameFolder()
    {
        $oldName = $this->request->getPost('oldName');
        $newName = $this->request->getPost('newName');
        $oldPath = WRITEPATH . 'uploads/' . $oldName;
        $newPath = WRITEPATH . 'uploads/' . dirname($oldName) . '/' . basename($newName);

        if (is_dir($oldPath) && !is_dir($newPath)) {
            if (rename($oldPath, $newPath)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Folder renamed successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Could not rename the folder']);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid folder name or destination']);
        }
    }

    public function deleteFolder()
    {
        $folderName = $this->request->getPost('folderName');
        $path = WRITEPATH . 'uploads/' . $folderName;

        if (is_dir($path)) {
            if ($this->deleteDirectory($path)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Folder deleted successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Could not delete the folder']);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Folder does not exist']);
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public function uploadFile()
    {
        $folder = $this->request->getPost('folder');
        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/' . $folder;

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($file->move($uploadPath, $newName)) {
                return $this->response->setJSON(['success' => true, 'message' => 'File uploaded successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Error uploading file']);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid file']);
        }
    }

    public function downloadFile()
    {
        $folder = $this->request->getGet('folder');
        $fileName = $this->request->getGet('fileName');
        $filePath = WRITEPATH . 'uploads/' . $folder . '/' . $fileName;

        if (file_exists($filePath)) {
            return $this->response->download($filePath, null)->setFileName($fileName);
        } else {
            return $this->response->setStatusCode(404, 'File Not Found');
        }
    }

    public function deleteFile()
    {
        $folder = $this->request->getPost('folder');
        $fileName = $this->request->getPost('fileName');
        $filePath = WRITEPATH . 'uploads/' . $folder . '/' . $fileName;

        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                return $this->response->setJSON(['success' => true, 'message' => 'File deleted successfully.']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete the file.']);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found.']);
        }
    }




    public function getTotalSize()
    {
        // Ruta de la carpeta uploads
        $uploadPath = WRITEPATH . 'uploads/';

        // Obtener el tamaño total de los archivos en la carpeta uploads
        $totalSize = $this->getFolderSize($uploadPath);

        // Convertir el tamaño a un formato legible (ejemplo: MB)
        $totalSizeFormatted = $this->formatSizeUnits($totalSize);

        // Verifica si $totalSize es un número válido
        if (!is_numeric($totalSize)) {
            return $this->response->setJSON(['error' => 'Invalid total size'], 400);
        }

        return $this->response->setJSON([
            'totalSizeInBytes' => $totalSize,
            'totalSizeFormatted' => $totalSizeFormatted
        ]);
    }

    /**
     * Función recursiva para calcular el tamaño total de los archivos en un directorio
     *
     * @param string $dir
     * @return int
     */
    private function getFolderSize($dir)
    {
        $size = 0;

        // Abrir el directorio
        foreach (scandir($dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    // Recursivamente obtener el tamaño de subdirectorios
                    $size += $this->getFolderSize($filePath);
                } else {
                    // Sumar el tamaño del archivo
                    $size += filesize($filePath);
                }
            }
        }

        return $size;
    }

    /**
     * Convertir bytes a un formato legible (KB, MB, GB)
     *
     * @param int $bytes
     * @return string
     */
    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
