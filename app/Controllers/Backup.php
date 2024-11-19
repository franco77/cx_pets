<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;

class Backup extends BaseController
{

    public function __construct() {}

    public function index()
    {
        return view('admin/backup/export', ['title' => 'Backup Management']);
    }

    public function exportDB()
    {
        $backupName = 'backup-on-' . date('Y-m-d-H-i-s') . '.sql';

        // Exportar base de datos
        if ($this->exportDatabase($backupName)) {
            return redirect()->back()->with('success', 'Backup generated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to generate backup');
        }
    }

    public function exportDatabase($backupName)
    {
        $tables = $this->db->listTables();
        $content = '';

        foreach ($tables as $table) {
            $structure = $this->db->query("SHOW CREATE TABLE `$table`")->getRowArray();
            $content .= "\n\n" . $structure['Create Table'] . ";\n\n";

            $rows = $this->db->query("SELECT * FROM `$table`")->getResultArray();

            if (!empty($rows)) {
                $content .= "INSERT INTO `$table` VALUES \n";
                $values = [];

                foreach ($rows as $row) {
                    $escapedValues = array_map(function ($value) {
                        return isset($value) ? '"' . addslashes($value) . '"' : 'NULL';
                    }, $row);
                    $values[] = "(" . implode(", ", $escapedValues) . ")";
                }

                $content .= implode(",\n", $values) . ";\n\n";
            }
        }

        $this->downloadBackup($backupName, $content);
    }


    private function downloadBackup($backupName, $content)
    {
        if (!empty($content)) {
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"$backupName\"");
            echo $content;
            exit;
        }

        return false;
    }
}
