<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AppointmentsModel;

class Dashboard extends BaseController
{
    public function __construct()
    {
        $this->AppointmentsModel = new AppointmentsModel;
    }


    protected function getAppointmentsByDate($date)
    {
        return $this->AppointmentsModel->select('appointments.id, appointments.owner_id, appointments.pet_id, appointments.appointment_date, appointments.appointment_time, appointments.reason, appointments.status, owners.id AS owId, owners.ic, owners.first_name, owners.last_name, owners.email, pets.id AS petId, pets.name')
            ->join('owners', 'owners.id = appointments.owner_id')
            ->join('pets', 'pets.id = appointments.pet_id')
            ->where('appointment_date', $date)
            ->findAll();
    }


    public function getDashboardStatistics()
    {
        $builder = $this->db->table('owners');
        $query = $builder->select([
            '(SELECT COUNT(*) FROM owners) AS total_owners',
            '(SELECT COUNT(*) FROM pets) AS total_pets',
            '(SELECT COUNT(*) FROM products) AS total_products',
            '(SELECT COUNT(*) 
                     FROM appointments 
                     WHERE (status = "pending" OR status = "confirmed") 
                       AND appointment_date = CURDATE()
                    ) AS total_appointments'
        ])->get();

        $result = $query->getRow();

        if ($result) {
            return $result;
        } else {

            return (object)[
                'total_owners' => 0,
                'total_pets' => 0,
                'total_products' => 0,
                'total_appointments' => 0
            ];
        }
    }


    public function index()
    {
        // Obtener las citas del día de hoy
        $today = date('Y-m-d');
        $appointments = $this->getAppointmentsByDate($today);
        $statistics       = $this->getDashboardStatistics();
        $data = [
            'title' => 'Dashboard',
            'total_owners' => $statistics->total_owners,
            'total_pets' => $statistics->total_pets,
            'total_products' => $statistics->total_products,
            'total_appointments' => $statistics->total_appointments,
            'appointments' => $appointments,

        ];
        return view('admin/dashboard/dashboard', $data);
    }
}
