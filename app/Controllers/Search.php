<?php

namespace App\Controllers;

use App\Models\OwnersModel;
use App\Models\PetsModel;
use App\Models\PetTestsModel;
use App\Models\PrescriptionsModel;
use App\Models\VaccinesModel;

class Search extends BaseController
{
    public function index()
    {
        // Muestra la vista del buscador
        return view('admin/search/search');
    }

    public function search()
    {
        // Obtener los parámetros de búsqueda del formulario
        $query = $this->request->getGet('query');

        // Modelos
        $OwnersModel = new OwnersModel();
        $PetsModel = new PetsModel();
        $PetTestsModel = new PetTestsModel();
        $PrescriptionsModel = new PrescriptionsModel();
        $VaccinesModel = new VaccinesModel();

        // Búsqueda en varias tablas relacionadas usando LIKE
        $owners = $OwnersModel->like('first_name', $query)
            ->orLike('last_name', $query)
            ->orLike('email', $query)
            ->findAll();

        $pets = $PetsModel->like('name', $query)
            ->orLike('profile', $query)
            ->findAll();

        $tests = $PetTestsModel->like('notes_test', $query)
            ->orLike('test_date', $query)
            ->findAll();

        $prescriptions = $PrescriptionsModel->like('notes', $query)
            ->orLike('start_date', $query)
            ->findAll();

        $vaccines = $VaccinesModel->like('notes', $query)
            ->orLike('vaccine_date', $query)
            ->findAll();

        // Enviar los resultados a la vista
        return view('admin/search/search', [
            'title' =>  'Búsqueda',
            'query' => $query,
            'owners' => $owners,
            'pets' => $pets,
            'tests' => $tests,
            'prescriptions' => $prescriptions,
            'vaccines' => $vaccines,
        ]);
    }
}
