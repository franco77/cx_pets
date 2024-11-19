<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\TestDetailsModel;
use App\Models\TestsModel;
use App\Models\PetTestsModel;

class TestDetails extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->TestDetailsModel = new TestDetailsModel;
      $this->TestsModel = new TestsModel;
      $this->PetTestsModel = new PetTestsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Test Details',
         'host' => site_url('testdetails/')
      ];
      echo view('admin/test_details/list', $data);
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

         $recordsTotal = $this->TestDetailsModel->countTotal();
         $data = $this->TestDetailsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->TestDetailsModel->countFilter($search);

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
         'data_tests' => $this->TestsModel->findAll(),
         'data_pet_tests' => $this->PetTestsModel->findAll(),
      ];

      echo view('admin/test_details/form', $data);
   }

   public function create()
   {
      $request = [
         'pet_tests_id' => $this->request->getPost('pet_tests_id'),
         'test_id' => $this->request->getPost('test_id'),
         'notes' => $this->request->getPost('notes'),
         'result' => $this->request->getPost('result'),
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
            $insert = $this->TestDetailsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->TestDetailsModel->errors());
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
         $data = $this->TestDetailsModel->join('tests', 'tests.id = test_details.test_id')
            ->join('pet_tests', 'pet_tests.id = test_details.pet_tests_id')
            ->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Pet Tests Id</th><td>' . $data['pet_tests_id'] . '</td></tr>';
            $table .= '<tr><th>Test Id</th><td>' . $data['test_id'] . '</td></tr>';
            $table .= '<tr><th>Notes</th><td>' . $data['notes'] . '</td></tr>';
            $table .= '<tr><th>Result</th><td>' . $data['result'] . '</td></tr>';
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
         $data = $this->TestDetailsModel->find($id);

         if ($data) {
            $data = [
               'data_tests' => $this->TestsModel->findAll(),
               'data_pet_tests' => $this->PetTestsModel->findAll(),
               'data_test_details' => $data
            ];

            echo view('admin/test_details/form', $data);
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
         'pet_tests_id' => $this->request->getPost('pet_tests_id'),
         'test_id' => $this->request->getPost('test_id'),
         'notes' => $this->request->getPost('notes'),
         'result' => $this->request->getPost('result'),
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
            $update = $this->TestDetailsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->TestDetailsModel->errors());
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
         $data = $this->TestDetailsModel->find($id);
         if ($data) {
            $this->TestDetailsModel->delete($id);
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
         'pet_tests_id' => [
            'label' => 'Pet Tests Id',
            'rules' => 'required|numeric'
         ],
         'test_id' => [
            'label' => 'Test Id',
            'rules' => 'required|numeric'
         ],
         'notes' => [
            'label' => 'Notes',
            'rules' => 'required|string|max_length[250]'
         ],
         'result' => [
            'label' => 'Result',
            'rules' => 'required|string|max_length[250]'
         ],
      ]);
   }
}
