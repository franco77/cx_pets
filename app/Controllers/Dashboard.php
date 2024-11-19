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


    public function index()
    {
        // Obtener las citas del día de hoy
        $today = "2024-09-30";
        $appointments = $this->getAppointmentsByDate($today);

        $data = [
            'title' => 'Dashboard',
            'appointments' => $appointments,

        ];
        return view('admin/dashboard/dashboard', $data);
    }
}
