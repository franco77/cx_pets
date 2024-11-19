<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PrescriptionDetailsModel;
use App\Models\PrescriptionsModel;

class PrescriptionDetails extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PrescriptionDetailsModel = new PrescriptionDetailsModel;
      $this->PrescriptionsModel = new PrescriptionsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Prescription Details',
         'host' => site_url('admin/prescriptiondetails/')
      ];
      echo view('admin/prescription_details/list', $data);
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

         $recordsTotal = $this->PrescriptionDetailsModel->countTotal();
         $data = $this->PrescriptionDetailsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PrescriptionDetailsModel->countFilter($search);

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
         'data_prescriptions' => $this->PrescriptionsModel->findAll(),
      ];

      echo view('admin/prescription_details/form', $data);
   }

   public function create()
   {
      $request = [
         'prescription_id' => $this->request->getPost('prescription_id'),
         'product_name' => $this->request->getPost('product_name'),
         'dosage' => $this->request->getPost('dosage'),
         'frequency' => $this->request->getPost('frequency'),
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
            $insert = $this->PrescriptionDetailsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->PrescriptionDetailsModel->errors());
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
         $data = $this->PrescriptionDetailsModel->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id')
            ->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Prescription Id</th><td>' . $data['prescription_id'] . '</td></tr>';
            $table .= '<tr><th>Product Name</th><td>' . $data['product_name'] . '</td></tr>';
            $table .= '<tr><th>Dosage</th><td>' . $data['dosage'] . '</td></tr>';
            $table .= '<tr><th>Frequency</th><td>' . $data['frequency'] . '</td></tr>';
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
         $data = $this->PrescriptionDetailsModel->find($id);

         if ($data) {
            $data = [
               'data_prescriptions' => $this->PrescriptionsModel->findAll(),
               'data_prescription_details' => $data
            ];

            echo view('admin/prescription_details/form', $data);
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
         'prescription_id' => $this->request->getPost('prescription_id'),
         'product_name' => $this->request->getPost('product_name'),
         'dosage' => $this->request->getPost('dosage'),
         'frequency' => $this->request->getPost('frequency'),
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
            $update = $this->PrescriptionDetailsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->PrescriptionDetailsModel->errors());
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
         $data = $this->PrescriptionDetailsModel->find($id);
         if ($data) {
            $this->PrescriptionDetailsModel->delete($id);
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
         'prescription_id' => [
            'label' => 'Prescription Id',
            'rules' => 'required|numeric'
         ],
         'product_name' => [
            'label' => 'Product Name',
            'rules' => 'required|string|max_length[255]'
         ],
         'dosage' => [
            'label' => 'Dosage',
            'rules' => 'required|string|max_length[255]'
         ],
         'frequency' => [
            'label' => 'Frequency',
            'rules' => 'required|string|max_length[255]'
         ],
      ]);
   }
}
