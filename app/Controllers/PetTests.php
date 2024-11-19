<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PetTestsModel;
use App\Models\PetsModel;
use App\Models\TestDetailsModel;


class PetTests extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PetTestsModel = new PetTestsModel;
      $this->PetsModel = new PetsModel;
      $this->TestDetailsModel = new TestDetailsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Pet Tests',
         'host' => site_url('pettests/')
      ];
      echo view('admin/pet_tests/list', $data);
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

         $recordsTotal = $this->PetTestsModel->countTotal();
         $data = $this->PetTestsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PetTestsModel->countFilter($search);

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




   function printVoucherView($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }
      $data = $this->PetTestsModel->getTestDetails($id);
      if (!$data) {
         throw new \RuntimeException('Prescripción no encontrada.');
      }
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      $viewData = [
         'data' => $data,
      ];
      $html = view('admin/pet_tests/print', $viewData);
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      try {
         $dompdf->render();
      } catch (\Exception $e) {
         throw new \RuntimeException('Error al generar el PDF: ' . $e->getMessage());
      }
      $dompdf->stream('voucher_' . $id . '.pdf', ['Attachment' => 0]);
   }




   public function printVoucher($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }

      // Obtener los detalles del pago
      $data = $this->PetTestsModel->getTestDetails($id);

      if (!$data) {
         throw new \RuntimeException('Pago no encontrado.');
      }

      // Configuración de Dompdf
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      $viewData = ['data' => $data];
      $html = view('admin/pet_tests/print', $viewData); // Generar el HTML para el PDF

      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();

      // Ruta del archivo PDF en public/uploads/pdf/pagos/
      $directory = FCPATH . 'uploads/pdf/pet_tests/';  // FCPATH apunta a la carpeta public/
      $filePath = $directory . 'voucher_' . $id . '.pdf';

      // Verificar si la carpeta existe, si no, crearla
      if (!is_dir($directory)) {
         mkdir($directory, 0755, true); // Crear la carpeta si no existe
      }

      // Guardar el archivo PDF
      $output = $dompdf->output();
      if (file_put_contents($filePath, $output)) {
         log_message('info', 'PDF guardado en: ' . $filePath);
      } else {
         log_message('error', 'Error al guardar el PDF.');
      }

      return $filePath; // Devolver la ruta del archivo PDF
   }




   public function sendVoucherByEmail($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }

      // Generar el PDF y obtener la ruta
      $filePath = $this->printVoucher($id);

      // Obtener la información de la factura
      $data = $this->PetTestsModel->getTestDetails($id);

      if (!$data) {
         throw new \RuntimeException('Factura no encontrada.');
      }
      $empresaEmail = $data['email'];
      // Configurar el correo electrónico

      $filePath = $this->printVoucher($id);
      $this->email->setTo($empresaEmail);
      $this->email->setFrom(setting('App.email'));
      $this->email->setSubject('Resultados  - Tests #' . $id);
      $this->email->setMessage('Adjunto encontrarás el PDF con la información de la factura y los pagos realizados.');

      // Adjuntar el PDF
      if (file_exists($filePath)) {
         $this->email->attach($filePath);
      } else {
         log_message('error', 'El archivo PDF no existe en: ' . $filePath);
         return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontró el PDF para enviar.']);
      }

      // Enviar el correo
      if ($this->email->send()) {
         log_message('info', 'Correo enviado correctamente a: juanfranco561@gmail.com');
         return $this->response->setJSON(['status' => 'success', 'message' => 'Correo enviado correctamente.']);
      } else {
         // Obtener los errores del envío de correo
         $errorMessage = $this->email->printDebugger(['headers']);
         log_message('error', 'Error al enviar el correo: ' . $errorMessage);
         return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo enviar el correo.']);
      }
   }






   public function new()
   {
      $data = [
         'data_pets' => $this->PetsModel->findAll(),
      ];

      echo view('admin/pet_tests/form', $data);
   }

   public function create()
   {
      // Capturar los datos del formulario
      $request = [
         'pet_id' => $this->request->getPost('pet_id'),
         'test_date' => $this->request->getPost('test_date'),
         'notes_test' => $this->request->getPost('notes_test'),
         'created_at' => date('Y-m-d H:i:s'),
         'updated_at' => date('Y-m-d H:i:s')
      ];

      $this->rules(); // Aquí defines las reglas de validación

      // Validación de los datos principales
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Iniciar transacción
            $this->db->transStart();

            // 1. Insertar el registro principal en pet_tests
            $insert = $this->PetTestsModel->insert($request);

            if ($insert) {
               // Obtener el ID del registro recién creado
               $petTestId = $this->PetTestsModel->getInsertID();

               // 2. Insertar los detalles en test_details


               // Capturamos los arrays enviados del formulario
               $codes = $this->request->getPost('code');
               $names = $this->request->getPost('name');
               $notes = $this->request->getPost('notes');
               $results = $this->request->getPost('result');

               // Iterar sobre los detalles y realizar las inserciones
               for ($i = 0; $i < count($codes); $i++) {
                  if (!empty($codes[$i]) && !empty($names[$i])) {
                     $testDetailsData = [
                        'pet_tests_id' => $petTestId,
                        'code' => $codes[$i],
                        'name' => $names[$i],
                        'notes' => htmlspecialchars($notes[$i], ENT_QUOTES, 'UTF-8'),
                        'result' => $results[$i] ?? ''
                     ];

                     // Insertar el detalle
                     $this->TestDetailsModel->insert($testDetailsData);
                  }
               }

               // Completar la transacción
               $this->db->transComplete();

               // Verificar si la transacción fue exitosa
               if ($this->db->transStatus() === FALSE) {
                  return $this->fail('Error al crear los detalles. Por favor, intenta de nuevo.');
               }

               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created successfully.'
               ]);
            } else {
               return $this->fail($this->PetTestsModel->errors());
            }
         } catch (\Exception $e) {
            // Revertir transacción en caso de error
            $this->db->transRollback();
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         // 1. Obtener los datos principales de pet_tests junto con los detalles relacionados
         $data = $this->PetTestsModel
            ->select('pet_tests.*, pets.name AS pet_name, pets.sex, pets.age, pets.weight, pets.weight_unit, pets.profile, owners.first_name AS owner_first_name, owners.last_name AS owner_last_name, owners.email AS owner_email, owners.address AS owner_address, owners.phone AS owner_phone')
            ->join('pets', 'pets.id = pet_tests.pet_id')
            ->join('owners', 'owners.id = pets.owner_id')
            ->where('pet_tests.id', $id)
            ->first();  // Usamos first() porque queremos un solo registro para la mascota

         if ($data) {
            // 2. Obtener los detalles de test_details relacionados
            $testDetails = $this->db->table('test_details')
               ->where('pet_tests_id', $id)
               ->get()
               ->getResultArray();  // Obtener todos los detalles como un array

            // 3. Construir la tabla principal con los datos de la mascota y el dueño
            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th style="width: 18%;">Owner Name</th><td>' . $data['owner_first_name'] . ' ' . $data['owner_last_name'] . '</td></tr>';
            $table .= '<tr><th>Email</th><td>' . $data['owner_email'] . '</td></tr>';
            $table .= '<tr><th>Address</th><td>' . $data['owner_address'] . '</td></tr>';
            $table .= '<tr><th>Phone</th><td>' . $data['owner_phone'] . '</td></tr>';
            $table .= '</table><br>';
            $table .= '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th style="width: 18%;">Pet Name</th><td>' . $data['pet_name'] . '</td></tr>';
            $table .= '<tr><th>Sex</th><td>' . $data['sex'] . '</td></tr>';
            $table .= '<tr><th>Age</th><td>' . $data['age'] . '</td></tr>';
            $table .= '<tr><th>Weight</th><td>' . $data['weight'] . ' ' . $data['weight_unit'] . '</td></tr>';
            $table .= '<tr><th>Test Date</th><td>' . $data['test_date'] . '</td></tr>';
            $table .= '<tr><th></th><td>' . $data['notes_test'] . '</td></tr>';
            $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '<tr><th>Updated At</th><td>' . $data['updated_at'] . '</td></tr>';
            $table .= '</table>';

            // 4. Si hay detalles de los exámenes, mostrarlos en una tabla separada
            if (!empty($testDetails)) {
               $table .= '<h4>Test Details</h4>';
               $table .= '<table class="table table-sm table-bordered">';
               $table .= '<thead class="table-dark"><tr><th>Code</th><th>Test Name</th><th>Notes</th><th>Result</th></tr></thead>';
               $table .= '<tbody>';

               foreach ($testDetails as $detail) {
                  $table .= '<tr>';
                  $table .= '<td>' . $detail['code'] . '</td>';
                  $table .= '<td>' . $detail['name'] . '</td>';
                  $table .= '<td>' . $detail['notes'] . '</td>';
                  $table .= '<td>' . $detail['result'] . '</td>';
                  $table .= '</tr>';
               }

               $table .= '</tbody></table>';
            } else {
               $table .= '<p>No test details found for this pet.</p>';
            }

            return $this->respond($table); // Devolver la tabla construida

         } else {
            return $this->failNotFound('Test not found');
         }
      } catch (\Exception $e) {
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }


   public function edit($id = null)
   {
      try {
         $data = $this->PetTestsModel->find($id);
         $test_details = $this->TestDetailsModel->where('pet_tests_id', $id)->findAll();

         if ($data) {
            $data = [
               'data_pets' => $this->PetsModel->findAll(),
               'data_pet_tests' => $data,
               'test_details' => $test_details
            ];

            echo view('admin/pet_tests/form', $data);
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
      // Capturamos los datos del formulario principal
      $request = [
         'pet_id' => $this->request->getPost('pet_id'),
         'test_date' => $this->request->getPost('test_date'),
         'notes_test' => $this->request->getPost('notes_test'),
         'updated_at' => date('Y-m-d H:i:s')
      ];

      $this->rules(); // Aquí llamas a las reglas de validación

      // Validación de los datos principales
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Iniciar una transacción
            $this->db->transStart();

            // 1. Actualizar el registro principal en pet_tests
            $update = $this->PetTestsModel->update($id, $request);

            if ($update) {
               // 2. Procesar los detalles dinámicos


               // Capturamos los arrays enviados del formulario
               $codes = $this->request->getPost('code');
               $names = $this->request->getPost('name');
               $notes = $this->request->getPost('notes');
               $results = $this->request->getPost('result');
               $detailIds = $this->request->getPost('detail_id'); // Esto debe contener el id de los detalles existentes

               // Asegurarnos de que $detailIds sea un array, incluso si está vacío
               if (is_null($detailIds)) {
                  $detailIds = [];
               }

               // 3. Iterar sobre los detalles para actualizar o insertar
               for ($i = 0; $i < count($codes); $i++) {
                  if (!empty($codes[$i]) && !empty($names[$i])) {
                     $detailData = [
                        'pet_tests_id' => $id,
                        'code' => $codes[$i],
                        'name' => $names[$i],
                        'notes' => htmlspecialchars($notes[$i], ENT_QUOTES, 'UTF-8'),
                        'result' => $results[$i] ?? ''
                     ];

                     if (!empty($detailIds[$i])) {
                        // Si el detalle tiene un ID, se actualiza
                        $this->TestDetailsModel->update($detailIds[$i], $detailData);
                     } else {
                        // Si no tiene un ID, es un nuevo detalle, entonces se inserta
                        $this->TestDetailsModel->insert($detailData);
                     }
                  }
               }

               // 4. Eliminar detalles que han sido eliminados en la vista
               $existingDetailIds = $this->TestDetailsModel->where('pet_tests_id', $id)->findAll();
               $existingIds = array_column($existingDetailIds, 'id'); // Obtener IDs actuales en la base de datos

               // Asegurarnos de que $existingIds y $detailIds sean arrays antes de usarlos en array_diff
               if (!empty($existingIds)) {
                  $idsToDelete = array_diff($existingIds, $detailIds);
                  if (!empty($idsToDelete)) {
                     $this->TestDetailsModel->whereIn('id', $idsToDelete)->delete();
                  }
               }

               // Completar la transacción
               $this->db->transComplete();

               if ($this->db->transStatus() === FALSE) {
                  return $this->fail('Error al actualizar los detalles. Por favor, intenta de nuevo.');
               }

               return $this->respondNoContent('Data updated successfully');
            } else {
               return $this->fail($this->PetTestsModel->errors());
            }
         } catch (\Exception $e) {
            // Si ocurre una excepción, revertir la transacción
            $this->db->transRollback();
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }



   public function delete($id = null)
   {
      try {
         $data = $this->PetTestsModel->find($id);
         if ($data) {
            $this->PetTestsModel->delete($id);
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
         'pet_id' => [
            'label' => 'Pet Id',
            'rules' => 'required|numeric'
         ],
         'test_date' => [
            'label' => 'Test Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ]
      ]);
   }
}
