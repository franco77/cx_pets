<?php

namespace App\Models;

use CodeIgniter\Model;

class TestsModel extends Model
{
   protected $table      = 'tests';
   protected $primaryKey = 'id';
   protected $allowedFields = ['code', 'name', 'description', 'created_at', 'updated_at'];
   protected $searchFields = ['code', 'name', 'description', 'created_at', 'updated_at'];

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

      $builder->select('tests.id, tests.code, tests.name, tests.description, tests.created_at, tests.updated_at')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
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

      return $builder->countAllResults();
   }



   public function getTestsForAutocomplete($type, $name)
   {
      // Se asegura de que los datos estén en mayúsculas y aplica la lógica de búsqueda
      $builder = $this->db->table($this->table);
      $builder->select('code, name');

      // Convierte el tipo y nombre a mayúsculas y aplica la búsqueda
      $name = strtoupper($name);
      $type = strtoupper($type);

      $builder->like("UPPER($type)", $name, 'after');  // 'after' es para que el LIKE busque al inicio

      $query = $builder->get();
      $result = $query->getResultArray();

      $data = [];
      foreach ($result as $row) {
         $name = $row['code'] . '|' . $row['name'];
         array_push($data, $name);
      }

      return $data;
   }
}
