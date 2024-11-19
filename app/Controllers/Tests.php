<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\TestsModel;

class Tests extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->TestsModel = new TestsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Tests',
         'host' => site_url('tests/')
      ];
      echo view('admin/tests/list', $data);
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

         $recordsTotal = $this->TestsModel->countTotal();
         $data = $this->TestsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->TestsModel->countFilter($search);

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
      $data = [];

      echo view('admin/tests/form', $data);
   }

   public function create()
   {
      $request = [
         'code' => $this->request->getPost('code'),
         'name' => $this->request->getPost('name'),
         'description' => $this->request->getPost('description'),
         'created_at' => $this->request->getPost('created_at'),
         'updated_at' => $this->request->getPost('updated_at'),
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
            $insert = $this->TestsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->TestsModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }



   public function autocomplete()
   {
      // Recoge los datos enviados vía POST
      $type = $this->request->getPost('type');
      $name_startsWith = $this->request->getPost('name_startsWith');

      // Llama al método del modelo para obtener los productos
      $data = $this->TestsModel->getTestsForAutocomplete($type, $name_startsWith);

      // Devuelve los resultados como JSON
      return $this->response->setJSON($data);
   }

   public function show($id = null)
   {
      try {
         $data = $this->TestsModel->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Code</th><td>' . $data['code'] . '</td></tr>';
            $table .= '<tr><th>Name</th><td>' . $data['name'] . '</td></tr>';
            $table .= '<tr><th>Description</th><td>' . $data['description'] . '</td></tr>';
            $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '<tr><th>Updated At</th><td>' . $data['updated_at'] . '</td></tr>';
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
         $data = $this->TestsModel->find($id);

         if ($data) {
            $data = [
               'data_tests' => $data
            ];

            echo view('admin/tests/form', $data);
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
         'code' => $this->request->getPost('code'),
         'name' => $this->request->getPost('name'),
         'description' => $this->request->getPost('description'),
         'created_at' => $this->request->getPost('created_at'),
         'updated_at' => $this->request->getPost('updated_at'),
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
            $update = $this->TestsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->TestsModel->errors());
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
         $data = $this->TestsModel->find($id);
         if ($data) {
            $this->TestsModel->delete($id);
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
         'code' => [
            'label' => 'Code',
            'rules' => 'required|string|max_length[150]'
         ],
         'name' => [
            'label' => 'Name',
            'rules' => 'required|string|max_length[250]'
         ],
         'description' => [
            'label' => 'Description',
            'rules' => 'required|string'
         ],
         'created_at' => [
            'label' => 'Created At',
            'rules' => 'required|string'
         ],
         'updated_at' => [
            'label' => 'Updated At',
            'rules' => 'required|string'
         ],
      ]);
   }
}
