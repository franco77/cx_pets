<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PrescriptionsModel;
use App\Models\PetsModel;
use App\Models\PrescriptionDetailsModel;

class Prescriptions extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PrescriptionsModel = new PrescriptionsModel;
      $this->PetsModel = new PetsModel;
      $this->PrescriptionDetailsModel = new PrescriptionDetailsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Prescriptions',
         'host' => site_url('prescriptions/')
      ];
      echo view('admin/prescriptions/list', $data);
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

         $recordsTotal = $this->PrescriptionsModel->countTotal();
         $data = $this->PrescriptionsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PrescriptionsModel->countFilter($search);

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
         'data_pets' => $this->PetsModel->findAll(),
      ];

      echo view('admin/prescriptions/form', $data);
   }

   public function create()
   {
      // Recoger los datos del formulario
      $request = [
         'pet_id' => $this->request->getPost('pet_id'),
         'start_date' => $this->request->getPost('start_date'),
         'end_date' => $this->request->getPost('end_date'),
         'notes' => $this->request->getPost('notes'),
         'created_at' => date('Y-m-d H:i:s'),
         'updated_at' => date('Y-m-d H:i:s'),
      ];

      // Reglas de validación (si tienes alguna)
      $this->rules();

      // Validar los datos
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Iniciar transacción para asegurar integridad de datos
            $this->db->transBegin();

            // Insertar la prescripción principal
            $prescriptionId = $this->PrescriptionsModel->insert($request);

            // Si falla la inserción, devolver error
            if (!$prescriptionId) {
               return $this->fail($this->PrescriptionsModel->errors());
            }

            // Recoger los medicamentos (detalles)
            $productIds = $this->request->getPost('product_id');
            $medications = $this->request->getPost('product_name');
            $dosages = $this->request->getPost('dosage');
            $frequencies = $this->request->getPost('frequency');

            // Verificar que hay al menos un medicamento
            if (!empty($medications) && count($medications) > 0) {
               // Iterar sobre los detalles y guardarlos
               foreach ($medications as $index => $medication) {
                  if (!empty($medication)) {
                     // Crear el array de detalle de prescripción
                     $detailData = [
                        'prescription_id' => $prescriptionId,
                        'product_id' => $productIds[$index],    // SKU del medicamento
                        'product_name' => $medication,          // Nombre del medicamento
                        'dosage' => $dosages[$index],           // Dosis
                        'frequency' => $frequencies[$index]     // Frecuencia
                     ];

                     // Guardar el detalle de prescripción en la tabla 'prescription_details'
                     $this->PrescriptionDetailsModel->insert($detailData);
                  }
               }
            }

            // Verificar si la transacción tuvo éxito
            if ($this->db->transStatus() === FALSE) {
               // Si hubo un error, revertir los cambios
               $this->db->transRollback();
               return $this->failServerError('Ocurrió un error al guardar la prescripción. Intente nuevamente.');
            } else {
               // Si todo fue bien, confirmar la transacción
               $this->db->transCommit();
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Prescripción creada correctamente.'
               ]);
            }
         } catch (\Exception $e) {
            // Revertir en caso de excepción
            $this->db->transRollback();
            return $this->failServerError($e->getMessage()); // Mostrar mensaje de error exacto
         }
      }
   }


   function printVoucherView($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }
      $data = $this->PrescriptionsModel->getPrescriptionDetails($id);
      if (!$data) {
         throw new \RuntimeException('Prescripción no encontrada.');
      }
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      $viewData = [
         'data' => $data,
      ];
      $html = view('admin/prescriptions/print', $viewData);
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
      $data = $this->PrescriptionsModel->getPrescriptionDetails($id);

      if (!$data) {
         throw new \RuntimeException('Pago no encontrado.');
      }

      // Configuración de Dompdf
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      $viewData = ['data' => $data];
      $html = view('admin/prescriptions/print', $viewData); // Generar el HTML para el PDF

      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();

      // Ruta del archivo PDF en public/uploads/pdf/pagos/
      $directory = FCPATH . 'uploads/pdf/prescriptions/';  // FCPATH apunta a la carpeta public/
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
      $data = $this->PrescriptionsModel->getPrescriptionDetails($id);

      if (!$data) {
         throw new \RuntimeException('Factura no encontrada.');
      }
      $empresaEmail = $data['email'];
      // Configurar el correo electrónico

      $filePath = $this->printVoucher($id);
      $this->email->setTo($empresaEmail);
      $this->email->setFrom(setting('App.email'));
      $this->email->setSubject('Factura y Pagos - Voucher #' . $id);
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






   public function show($id = null)
   {
      try {
         // Seleccionar los datos de la prescripción, mascota, propietario, especie y raza
         $data = $this->PrescriptionsModel
            ->select('prescriptions.id, prescriptions.pet_id, prescriptions.start_date, prescriptions.end_date, prescriptions.notes, prescriptions.created_at, prescriptions.updated_at,
                         pets.id AS pet_id, pets.name AS pet_name, pets.photo, pets.sex, pets.age, pets.weight, pets.weight_unit,
                         owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone,
                         species.name AS specie_name,
                         breeds.name AS breed_name')
            ->join('pets', 'pets.id = prescriptions.pet_id')
            ->join('owners', 'owners.id = pets.owner_id')
            ->join('species', 'species.id = pets.specie_id')
            ->join('breeds', 'breeds.id = pets.breed_id')
            ->where('prescriptions.id', $id)
            ->first();

         // Si no se encuentra la prescripción, retornar un error 404
         if (!$data) {
            return $this->failNotFound('Prescripción no encontrada.');
         }

         // Consultar los detalles de la prescripción desde prescription_details
         $prescriptionDetails = $this->db->table('prescription_details')
            ->select('prescription_details.product_name, prescription_details.dosage, prescription_details.frequency')
            ->where('prescription_details.prescription_id', $id)
            ->get()
            ->getResultArray();

         // Construir la tabla con la información completa
         $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';

         // Datos de la mascota
         $table .= '<tr><th style="width: 30%;">Nombre de la Mascota</th><td>' . $data['pet_name'] . '</td></tr>';
         $table .= '<tr><th>Edad de la Mascota</th><td>' . $data['age'] . ' años</td></tr>';
         $table .= '<tr><th>Sexo de la Mascota</th><td>' . $data['sex'] . '</td></tr>';
         $table .= '<tr><th>Peso de la Mascota</th><td>' . $data['weight'] . ' ' . $data['weight_unit'] . '</td></tr>';
         $table .= '<tr><th>Especie</th><td>' . $data['specie_name'] . '</td></tr>';
         $table .= '<tr><th>Raza</th><td>' . $data['breed_name'] . '</td></tr>';
         $table .= '</table><br>';

         $table .= '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
         $table .= '<tr><th>Propietario</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
         $table .= '<tr><th>Identificación</th><td>' . $data['ic'] . '</td></tr>';
         $table .= '<tr><th>Email</th><td>' . $data['email'] . '</td></tr>';
         $table .= '<tr><th>Teléfono</th><td>' . $data['phone'] . '</td></tr>';
         $table .= '<tr><th>Dirección</th><td>' . $data['address'] . '</td></tr>';
         $table .= '</table><br>';

         $table .= '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
         $table .= '<tr><th style="width: 30%;">Fecha de Inicio</th><td>' . $data['start_date'] . '</td></tr>';
         $table .= '<tr><th>Fecha de Finalización</th><td>' . $data['end_date'] . '</td></tr>';
         $table .= '<tr><th>Notas</th><td>' . $data['notes'] . '</td></tr>';
         $table .= '<tr><th>Creado el</th><td>' . $data['created_at'] . '</td></tr>';
         $table .= '<tr><th>Actualizado el</th><td>' . $data['updated_at'] . '</td></tr>';

         $table .= '</table>';

         // Mostrar los detalles de la prescripción (medicamentos, dosis, frecuencia)
         if (!empty($prescriptionDetails)) {
            $table .= '<h4>Detalles de la Prescripción</h4>';
            $table .= '<table class="table table-sm table-bordered">';
            $table .= '<thead class="table-dark"><tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th></tr></thead>';
            $table .= '<tbody>';

            foreach ($prescriptionDetails as $detail) {
               $table .= '<tr>';
               $table .= '<td>' . $detail['product_name'] . '</td>';
               $table .= '<td>' . $detail['dosage'] . '</td>';
               $table .= '<td>' . $detail['frequency'] . '</td>';
               $table .= '</tr>';
            }

            $table .= '</tbody></table>';
         } else {
            $table .= '<p>No se encontraron detalles para esta prescripción.</p>';
         }

         return $this->respond($table);
      } catch (\Exception $e) {
         // Manejo de errores generales
         return $this->failServerError('Lo siento, ocurrió un error. Por favor contacte al administrador.');
      }
   }



   public function edit($id = null)
   {
      try {
         // Buscar la prescripción por ID
         $data_pets = $this->PetsModel->findAll();
         $data_prescriptions = $this->PrescriptionsModel->find($id);
         $prescription_details = $this->PrescriptionDetailsModel->where('prescription_id', $id)->findAll();

         if ($data_prescriptions) {
            $data = [
               'data_pets' => $data_pets,
               'data_prescriptions' => $data_prescriptions,
               'prescription_details' => $prescription_details
            ];

            return view('admin/prescriptions/form', $data);
         } else {
            // Si no se encuentra el registro
            return $this->failNotFound('La prescripción no fue encontrada.');
         }
      } catch (\Exception $e) {
         // Error en el servidor
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }


   public function update($id = null)
   {
      // Recoger los datos del formulario
      $request = [
         'pet_id' => $this->request->getPost('pet_id'),
         'start_date' => $this->request->getPost('start_date'),
         'end_date' => $this->request->getPost('end_date'),
         'notes' => $this->request->getPost('notes'),
         'updated_at' => date('Y-m-d H:i:s'),
      ];

      // Reglas de validación
      $this->rules();

      // Validar los datos
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Iniciar transacción para asegurar integridad de datos
            $this->db->transBegin();

            // Actualizar la prescripción principal
            $update = $this->PrescriptionsModel->update($id, $request);

            if (!$update) {
               // Si falla la actualización, revertir la transacción
               $this->db->transRollback();
               return $this->fail($this->PrescriptionsModel->errors());
            }

            // Recoger los medicamentos (detalles)
            $productIds = $this->request->getPost('product_id');
            $medications = $this->request->getPost('product_name');
            $dosages = $this->request->getPost('dosage');
            $frequencies = $this->request->getPost('frequency');

            // Verificar que hay al menos un medicamento
            if (!empty($medications) && count($medications) > 0) {
               // Eliminar los detalles anteriores de la prescripción
               $this->PrescriptionDetailsModel->where('prescription_id', $id)->delete();

               // Iterar sobre los nuevos detalles y guardarlos
               foreach ($medications as $index => $medication) {
                  if (!empty($medication)) {
                     // Crear el array de detalle de prescripción
                     $detailData = [
                        'prescription_id' => $id,
                        'product_id' => $productIds[$index],    // SKU del medicamento
                        'product_name' => $medication,          // Nombre del medicamento
                        'dosage' => $dosages[$index],           // Dosis
                        'frequency' => $frequencies[$index]     // Frecuencia
                     ];

                     // Guardar el nuevo detalle de prescripción en la tabla 'prescription_details'
                     $this->PrescriptionDetailsModel->insert($detailData);
                  }
               }
            }

            // Verificar si la transacción tuvo éxito
            if ($this->db->transStatus() === FALSE) {
               // Si hubo un error, revertir los cambios
               $this->db->transRollback();
               return $this->failServerError('Ocurrió un error al actualizar la prescripción. Intente nuevamente.');
            } else {
               // Si todo fue bien, confirmar la transacción
               $this->db->transCommit();
               return $this->respondNoContent('Data updated');
            }
         } catch (\Exception $e) {
            // Revertir en caso de excepción
            $this->db->transRollback();
            return $this->failServerError($e->getMessage()); // Mostrar mensaje de error exacto
         }
      }
   }


   public function delete($id = null)
   {
      try {
         $data = $this->PrescriptionsModel->find($id);
         if ($data) {
            $this->PrescriptionsModel->delete($id);
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
         'start_date' => [
            'label' => 'Start Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'end_date' => [
            'label' => 'End Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'notes' => [
            'label' => 'Notes',
            'rules' => 'required|string'
         ]
      ]);
   }
}
