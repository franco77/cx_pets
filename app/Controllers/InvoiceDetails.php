<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\InvoiceDetailsModel;
use App\Models\InvoicesModel;

class InvoiceDetails extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->InvoiceDetailsModel = new InvoiceDetailsModel;
      $this->InvoicesModel = new InvoicesModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Invoice Details',
         'host' => site_url('invoicedetails/')
      ];
      echo view('admin/invoice_details/list', $data);
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

         $recordsTotal = $this->InvoiceDetailsModel->countTotal();
         $data = $this->InvoiceDetailsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->InvoiceDetailsModel->countFilter($search);

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
         'data_invoices' => $this->InvoicesModel->findAll(),
      ];

      echo view('admin/invoice_details/form', $data);
   }

   public function create()
   {
      $request = [
         'invoice_id' => $this->request->getPost('invoice_id'),
         'product_id' => $this->request->getPost('product_id'),
         'product_name' => $this->request->getPost('product_name'),
         'quantity' => $this->request->getPost('quantity'),
         'price' => $this->request->getPost('price'),
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
            $insert = $this->InvoiceDetailsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->InvoiceDetailsModel->errors());
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
         $data = $this->InvoiceDetailsModel->select('invoice_details.id, invoice_details.invoice_id, invoice_details.product_id,invoice_details.product_name, invoice_details.quantity, invoice_details.price, invoices.id, invoices.client_id, invoices.date_invoice')
            ->join('invoices', 'invoices.id = invoice_details.invoice_id')
            ->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Product of Invoice #</th><td>' . $data['invoice_id'] . '</td></tr>';
            $table .= '<tr><th>Product Id</th><td>' . $data['product_id'] . '</td></tr>';
            $table .= '<tr><th>Product Name</th><td>' . $data['product_name'] . '</td></tr>';
            $table .= '<tr><th>Quantity</th><td>' . $data['quantity'] . '</td></tr>';
            $table .= '<tr><th>Price</th><td>' . $data['price'] . '</td></tr>';
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
         $data = $this->InvoiceDetailsModel->find($id);

         if ($data) {
            $data = [
               'data_invoices' => $this->InvoicesModel->findAll(),
               'data_invoice_details' => $data
            ];

            echo view('admin/invoice_details/form', $data);
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
         'invoice_id' => $this->request->getPost('invoice_id'),
         'product_id' => $this->request->getPost('product_id'),
         'product_name' => $this->request->getPost('product_name'),
         'quantity' => $this->request->getPost('quantity'),
         'price' => $this->request->getPost('price'),
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
            $update = $this->InvoiceDetailsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->InvoiceDetailsModel->errors());
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
         $data = $this->InvoiceDetailsModel->find($id);
         if ($data) {
            $this->InvoiceDetailsModel->delete($id);
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
         'invoice_id' => [
            'label' => 'Invoice Id',
            'rules' => 'required|numeric'
         ],
         'product_id' => [
            'label' => 'Product Id',
            'rules' => 'required|string|max_length[25]'
         ],
         'product_name' => [
            'label' => 'Product Name',
            'rules' => 'required|string|max_length[250]'
         ],
         'quantity' => [
            'label' => 'Quantity',
            'rules' => 'required|numeric'
         ],
         'price' => [
            'label' => 'Price',
            'rules' => 'required|decimal|max_length[10]'
         ],
      ]);
   }
}
