<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductsModel;
use App\Models\VendorsModel;

class Products extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->ProductsModel = new ProductsModel;
      $this->VendorsModel = new VendorsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Products',
         'host' => site_url('products/')
      ];
      echo view('admin/products/list', $data);
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

         $recordsTotal = $this->ProductsModel->countTotal();
         $data = $this->ProductsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->ProductsModel->countFilter($search);

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


   public function autocomplete()
   {
      // Recoge los datos enviados vía POST
      $type = $this->request->getPost('type');
      $name_startsWith = $this->request->getPost('name_startsWith');

      // Llama al método del modelo para obtener los productos
      $data = $this->ProductsModel->getProductsForAutocomplete($type, $name_startsWith);

      // Devuelve los resultados como JSON
      return $this->response->setJSON($data);
   }

   public function new()
   {
      $data = [
         'data_vendors' => $this->VendorsModel->findAll(),
      ];

      echo view('admin/products/form', $data);
   }

   public function create()
   {
      $request = [
         'productName' => $this->request->getPost('productName'),
         'productLine' => $this->request->getPost('productLine'),
         'productVendor' => $this->request->getPost('productVendor'),
         'productDescription' => $this->request->getPost('productDescription'),
         'quantityInStock' => $this->request->getPost('quantityInStock'),
         'buyPrice' => $this->request->getPost('buyPrice'),
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
            $insert = $this->ProductsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->ProductsModel->errors());
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
         $data = $this->ProductsModel->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Name</th><td>' . $data['productName'] . '</td></tr>';
            $table .= '<tr><th>Categoría</th><td>' . $data['productLine'] . '</td></tr>';
            $table .= '<tr><th>Vendor</th><td>' . $data['productVendor'] . '</td></tr>';
            $table .= '<tr><th>Description</th><td>' . $data['productDescription'] . '</td></tr>';
            $table .= '<tr><th>Quantity In Stock</th><td>' . $data['quantityInStock'] . '</td></tr>';
            $table .= '<tr><th>Price</th><td>' . $data['buyPrice'] . '</td></tr>';
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
         $data = $this->ProductsModel->find($id);

         if ($data) {
            $data = [
               'data_vendors' => $this->VendorsModel->findAll(),
               'data_products' => $data
            ];

            echo view('admin/products/form', $data);
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
         'productName' => $this->request->getPost('productName'),
         'productLine' => $this->request->getPost('productLine'),
         'productVendor' => $this->request->getPost('productVendor'),
         'productDescription' => $this->request->getPost('productDescription'),
         'quantityInStock' => $this->request->getPost('quantityInStock'),
         'buyPrice' => $this->request->getPost('buyPrice'),
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
            $update = $this->ProductsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->ProductsModel->errors());
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
         $data = $this->ProductsModel->find($id);
         if ($data) {
            $this->ProductsModel->delete($id);
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
         'productName' => [
            'label' => 'ProductName',
            'rules' => 'required|string|max_length[70]'
         ],
         'productLine' => [
            'label' => 'ProductLine',
            'rules' => 'required|string|max_length[50]'
         ],
         'productScale' => [
            'label' => 'ProductScale',
            'rules' => 'required|string|max_length[10]'
         ],
         'productVendor' => [
            'label' => 'ProductVendor',
            'rules' => 'required|string|max_length[50]'
         ],
         'productDescription' => [
            'label' => 'ProductDescription',
            'rules' => 'required|string'
         ],
         'quantityInStock' => [
            'label' => 'QuantityInStock',
            'rules' => 'required|numeric'
         ],
         'buyPrice' => [
            'label' => 'BuyPrice',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'MSRP' => [
            'label' => 'MSRP',
            'rules' => 'required|decimal|max_length[10]'
         ],
      ]);
   }
}
