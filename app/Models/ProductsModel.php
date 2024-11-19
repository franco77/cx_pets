<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
   protected $table      = 'products';
   protected $primaryKey = 'productCode';
   protected $allowedFields = ['productName', 'productLine', 'productVendor', 'productDescription', 'quantityInStock', 'buyPrice'];
   protected $searchFields = ['productName', 'productLine', 'productVendor', 'productDescription'];

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

      $builder->select('productCode, productName, productLine, productVendor, productDescription')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['productDescription'] = strlen($query[$index]['productDescription']) > 50 ? substr($query[$index]['productDescription'], 0, 50) . '...' : $query[$index]['productDescription'];

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



   public function getProductsForAutocomplete($type, $name)
   {
      // Se asegura de que los datos estén en mayúsculas y aplica la lógica de búsqueda
      $builder = $this->db->table($this->table);
      $builder->select('productCode, productName, buyPrice');
      $builder->where('quantityInStock !=', 0);

      // Convierte el tipo y nombre a mayúsculas y aplica la búsqueda
      $name = strtoupper($name);
      $type = strtoupper($type);

      $builder->like("UPPER($type)", $name, 'after');  // 'after' es para que el LIKE busque al inicio

      $query = $builder->get();
      $result = $query->getResultArray();

      $data = [];
      foreach ($result as $row) {
         $name = $row['productCode'] . '|' . $row['productName'] . '|' . $row['buyPrice'];
         array_push($data, $name);
      }

      return $data;
   }
}
