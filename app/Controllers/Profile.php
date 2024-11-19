<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use IonAuth\Libraries\IonAuth;

class Profile extends BaseController
{
    protected $ionAuth;
    protected $validation;

    public function __construct()
    {

        $this->ionAuth = new IonAuth();

        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data = [
            'user' => $this->ionAuth->user()->row(),
            'title' => 'Perfil',
        ];
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('auth/login');
        }
        return view('admin/profile/profile', $data);
    }

    public function update()
    {
        $user = $this->ionAuth->user()->row();

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'company' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'company' => $this->request->getPost('company'),
        ];

        if ($this->ionAuth->update($user->id, $data)) {
            return redirect()->to('profile')->with('message', 'Perfil actualizado exitosamente');
        } else {
            return redirect()->back()->with('error', 'No se pudo actualizar el perfil')->withInput();
        }
    }

    public function updateAvatar()
    {

        if (!$this->ionAuth->loggedIn()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No estás autenticado']);
        }

        $validationRule = [
            'avatar' => [
                'label' => 'Avatar Image',
                'rules' => 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]|max_size[avatar,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $avatar = $this->request->getFile('avatar');

        $user = $this->ionAuth->user()->row();
        $userId = $user->id;

        if ($avatar->isValid() && !$avatar->hasMoved()) {

            $newName = $avatar->getRandomName();

            $tempPath = FCPATH . 'uploads/avatars/' . $newName;

            $avatar->move(FCPATH . 'uploads/avatars', $newName);

                $this->imageService->withFile($tempPath);
                $this->imageService->resize(400, 400, true, 'height');
                $this->imageService->save($tempPath);;

            if ($this->ionAuth->update($userId, ['avatar' => $newName])) {

                return $this->response->setJSON([
                    'status' => 'success',
                    'new_image_url' => base_url('uploads/avatars/' . $newName),
                ]);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo actualizar el avatar en la base de datos']);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al subir la imagen']);
        }
    }
}