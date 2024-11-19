<?php

namespace App\Models;

use CodeIgniter\Model;

class VaccinesModel extends Model
{
   protected $table      = 'vaccines';
   protected $primaryKey = 'id';
   protected $allowedFields = ['pet_id', 'vaccine_date', 'notes', 'created_at', 'updated_at'];
   protected $searchFields = ['pets.name', 'vaccines.vaccine_date', 'vaccines.notes', 'vaccines.created_at', 'vaccines.updated_at'];

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

      $builder->select('vaccines.id, vaccines.pet_id, vaccines.vaccine_date, vaccines.notes, vaccines.created_at, vaccines.updated_at, pets.id AS petId, pets.name')
         ->join('pets', 'pets.id = vaccines.pet_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['notes'] = strlen($query[$index]['notes']) > 50 ? substr($query[$index]['notes'], 0, 50) . '...' : $query[$index]['notes'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-danger" href="' . base_url('vaccines/printVoucherView/') . $query[$index][$this->primaryKey] . '"><i class="far fa-file-pdf"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('pets', 'pets.id = vaccines.pet_id')
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

      return $builder->join('pets', 'pets.id = vaccines.pet_id')
         ->countAllResults();
   }



   public function getVaccineDetails($id)
   {
      // Obtener los datos principales de la prescripción y sus relaciones
      $vaccineData = $this->db->table('vaccines')
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
         ->get()
         ->getRowArray(); // Obtener solo una fila como array

      // Si no se encuentra la prescripción, retornar null
      if (!$vaccineData) {
         return null;
      }

      // Obtener los detalles de la prescripción (medicamentos, dosis, frecuencia)
      $vaccineDetails = $this->db->table('vaccine_details')
         ->select('vaccine_details.id, vaccine_details.vaccine_id, vaccine_details.product_id, vaccine_details.product_name, vaccine_details.dosage')
         ->where('vaccine_details.vaccine_id', $id)
         ->get()
         ->getResultArray(); // Obtener múltiples filas como array

      // Combinar la información de la prescripción con sus detalles
      $vaccineData['details'] = $vaccineDetails;

      // Retornar el array completo que incluye la prescripción y los detalles
      return $vaccineData;
   }
}
