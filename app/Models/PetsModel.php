<?php

namespace App\Models;

use CodeIgniter\Model;

class PetsModel extends Model
{
   protected $table      = 'pets';
   protected $primaryKey = 'id';
   protected $allowedFields = ['owner_id', 'name', 'photo', 'sex', 'age', 'weight', 'weight_unit', 'specie_id', 'breed_id', 'profile', 'created_at', 'updated_at'];
   protected $searchFields = ['owners.first_name', 'owners.last_name', 'pets.name', 'pets.sex', 'pets.age', 'breeds.name', 'species.name'];

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

      $builder->select('pets.id, pets.owner_id, pets.name AS pName, pets.photo, pets.sex, pets.weight, pets.weight_unit, pets.age, breeds.id AS bId, breeds.id_specie, breeds.name AS brName, species.id AS sId, species.name AS spName, species.description, owners.id AS oId, owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone')
         ->join('owners', 'owners.id = pets.owner_id')
         ->join('species', 'species.id = pets.specie_id')
         ->join('breeds', 'breeds.id = pets.breed_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['first_name'] =  $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];
         $query[$index]['weight'] =  $query[$index]['weight'] . ' ' . $query[$index]['weight_unit'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-primary" href="' . base_url('MedicalRecords/show/') . $query[$index][$this->primaryKey] . '"><i class="fas fa-file-word"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->select('pets.*, breeds.id, breeds.id_specie, breeds.name, species.id, species.name, species.description, owners.id, owners.first_name, owners.last_name')
         ->join('owners', 'owners.id = pets.owner_id')
         ->join('species', 'species.id = pets.specie_id')
         ->join('breeds', 'breeds.id = pets.breed_id')
         ->countAll();
   }

   public function countFilter($search)
   {
      $builder = $this->table($this->table);

      // Búsqueda filtrada
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

      return $builder->select('pets.*, breeds.id, breeds.id_specie, breeds.name, species.id, species.name, species.description, owners.id, owners.first_name, owners.last_name')
         ->join('owners', 'owners.id = pets.owner_id')
         ->join('species', 'species.id = pets.specie_id')
         ->join('breeds', 'breeds.id = pets.breed_id')
         ->countAllResults();
   }
}
