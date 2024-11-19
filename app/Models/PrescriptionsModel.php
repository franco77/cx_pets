<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionsModel extends Model
{
   protected $table      = 'prescriptions';
   protected $primaryKey = 'id';
   protected $allowedFields = ['pet_id', 'start_date', 'end_date', 'notes', 'created_at', 'updated_at'];
   protected $searchFields = ['pets.name', 'prescriptions.start_date', 'prescriptions.end_date', 'prescriptions.notes', 'prescriptions.created_at'];

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

      $builder->select('prescriptions.id, prescriptions.pet_id, prescriptions.start_date, prescriptions.end_date, prescriptions.notes, prescriptions.created_at, pets.id AS pId, pets.name')
         ->join('pets', 'pets.id = prescriptions.pet_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['notes'] = strlen($query[$index]['notes']) > 50 ? substr($query[$index]['notes'], 0, 50) . '...' : $query[$index]['notes'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-danger" href="' . base_url('prescriptions/printVoucherView/') . $query[$index][$this->primaryKey] . '"><i class="far fa-file-pdf"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button> <button class="btn btn-sm btn-xs btn-info send-email" item-id="' . $query[$index][$this->primaryKey] . '" style="border-radius: 1px;"> <i class="fa fa-envelope"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('pets', 'pets.id = prescriptions.pet_id')
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

      return $builder->join('pets', 'pets.id = prescriptions.pet_id')
         ->countAllResults();
   }


   public function getPrescriptionDetails($id)
   {
      // Obtener los datos principales de la prescripción y sus relaciones
      $prescriptionData = $this->db->table('prescriptions')
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
         ->get()
         ->getRowArray(); // Obtener solo una fila como array

      // Si no se encuentra la prescripción, retornar null
      if (!$prescriptionData) {
         return null;
      }

      // Obtener los detalles de la prescripción (medicamentos, dosis, frecuencia)
      $prescriptionDetails = $this->db->table('prescription_details')
         ->select('prescription_details.product_name, prescription_details.dosage, prescription_details.frequency')
         ->where('prescription_details.prescription_id', $id)
         ->get()
         ->getResultArray(); // Obtener múltiples filas como array

      // Combinar la información de la prescripción con sus detalles
      $prescriptionData['details'] = $prescriptionDetails;

      // Retornar el array completo que incluye la prescripción y los detalles
      return $prescriptionData;
   }
}
