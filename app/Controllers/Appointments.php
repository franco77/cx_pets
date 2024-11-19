<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AppointmentsModel;
use App\Models\OwnersModel;

class Appointments extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->AppointmentsModel = new AppointmentsModel;
      $this->OwnersModel = new OwnersModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Appointments',
         'host' => site_url('appointments/')
      ];
      echo view('admin/appointments/list', $data);
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

         $recordsTotal = $this->AppointmentsModel->countTotal();
         $data = $this->AppointmentsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->AppointmentsModel->countFilter($search);

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



   public function sendReminder()
   {
      // Verificar si se ha recibido la solicitud POST
      if ($this->request->getMethod() === 'post') {
         // Decodificar los datos JSON recibidos
         $data = $this->request->getJSON(true);

         // Obtener el email y el ID de la cita
         $email = $data['email'] ?? '';
         $appointmentId = $data['id'] ?? '';

         // Validar que el email sea válido
         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email no válido.']);
         }

         // Cargar el modelo de la cita


         // Realizar la consulta a la base de datos para obtener todos los detalles de la cita
         $appointment = $this->AppointmentsModel
            ->select('appointments.*, owners.first_name, owners.last_name, pets.name as petName')
            ->join('owners', 'appointments.owner_id = owners.id')
            ->join('pets', 'appointments.pet_id = pets.id')
            ->where('appointments.id', $appointmentId)
            ->first();

         // Verificar que la cita existe
         if (!$appointment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cita no encontrada.']);
         }

         // Obtener los detalles de la cita
         $nameCustomer = $appointment['first_name'] . ' ' . $appointment['last_name'];
         $namePet = $appointment['petName'];
         $date = date('d F Y', strtotime($appointment['appointment_date']));
         $time = date('H:i', strtotime($appointment['appointment_time']));
         $reason = $appointment['reason'];
         $phone = setting('App.phone');

         // Crear el cuerpo del email usando sprintf con variables de posición
         $textMessage = sprintf(setting('App.templateAppoint'), esc($nameCustomer), esc($namePet), esc($date), esc($time), esc($reason), esc($phone), esc($phone));


         // Configurar el contenido del correo
         $emailService = \Config\Services::email();
         $emailService->setTo($email);
         $emailService->setFrom(setting('App.email'), setting('App.siteName'));
         $emailService->setSubject('Recordatorio de Cita');
         $emailService->setMessage($textMessage);

         // Enviar el correo electrónico
         if ($emailService->send()) {
            return $this->response->setJSON(['success' => true]);
         } else {
            // Obtener el error del depurador si falla el envío
            $error = $emailService->printDebugger(['headers']);
            return $this->response->setJSON(['success' => false, 'message' => 'No se pudo enviar el correo.', 'error' => $error]);
         }
      }

      // Si la solicitud no es POST, devolvemos un error
      return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Método no permitido.']);
   }




   public function updateAppointmentStatus()
   {
      $appointmentId = $this->request->getPost('id');
      $newStatus = $this->request->getPost('status');

      $update = $this->AppointmentsModel->update($appointmentId, ['status' => $newStatus]);

      if ($update) {
         return $this->response->setJSON(['success' => true]);
      } else {
         return $this->response->setJSON(['success' => false]);
      }
   }



   public function new()
   {
      $data = [
         'data_owners' => $this->OwnersModel->findAll(),
      ];

      echo view('admin/appointments/form', $data);
   }

   public function create()
   {
      $request = [
         'owner_id' => $this->request->getPost('owner_id'),
         'pet_id' => $this->request->getPost('pet_id'),
         'appointment_date' => $this->request->getPost('appointment_date'),
         'appointment_time' => $this->request->getPost('appointment_time'),
         'reason' => $this->request->getPost('reason'),
         'status' => $this->request->getPost('status'),
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
            $insert = $this->AppointmentsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->AppointmentsModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }


   public function getAvailableTimes($date)
   {


      // Consultar las citas de esa fecha
      $appointments = $this->AppointmentsModel->where('appointment_date', $date)->findAll();

      // Obtener todas las horas posibles
      $availableTimes = $this->getTimesOfDay();

      // Crear un array que incluya las horas y su estado (ocupada o disponible)
      $timesStatus = [];
      foreach ($availableTimes as $time) {
         if (in_array($time, array_column($appointments, 'appointment_time'))) {
            $timesStatus[] = ['time' => $time, 'status' => 'busy'];  // Hora ocupada
         } else {
            $timesStatus[] = ['time' => $time, 'status' => 'available'];  // Hora disponible
         }
      }

      return $this->response->setJSON($timesStatus);  // Retornar las horas en formato JSON
   }

   // Horas de trabajo disponibles del día (de 8:00 AM a 5:00 PM)
   private function getTimesOfDay()
   {
      return [
         '08:00:00',
         '09:00:00',
         '10:00:00',
         '11:00:00',
         '12:00:00',
         '13:00:00',
         '14:00:00',
         '15:00:00',
         '16:00:00',
         '17:00:00',
      ];
   }

   public function show($id = null)
   {
      try {
         $data = $this->AppointmentsModel->select('
         appointments.id,
         appointments.pet_id,
         appointments.owner_id AS appointment_owner_id,
         appointments.appointment_date,
         appointments.appointment_time,
         appointments.reason,
         appointments.status,
         appointments.created_at,
         appointments.updated_at,
         owners.id AS owner_id,
         owners.first_name,
         owners.last_name,
         pets.id AS pet_id,
         pets.name AS pet_name
     ')
            ->join('owners', 'owners.id = appointments.owner_id')
            ->join('pets', 'pets.id = appointments.pet_id')
            ->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Owner</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Pet Name</th><td>' . $data['pet_name'] . '</td></tr>';
            $table .= '<tr><th>Appointment Date</th><td>' . $data['appointment_date'] . '</td></tr>';
            $table .= '<tr><th>Appointment Time</th><td>' . $data['appointment_time'] . '</td></tr>';
            $table .= '<tr><th>Reason</th><td>' . $data['reason'] . '</td></tr>';
            if ($data['status'] == 'pending') {
               $table .= '<tr><th>Status</th><td class="table-primary">Pending</td></tr>';
            } elseif ($data['status'] == 'confirmed') {
               $table .= '<tr><th>Status</th><td class="table-success">Success</td></tr>';
            } else {
               $table .= '<tr><th>Status</th><td class="table-warning">Completed</td></tr>';
            }
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
         $data = $this->AppointmentsModel->find($id);

         if ($data) {
            $data = [
               'data_owners' => $this->OwnersModel->findAll(),
               'data_appointments' => $data
            ];

            echo view('admin/appointments/form', $data);
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
         'owner_id' => $this->request->getPost('owner_id'),
         'pet_id' => $this->request->getPost('pet_id'),
         'appointment_date' => $this->request->getPost('appointment_date'),
         'appointment_time' => $this->request->getPost('appointment_time'),
         'reason' => $this->request->getPost('reason'),
         'status' => $this->request->getPost('status'),
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
            $update = $this->AppointmentsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->AppointmentsModel->errors());
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
         $data = $this->AppointmentsModel->find($id);
         if ($data) {
            $this->AppointmentsModel->delete($id);
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
         'owner_id' => [
            'label' => 'Owner Id',
            'rules' => 'required|numeric'
         ],
         'appointment_date' => [
            'label' => 'Appointment Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'appointment_time' => [
            'label' => 'Appointment Time',
            'rules' => 'required'
         ],
         'reason' => [
            'label' => 'Reason',
            'rules' => 'required|string'
         ],
         'status' => [
            'label' => 'Status',
            'rules' => 'required|in_list[pending, confirmed, completed, cancelled]'
         ]
      ]);
   }
}
