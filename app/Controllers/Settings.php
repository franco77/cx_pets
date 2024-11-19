<?php

namespace App\Controllers;

use App\Models\SettingsModel;
use App\Controllers\BaseController;
use App\Models\CurrencyModel;

class Settings extends BaseController
{
    function __construct()
    {
        $this->SettingsModel = new SettingsModel();
        $this->CurrencyModel = new CurrencyModel();
    }

    public function index()
    {
        $data = [
            'controller' => 'settings',
            'title' => 'Ajustes',
            'all_currency' => $this->CurrencyModel->findAll(),
        ];

        return view('admin/setting/settings', $data);
    }

    public function dataCompanySettings()
    {
        $settings = ["siteName", "address", "email", "money", "theme", "phone", "sidebar", "vat", "corporateColor"];
        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if ($value || $value === "0") {
                $this->SettingsModel->save_setting($setting, $value);
            }
        }

        session()->setFlashdata('msg', 'Los ajustes se guardaron correctamente.');
        return $this->response->setJSON([
            'status' => 'success',
            'message' => session()->getFlashdata('msg')
        ]);
    }

    public function formatsSettings()
    {
        $settings = ["date_format", "number_format", "currency_symbol"];
        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if ($value || $value === "0") {
                $this->SettingsModel->save_setting($setting, $value);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Ajustes de formato guardados exitosamente.']);
    }

    public function otherSettings()
    {
        $settings = ["inventory", "footer_voucher", "footer_invoice", "templateAppoint"];
        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if ($value || $value === "0") {
                $this->SettingsModel->save_setting($setting, $value);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Otros ajustes guardados exitosamente.']);
    }

    public function upload()
    {
        $rules = [
            'file' => [
                'rules' => 'uploaded[file]|mime_in[file,image/jpg,image/jpeg,image/gif,image/png]|max_size[file,4096]',
                'errors' => [
                    'uploaded' => 'Debe seleccionar un archivo para subir.',
                    'mime_in' => 'El tipo de archivo es inválido. Tipos permitidos: jpg, jpeg, gif, png.',
                    'max_size' => 'El tamaño del archivo supera el máximo permitido de 4MB.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }

        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $newFileName = $file->getRandomName();

            try {
                $this->imageService->withFile($file->getTempName())
                    ->resize(200, 200, true, 'height')
                    ->save(FCPATH . 'uploads/logo/' . $newFileName);

                $id = 13;
                $fileData = ['value' => $newFileName];
                $this->SettingsModel->update($id, $fileData);

                return $this->response->setJSON(['status' => 'success', 'message' => 'El archivo ha sido subido exitosamente.']);
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al procesar la imagen: ' . $e->getMessage()]);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'El archivo no es válido o ya ha sido movido.']);
        }
    }
}
