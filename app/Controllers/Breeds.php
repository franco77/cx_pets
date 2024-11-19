<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\BreedsModel;
use App\Models\SpeciesModel;

class Breeds extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->BreedsModel = new BreedsModel;
      $this->SpeciesModel = new SpeciesModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Breeds',
         'host' => site_url('breeds/')
      ];
      echo view('admin/breeds/list', $data);
   }

   public function data()
   {
      try {
         $request = esc($this->request->getPost());
         $search = $request['search']['value'];
         $limit = $request['length'];
         $start = $request['start'];

         $orderIndex = $request['order'][0]['column'];
         $orderFields = $request['columns'][$orderIndex]['data'];
         $orderDir = $request['order'][0]['dir'];

         $recordsTotal = $this->BreedsModel->countTotal();
         $data = $this->BreedsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->BreedsModel->countFilter($search);

         $callback = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
         ];

         return $this->respond($callback);
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function new()
   {
      $data = [
         'data_species' => $this->SpeciesModel->findAll(),
      ];

      echo view('admin/breeds/form', $data);
   }

   public function create()
   {
      $request = [
         'id_specie' => $this->request->getPost('id_specie'),
         'name' => $this->request->getPost('name'),
      ];
      $this->rules();

      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            $insert = $this->BreedsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->BreedsModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         $data = $this->BreedsModel->select('breeds.id, breeds.id_specie, breeds.name, species.id AS sId, species.name AS spName')
            ->join('species', 'species.id = breeds.id_specie')
            ->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Specie</th><td>' . $data['spName'] . '</td></tr>';
            $table .= '<tr><th>Name</th><td>' . $data['name'] . '</td></tr>';
            $table .= '</table>';
            return $this->respond($table);;
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function edit($id = null)
   {
      try {
         $data = $this->BreedsModel->find($id);

         if ($data) {
            $data = [
               'data_species' => $this->SpeciesModel->findAll(),
               'data_breeds' => $data
            ];

            echo view('admin/breeds/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function update($id = null)
   {
      $request = [
         'id_specie' => $this->request->getPost('id_specie'),
         'name' => $this->request->getPost('name'),
      ];
      $this->rules();

      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            $update = $this->BreedsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->BreedsModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->BreedsModel->find($id);
         if ($data) {
            $this->BreedsModel->delete($id);
            return $this->respondDeleted([
               'status' => 200,
               'message' => 'Data deleted.'
            ]);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   private function rules()
   {
      $this->validation->setRules([
         'id_specie' => [
            'label' => 'Id Specie',
            'rules' => 'required|numeric'
         ],
         'name' => [
            'label' => 'Name',
            'rules' => 'required|string|max_length[70]'
         ],
      ]);
   }
}