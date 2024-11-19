<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\OwnersModel;
use App\Models\PetsModel;

class Owners extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->OwnersModel = new OwnersModel;
      $this->PetsModel = new PetsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Owners',
         'host' => site_url('owners/')
      ];
      echo view('admin/owners/list', $data);
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

         $recordsTotal = $this->OwnersModel->countTotal();
         $data = $this->OwnersModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->OwnersModel->countFilter($search);

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

      echo view('admin/owners/form', $data);
   }

   public function create()
   {
      $request = [
         'ic' => $this->request->getPost('ic'),
         'first_name' => $this->request->getPost('first_name'),
         'last_name' => $this->request->getPost('last_name'),
         'email' => $this->request->getPost('email'),
         'address' => $this->request->getPost('address'),
         'phone' => $this->request->getPost('phone'),
         'profile' => $this->request->getPost('profile'),
         'created_at' => date('Y-m-d H:i:s'),
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
            $insert = $this->OwnersModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->OwnersModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }


   public function getPetsByOwner($owner_id)
   {
      // Obtener las mascotas asociadas al dueño
      $pets = $this->PetsModel->where('owner_id', $owner_id)->findAll();

      return $this->response->setJSON($pets);
   }

   public function show($id = null)
   {
      try {
         $data = $this->OwnersModel->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Ic</th><td>' . $data['ic'] . '</td></tr>';
            $table .= '<tr><th>First Name</th><td>' . $data['first_name'] . '</td></tr>';
            $table .= '<tr><th>Last Name</th><td>' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Email</th><td>' . $data['email'] . '</td></tr>';
            $table .= '<tr><th>Address</th><td>' . $data['address'] . '</td></tr>';
            $table .= '<tr><th>Phone</th><td>' . $data['phone'] . '</td></tr>';
            $table .= '<tr><th>Profile</th><td>' . $data['profile'] . '</td></tr>';
            $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '<tr><th>Updated At</th><td>' . $data['updated_at'] . '</td></tr>';
            $table .= '</table>';
            return $this->respond($table);
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
         $data = $this->OwnersModel->find($id);

         if ($data) {
            $data = [
               'data_owners' => $data
            ];

            echo view('admin/owners/form', $data);
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
         'ic' => $this->request->getPost('ic'),
         'first_name' => $this->request->getPost('first_name'),
         'last_name' => $this->request->getPost('last_name'),
         'email' => $this->request->getPost('email'),
         'address' => $this->request->getPost('address'),
         'phone' => $this->request->getPost('phone'),
         'profile' => $this->request->getPost('profile'),
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
            $update = $this->OwnersModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->OwnersModel->errors());
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
         $data = $this->OwnersModel->find($id);
         if ($data) {
            $this->OwnersModel->delete($id);
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
      $id = $this->request->getPost('id');
      $this->validation->setRules([
         'ic' => [
            'label' => 'Ic',
            'rules' => 'required|is_unique[owners.ic,id,' . $id . ']',
         ],
         'first_name' => [
            'label' => 'First Name',
            'rules' => 'required|string|max_length[50]'
         ],
         'last_name' => [
            'label' => 'Last Name',
            'rules' => 'required|string|max_length[50]'
         ],
         'email' => [
            'label' => 'Email',
            'rules' => 'required|string|max_length[255]'
         ],
         'address' => [
            'label' => 'Address',
            'rules' => 'required|string|max_length[100]'
         ],
         'phone' => [
            'label' => 'Phone',
            'rules' => 'required|string|max_length[20]'
         ],
         'profile' => [
            'label' => 'Profile',
            'rules' => 'required|string'
         ]
      ]);
   }
}
