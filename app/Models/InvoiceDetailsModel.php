<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceDetailsModel extends Model
{
   protected $table      = 'invoice_details';
   protected $primaryKey = 'id';
   protected $allowedFields = ['invoice_id', 'product_id', 'product_name', 'quantity', 'price'];
   protected $searchFields = ['invoice_id', 'product_id', 'product_name', 'quantity', 'price'];

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

      $builder->select('invoice_details.id, invoice_details.invoice_id, invoice_details.product_id,invoice_details.product_name, invoice_details.quantity, invoice_details.price, invoices.id AS invoId, invoices.client_id, invoices.date_invoice')
         ->join('invoices', 'invoices.id = invoice_details.invoice_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('invoices', 'invoices.id = invoice_details.invoice_id')
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

      return $builder->join('invoices', 'invoices.id = invoice_details.invoice_id')
         ->countAllResults();
   }
}
