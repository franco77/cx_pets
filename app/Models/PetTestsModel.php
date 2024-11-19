<?php

namespace App\Models;

use CodeIgniter\Model;

class PetTestsModel extends Model
{
   protected $table      = 'pet_tests';
   protected $primaryKey = 'id';
   protected $allowedFields = ['pet_id', 'test_date', 'notes_test', 'created_at', 'updated_at'];
   protected $searchFields = ['pets.name', 'pet_tests.test_date', 'pet_tests.notes_test', 'pet_tests.created_at', 'pet_tests.updated_at'];

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

      $builder->select('pet_tests.id, pet_tests.pet_id, pet_tests.test_date, pet_tests.notes_test, pet_tests.created_at, pet_tests.updated_at, pets.id AS petId, pets.owner_id, pets.name, pets.sex, pets.age, pets.weight, pets.weight_unit')
         ->join('pets', 'pets.id = pet_tests.pet_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['notes_test'] = strlen($query[$index]['notes_test']) > 50 ? substr($query[$index]['notes_test'], 0, 50) . '...' : $query[$index]['notes_test'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-danger" href="' . base_url('petTests/printVoucherView/') . $query[$index][$this->primaryKey] . '"><i class="far fa-file-pdf"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button> <button class="btn btn-sm btn-xs btn-info send-email" item-id="' . $query[$index][$this->primaryKey] . '" style="border-radius: 1px;"> <i class="fa fa-envelope"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      // Conteo total de registros
      return $this->table($this->table)
         ->join('pets', 'pets.id = pet_tests.pet_id')
         ->countAllResults();
   }

   public function countFilter($search = null)
   {
      $builder = $this->table($this->table)
         ->join('pets', 'pets.id = pet_tests.pet_id');

      // Aplicar filtros de búsqueda si se proporciona un término de búsqueda
      if ($search) {
         $builder->groupStart();
         foreach ($this->searchFields as $i => $column) {
            if ($i === 0) {
               $builder->like($column, $search);
            } else {
               $builder->orLike($column, $search);
            }
         }
         $builder->groupEnd();
      }

      // Contar los resultados filtrados
      return $builder->countAllResults();
   }
   public function getTestDetails($id)
   {
      // Obtener los datos principales de la prueba y los datos de la mascota y el dueño (owner)
      $testData = $this->db->table('pet_tests')
         ->select('pet_tests.id, pet_tests.pet_id, pet_tests.test_date, pet_tests.notes_test, pet_tests.created_at, pet_tests.updated_at,
                    pets.id AS pet_id, pets.name AS pet_name, pets.photo, pets.sex, pets.age, pets.weight, pets.weight_unit,
                    owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone,
                    species.name AS specie_name,
                    breeds.name AS breed_name')
         ->join('pets', 'pets.id = pet_tests.pet_id') // Unir con la tabla 'pets' en base al campo 'pet_id'
         ->join('owners', 'owners.id = pets.owner_id') // Unir con la tabla 'owners' en base al campo 'owner_id' de 'pets'
         ->join('species', 'species.id = pets.specie_id') // Unir con la tabla 'species' en base al campo 'specie_id' de 'pets'
         ->join('breeds', 'breeds.id = pets.breed_id') // Unir con la tabla 'breeds' en base al campo 'breed_id' de 'pets'
         ->where('pet_tests.id', $id)
         ->get()
         ->getRowArray(); // Obtener solo una fila como array

      // Si no se encuentran los datos de la prueba, retornar null
      if (!$testData) {
         return null;
      }

      // Obtener los detalles de la prueba (códigos, resultados, notas, etc.)
      $testDetails = $this->db->table('test_details')
         ->select('test_details.id, test_details.pet_tests_id, test_details.code, test_details.name, test_details.description, test_details.notes, test_details.result')
         ->where('test_details.pet_tests_id', $id)
         ->get()
         ->getResultArray(); // Obtener múltiples filas como array

      // Combinar la información de la prueba con los detalles de la prueba
      $testData['details'] = $testDetails;

      // Retornar el array completo que incluye la prueba y los detalles
      return $testData;
   }
}