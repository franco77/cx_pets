<?php
namespace App\Models;
use CodeIgniter\Model;

class ExpensesModel extends Model{
   protected $table      = 'expenses';
   protected $primaryKey = 'id';
   protected $allowedFields = ['title', 'amount', 'support', 'notes', 'created_at', 'updated_at'];
   protected $searchFields = ['title', 'amount', 'support', 'notes', 'created_at'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null){
      $builder = $this->table($this->table);

      $i = 0;
      foreach ($this->searchFields as $column)
      {
            if($search)
            {
               if($i == 0)
               {
                  $builder->groupStart()
                          ->like($column, $search);
               }
               else
               {
                  $builder->orLike($column, $search);
               }

               if(count($this->searchFields) - 1 == $i) $builder->groupEnd();

            }
            $i++;
      }

      // Muestra datos menores o iguales a las primeras 6 columnas.
	  
      $builder->select('id, title, amount, support, notes, created_at')
              ->orderBy($orderField, $orderDir)
              ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['notes'] = strlen($query[$index]['notes']) > 50 ? substr($query[$index]['notes'], 0, 50).'...' : $query[$index]['notes'];
         
         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="'.$query[$index][$this->primaryKey].'">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="'.$query[$index][$this->primaryKey].'" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="'.$query[$index][$this->primaryKey].'"><i class="far fa-edit"></i></button>';
      }
      return $query;
   }

   public function countTotal(){
      return $this->table($this->table)
                  ->countAll();
   }

   public function countFilter($search){
      $builder = $this->table($this->table);

      $i = 0;
      foreach ($this->searchFields as $column)
      {
            if($search)
            {
               if($i == 0)
               {
                  $builder->groupStart()
                          ->like($column, $search);
               }
               else
               {
                  $builder->orLike($column, $search);
               }

               if(count($this->searchFields) - 1 == $i) $builder->groupEnd();

            }
            $i++;
      }

      return $builder->countAllResults();
   }

}