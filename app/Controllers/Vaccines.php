<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\VaccinesModel;
use App\Models\VaccineDetailsModel;
use App\Models\PetsModel;

class Vaccines extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->VaccinesModel = new VaccinesModel;
      $this->VaccineDetailsModel = new VaccineDetailsModel;
      $this->PetsModel = new PetsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Vaccines',
         'host' => site_url('vaccines/')
      ];
      echo view('admin/vaccines/list', $data);
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

         $recordsTotal = $this->VaccinesModel->countTotal();
         $data = $this->VaccinesModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->VaccinesModel->countFilter($search);

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

      echo view('admin/vaccines/form', $data);
   }

   public function create()
   {
      // Recoger los datos del formulario
      $request = [
         'pet_id' => $this->request->getPost('pet_id'),
         'vaccine_date' => $this->request->getPost('vaccine_date'),
         'notes' => $this->request->getPost('notes'),
         'created_at' => date('Y-m-d H:i:s'),
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
            // Iniciar transacción
            $this->db->transBegin();

            // Insertar la prescripción principal
            $vaccineId = $this->VaccinesModel->insert($request);

            if (!$vaccineId) {
               // Error en la inserción de la prescripción
               $this->db->transRollback();
               return $this->fail($this->VaccinesModel->errors());
            }

            // Recoger los medicamentos (detalles)
            $productIds = $this->request->getPost('product_id');
            $medications = $this->request->getPost('product_name');
            $dosages = $this->request->getPost('dosage');

            // Verificar que todos los arrays tienen el mismo tamaño
            if (!empty($medications) && count($medications) > 0) {
               foreach ($medications as $index => $medication) {
                  if (!empty($medication)) {
                     $detailData = [
                        'vaccine_id' => $vaccineId,
                        'product_id' => $productIds[$index],    // SKU del medicamento
                        'product_name' => $medication,          // Nombre del medicamento
                        'dosage' => $dosages[$index],           // Dosis
                     ];

                     // Guardar el detalle de prescripción
                     $this->VaccineDetailsModel->insert($detailData);
                  }
               }
            }  // Verificar si la transacción tuvo éxito
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
      $data = $this->VaccinesModel->getVaccineDetails($id);
      if (!$data) {
         throw new \RuntimeException('Prescripción no encontrada.');
      }
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      $viewData = [
         'data' => $data,
      ];
      $html = view('admin/vaccines/print', $viewData);
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      try {
         $dompdf->render();
      } catch (\Exception $e) {
         throw new \RuntimeException('Error al generar el PDF: ' . $e->getMessage());
      }
      $dompdf->stream('voucher_' . $id . '.pdf', ['Attachment' => 0]);
   }


   public function show($id = null)
   {
      try {
         // Seleccionar los datos de la prescripción, mascota, propietario, especie y raza
         $data = $this->VaccinesModel
            ->select('vaccines.id, vaccines.pet_id, vaccines.vaccine_date, vaccines.notes, vaccines.created_at, vaccines.updated_at,
                         pets.id AS pet_id, pets.name AS pet_name, pets.photo, pets.sex, pets.age, pets.weight, pets.weight_unit,
                         owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone,
                         species.name AS specie_name,
                         breeds.name AS breed_name')
            ->join('pets', 'pets.id = vaccines.pet_id')
            ->join('owners', 'owners.id = pets.owner_id')
            ->join('species', 'species.id = pets.specie_id')
            ->join('breeds', 'breeds.id = pets.breed_id')
            ->where('vaccines.id', $id)
            ->first();

         // Si no se encuentra la prescripción, retornar un error 404
         if (!$data) {
            return $this->failNotFound('Prescripción no encontrada.');
         }

         // Consultar los detalles de la prescripción desde vaccine_details
         $vaccineDetails = $this->db->table('vaccine_details')
            ->select('vaccine_details.id, vaccine_details.vaccine_id, vaccine_details.product_id, vaccine_details.product_name, vaccine_details.dosage')
            ->where('vaccine_details.vaccine_id', $id)
            ->get()
            ->getResultArray();

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
         $table .= '<tr><th style="width: 30%;">Fecha de Dosis</th><td>' . $data['vaccine_date'] . '</td></tr>';
         $table .= '<tr><th>Notas</th><td>' . $data['notes'] . '</td></tr>';
         $table .= '<tr><th>Creado el</th><td>' . $data['created_at'] . '</td></tr>';
         $table .= '<tr><th>Actualizado el</th><td>' . $data['updated_at'] . '</td></tr>';

         $table .= '</table>';
         // Mostrar los detalles de la prescripción (medicamentos, dosis, frecuencia)
         if (!empty($vaccineDetails)) {
            $table .= '<h5>Detalles de la Vacunación</h5>';
            $table .= '<table class="table table-sm table-bordered">';
            $table .= '<thead class="table-dark"><tr><th>Vacunas</th><th>Dosis</th></tr></thead>';
            $table .= '<tbody>';

            foreach ($vaccineDetails as $detail) {
               $table .= '<tr>';
               $table .= '<td>' . $detail['product_name'] . '</td>';
               $table .= '<td>' . $detail['dosage'] . '</td>';
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
         $data_pets = $this->PetsModel->findAll();
         $data_vaccines = $this->VaccinesModel->find($id);
         $vaccine_details = $this->VaccineDetailsModel->where('vaccine_id', $id)->findAll();
         if ($data_vaccines) {
            $data = [
               'data_pets' => $data_pets,
               'data_vaccines' => $data_vaccines,
               'vaccine_details' => $vaccine_details
            ];

            echo view('admin/vaccines/form', $data);
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
      // Verificar si el ID es válido
      if (!$id) {
         return $this->failValidationError('ID de vacuna no proporcionado.');
      }

      // Recoger los datos del formulario
      $request = [
         'pet_id' => $this->request->getPost('pet_id'),
         'vaccine_date' => $this->request->getPost('vaccine_date'),
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
            // Iniciar transacción
            $this->db->transBegin();

            // Verificar si el registro de la vacuna existe
            $vaccine = $this->VaccinesModel->find($id);
            if (!$vaccine) {
               // Si no existe, devolver un error
               $this->db->transRollback();
               return $this->failNotFound('Vacuna no encontrada.');
            }

            // Actualizar la vacuna principal
            if (!$this->VaccinesModel->update($id, $request)) {
               // Error en la actualización de la vacuna
               $this->db->transRollback();
               return $this->fail($this->VaccinesModel->errors());
            }

            // Recoger los medicamentos (detalles)
            $productIds = $this->request->getPost('product_id');
            $medications = $this->request->getPost('product_name');
            $dosages = $this->request->getPost('dosage');

            // Eliminar detalles antiguos si es necesario
            $this->VaccineDetailsModel->where('vaccine_id', $id)->delete();

            // Verificar que todos los arrays tienen el mismo tamaño
            if (!empty($medications) && count($medications) > 0) {
               foreach ($medications as $index => $medication) {
                  if (!empty($medication)) {
                     $detailData = [
                        'vaccine_id' => $id,
                        'product_id' => $productIds[$index],
                        'product_name' => $medication,
                        'dosage' => $dosages[$index],
                     ];

                     // Guardar el detalle de prescripción
                     if (!$this->VaccineDetailsModel->insert($detailData)) {
                        // Log de errores
                        log_message('error', 'Error al actualizar detalles de prescripción: ' . json_encode($this->VaccineDetailsModel->errors()));

                        // Revertir la transacción si hay error en la inserción
                        $this->db->transRollback();
                        return $this->failServerError('Ocurrió un error al actualizar los detalles de la prescripción.');
                     }
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
               return $this->respondUpdated([
                  'status' => 200,
                  'message' => 'Prescripción actualizada correctamente.'
               ]);
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
         $data = $this->VaccinesModel->find($id);
         if ($data) {
            $this->VaccinesModel->delete($id);
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
         'vaccine_date' => [
            'label' => 'Vaccine Date',
            'rules' => 'required'
         ],
         'notes' => [
            'label' => 'Notes',
            'rules' => 'required|string'
         ],

      ]);
   }
}
