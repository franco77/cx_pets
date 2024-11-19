<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\VaccineDetailsModel;
use App\Models\VaccinesModel;

class VaccineDetails extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->VaccineDetailsModel = new VaccineDetailsModel;
      $this->VaccinesModel = new VaccinesModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Vaccine Details',
         'host' => site_url('vaccinedetails/')
      ];
      echo view('admin/vaccine_details/list', $data);
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

         $recordsTotal = $this->VaccineDetailsModel->countTotal();
         $data = $this->VaccineDetailsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->VaccineDetailsModel->countFilter($search);

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
         'data_vaccines' => $this->VaccinesModel->findAll(),
      ];

      echo view('admin/vaccine_details/form', $data);
   }

   public function create()
   {
      $request = [
         'vaccine_id' => $this->request->getPost('vaccine_id'),
         'product_id' => $this->request->getPost('product_id'),
         'product_name' => $this->request->getPost('product_name'),
         'dosage' => $this->request->getPost('dosage'),
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
            $insert = $this->VaccineDetailsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->VaccineDetailsModel->errors());
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
         $data = $this->VaccineDetailsModel->join('vaccines', 'vaccines.id = vaccine_details.vaccine_id')
            ->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Vaccine Id</th><td>' . $data['vaccine_id'] . '</td></tr>';
            $table .= '<tr><th>Product Id</th><td>' . $data['product_id'] . '</td></tr>';
            $table .= '<tr><th>Product Name</th><td>' . $data['product_name'] . '</td></tr>';
            $table .= '<tr><th>Dosage</th><td>' . $data['dosage'] . '</td></tr>';
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
         $data = $this->VaccineDetailsModel->find($id);

         if ($data) {
            $data = [
               'data_vaccines' => $this->VaccinesModel->findAll(),
               'data_vaccine_details' => $data
            ];

            echo view('admin/vaccine_details/form', $data);
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
         'vaccine_id' => $this->request->getPost('vaccine_id'),
         'product_id' => $this->request->getPost('product_id'),
         'product_name' => $this->request->getPost('product_name'),
         'dosage' => $this->request->getPost('dosage'),
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
            $update = $this->VaccineDetailsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->VaccineDetailsModel->errors());
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
         $data = $this->VaccineDetailsModel->find($id);
         if ($data) {
            $this->VaccineDetailsModel->delete($id);
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
         'vaccine_id' => [
            'label' => 'Vaccine Id',
            'rules' => 'required|numeric'
         ],
         'product_id' => [
            'label' => 'Product Id',
            'rules' => 'required|string|max_length[150]'
         ],
         'product_name' => [
            'label' => 'Product Name',
            'rules' => 'required|string|max_length[255]'
         ],
         'dosage' => [
            'label' => 'Dosage',
            'rules' => 'required|string|max_length[255]'
         ],
      ]);
   }
}
