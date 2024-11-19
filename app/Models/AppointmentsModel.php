<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentsModel extends Model
{
   protected $table      = 'appointments';
   protected $primaryKey = 'id';
   protected $allowedFields = ['owner_id', 'pet_id', 'appointment_date', 'appointment_time', 'reason', 'status', 'created_at', 'updated_at'];
   protected $searchFields = ['owners.first_name', 'owners.last_name', 'appointments.appointment_date', 'appointments.appointment_time', 'appointments.reason', 'appointments.status'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      $i = 0;
      foreach ($this->searchFields as $column) {
         if ($search) {
            if ($i == 0) {
               $builder->groupStart()
                  ->like($column, $search);
            } else {
               $builder->orLike($column, $search);
            }

            if (count($this->searchFields) - 1 == $i) $builder->groupEnd();
         }
         $i++;
      }

      // Muestra datos menores o iguales a las primeras 6 columnas.

      $builder->select('
      appointments.id,
      appointments.pet_id,
      appointments.owner_id AS appointment_owner_id,
      appointments.appointment_date,
      appointments.appointment_time,
      appointments.reason,
      appointments.status,
      owners.id AS owner_id,
      owners.first_name,
      owners.last_name,
      pets.id AS pet_id,
      pets.name AS pet_name
  ')
         ->join('owners', 'owners.id = appointments.owner_id')
         ->join('pets', 'pets.id = appointments.pet_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['reason'] = strlen($query[$index]['reason']) > 50 ? substr($query[$index]['reason'], 0, 50) . '...' : $query[$index]['reason'];
         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];

         if ($query[$index]['status'] == 'pending') {
            $query[$index]['status'] = '<div class="ps-2 bg-primary text-white">Pending</div>';
         } elseif ($query[$index]['status'] == 'confirmed') {
            $query[$index]['status'] = '<div class="ps-2 bg-success text-white">Confirmed</div>';
         } elseif ($query[$index]['status'] == 'completed') {
            $query[$index]['status'] = '<div class="ps-2 bg-warning text-dark">Completed</div>';
         } else {
            $query[$index]['status'] = '<div class="ps-2 bg-danger text-white">Cancelado</div>';
         }

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('owners', 'owners.id = appointments.owner_id')
         ->countAll();
   }

   public function countFilter($search)
   {
      $builder = $this->table($this->table);

      $i = 0;
      foreach ($this->searchFields as $column) {
         if ($search) {
            if ($i == 0) {
               $builder->groupStart()
                  ->like($column, $search);
            } else {
               $builder->orLike($column, $search);
            }

            if (count($this->searchFields) - 1 == $i) $builder->groupEnd();
         }
         $i++;
      }

      return $builder->join('owners', 'owners.id = appointments.owner_id')
         ->countAllResults();
   }
}