<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoicesModel extends Model
{
   protected $table      = 'invoices';
   protected $primaryKey = 'id';
   protected $allowedFields = ['client_id', 'date_invoice', 'invoice_total', 'invoice_subtotal', 'tax', 'amount_paid', 'amount_due', 'notes', 'created_at', 'updated_at', 'uuid'];
   protected $searchFields = ['owners.first_name', 'owners.last_name', 'invoices.invoice_total', 'invoices.invoice_subtotal', 'invoices.tax', 'invoices.amount_paid', 'invoices.uuid'];

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

      $builder->select('invoices.id, invoices.client_id, invoices.date_invoice, invoices.invoice_total, invoices.invoice_subtotal, invoices.tax, invoices.amount_paid, invoices.amount_due, invoices.notes, invoices.created_at, invoices.updated_at, invoices.uuid, owners.id AS owId, owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone')
         ->join('owners', 'owners.id = invoices.client_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-danger" href="' . base_url('invoices/printvoucherView/') . $query[$index][$this->primaryKey] . '"><i class="far fa-file-pdf"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="far fa-edit"></i></button> <button class="btn btn-sm btn-xs btn-info send-email" item-id="' . $query[$index][$this->primaryKey] . '" style="border-radius: 1px;"><i class="fa fa-envelope"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('owners', 'owners.id = invoices.client_id')
         ->countAll();
   }

   public function countFilter($search)
   {
      $builder = $this->table($this->table)
         ->join('owners', 'owners.id = invoices.client_id');

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

   public function getInvoiceDetails($id)
   {
      // Obtener los datos principales de la factura y los datos del cliente (owner)
      $invoiceData = $this->db->table('invoices')
         ->select('invoices.id, invoices.client_id, invoices.date_invoice, invoices.invoice_total, invoices.invoice_subtotal, invoices.tax, invoices.amount_paid, invoices.amount_due, invoices.notes, invoices.created_at, invoices.updated_at, invoices.uuid,
                  owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone')
         ->join('owners', 'owners.id = invoices.client_id') // Unimos la tabla 'owners' en base a la relación con 'client_id'
         ->where('invoices.id', $id)
         ->get()
         ->getRowArray(); // Obtener solo una fila como array

      // Si no se encuentra la factura, retornar null
      if (!$invoiceData) {
         return null;
      }

      // Obtener los detalles de la factura (productos, cantidad, precio)
      $invoiceDetails = $this->db->table('invoice_details')
         ->select('invoice_details.product_name, invoice_details.quantity, invoice_details.price')
         ->where('invoice_details.invoice_id', $id)
         ->get()
         ->getResultArray(); // Obtener múltiples filas como array

      // Combinar la información de la factura con los detalles de la factura
      $invoiceData['details'] = $invoiceDetails;

      // Retornar el array completo que incluye la factura y los detalles
      return $invoiceData;
   }
}
