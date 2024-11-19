<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Controllers\BaseController;

class Calendar extends BaseController
{
    public function __construct()
    {
        $this->EventModel = new EventModel;
    }
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
        ];
        return view('admin/calendar/index', $data);
    }

    public function loadEvents()
    {
        $events = $this->EventModel->findAll();
        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['start'],
                'end' => $event['end'],
                'className' => $event['className']
            ];
        }

        return $this->response->setJSON($data);
    }

    // Añadir un nuevo evento
    public function addEvent()
    {
        $data = [
            'title' => $this->request->getPost('title'),
            'start' => $this->request->getPost('start'),
            'end' => $this->request->getPost('end'),
            'className' => $this->request->getPost('className')
        ];

        if ($this->EventModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    // Actualizar un evento existente
    public function updateEvent()
    {
        $id = $this->request->getPost('id');
        $data = [
            'title' => $this->request->getPost('title'),
            'className' => $this->request->getPost('className')
        ];

        if ($this->EventModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    // Actualizar las fechas de un evento cuando se arrastra o redimensiona
    public function updateEventDate()
    {
        $id = $this->request->getPost('id');
        $data = [
            'start' => $this->request->getPost('start'),
            'end' => $this->request->getPost('end')
        ];

        if ($this->EventModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    // Eliminar un evento
    public function deleteEvent()
    {
        $id = $this->request->getPost('id');

        if ($this->EventModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }
}
