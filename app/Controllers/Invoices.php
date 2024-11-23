<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\InvoicesModel;
use App\Models\OwnersModel;
use App\Models\InvoiceDetailsModel;

class Invoices extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->InvoicesModel = new InvoicesModel;
      $this->OwnersModel = new OwnersModel;
      $this->InvoiceDetailsModel = new InvoiceDetailsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Invoices',
         'host' => site_url('invoices/')
      ];
      echo view('admin/invoices/list', $data);
   }

   public function data()
   {
      try {
         $request = esc($this->request->getPost());
         $search = $request['search']['value'];
         $limit = $request['length'];
         $start = $request['start'];

         $orderIndex = $request['order'][0]['column'];
         $orderFields = $request['columns'][$orderIndex]['data'];
         $orderDir = $request['order'][0]['dir'];

         $recordsTotal = $this->InvoicesModel->countTotal();
         $data = $this->InvoicesModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->InvoicesModel->countFilter($search);

         $callback = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
         ];

         return $this->respond($callback);
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }



   public function showInvoiceModal($invoiceId)
   {

      $invoice = $this->InvoicesModel->find($invoiceId);

      return $this->response->setJSON([
         'id' => $invoice['id'],
         'invoice_total' => $invoice['invoice_total'],
         'amount_paid' => $invoice['amount_paid'],
         'amount_due' => $invoice['amount_due'],
         'uuid' => $invoice['uuid']
      ]);
   }

   public function processPayment()
   {
      $invoiceId = $this->request->getPost('invoice_id');
      $paymentAmount = $this->request->getPost('payment_amount');

      $invoice = $this->InvoicesModel->find($invoiceId);

      if ($paymentAmount <= $invoice['amount_due']) {
         $newAmountPaid = $invoice['amount_paid'] + $paymentAmount;

         $updateData = [
            'amount_paid' => $newAmountPaid,
            'amount_due' => $invoice['invoice_total'] - $newAmountPaid
         ];

         $result = $this->InvoicesModel->update($invoiceId, $updateData);

         return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Payment processed successfully' : 'Payment failed'
         ]);
      }

      return $this->response->setJSON([
         'success' => false,
         'message' => 'Invalid payment amount'
      ]);
   }


   public function getOwnerDetails()
   {
      $ownerId = $this->request->getPost('client_id');
      $ownerDetails = $this->OwnersModel->find($ownerId);  // Busca el cliente por ID
      if ($ownerDetails) {
         return $this->response->setJSON($ownerDetails);  // Devuelve los datos en formato JSON
      } else {
         return $this->response->setStatusCode(404)->setBody('Owner not found');
      }
   }


   public function new()
   {
      $data = ['data_owners' => $this->OwnersModel->findAll(),];

      echo view('admin/invoices/form', $data);
   }

   public function create()
   {
      // Validar datos de la factura
      $request = [
         'client_id' => $this->request->getPost('client_id'),
         'date_invoice' => $this->request->getPost('date_invoice'),
         'invoice_total' => $this->request->getPost('invoice_total'),
         'invoice_subtotal' => $this->request->getPost('invoice_subtotal'),
         'tax' => $this->request->getPost('tax'),
         'amount_paid' => $this->request->getPost('amount_paid'),
         'amount_due' => $this->request->getPost('amount_due'),
         'notes' => $this->request->getPost('notes'),
         'created_at' => date('Y-m-d H:i:s'), // Generamos la fecha actual para la factura
         'updated_at' => date('Y-m-d H:i:s'),
         'uuid' => uniqid(),
      ];

      // Aplicar reglas de validación
      $this->validation->setRules($this->rules());

      if ($this->validation->run($request) === FALSE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {

            $this->db->transBegin();

            // Insertar la factura
            $invoice_id = $this->InvoicesModel->insert($request);

            if (!$invoice_id) {
               $this->db->transRollback();
               return $this->fail($this->InvoicesModel->errors());
            }

            // Obtener los detalles de los productos desde la solicitud
            $product_ids = $this->request->getPost('product_id') ?? []; // Asegúrate de que sea un array
            $product_names = $this->request->getPost('product_name') ?? [];
            $quantities = $this->request->getPost('quantity') ?? [];
            $prices = $this->request->getPost('price') ?? [];

            // Verificar si hay productos para insertar
            if (count($product_ids) > 0) {
               $invoiceDetailsData = [];

               // Crear un array con los datos para la tabla `invoice_details`
               for ($i = 0; $i < count($product_ids); $i++) {
                  // Insertar los detalles de la factura
                  $invoiceDetailsData[] = [
                     'invoice_id' => $invoice_id, // El ID de la factura recién insertada
                     'product_id' => $product_ids[$i],
                     'product_name' => $product_names[$i],
                     'quantity' => $quantities[$i],
                     'price' => $prices[$i],
                  ];

                  // Actualizar el inventario del producto
                  $this->db->query("UPDATE products SET quantityInStock = quantityInStock - ? WHERE productCode = ?", [
                     $quantities[$i],
                     $product_ids[$i]
                  ]);

                  // Verificar si el producto se quedó sin stock
                  $product = $this->db->query("SELECT quantityInStock FROM products WHERE productCode = ?", [$product_ids[$i]])->getRow();
                  if ($product && $product->quantityInStock < 0) {
                     $this->db->transRollback();
                     return $this->fail("<div class='alert alert-danger'>El producto con código {$product_ids[$i]} no tiene suficiente stock.</div>");
                  }
               }

               // Insertar los detalles de la factura
               $this->InvoiceDetailsModel->insertBatch($invoiceDetailsData); // Insertar múltiples filas
            }

            if ($this->db->transStatus() === FALSE) {
               $this->db->transRollback();
               return $this->failServerError('Error al crear la factura.');
            } else {
               $this->db->transCommit();
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Factura creada exitosamente.',
                  'invoice_id' => $invoice_id
               ]);
            }
         } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->failServerError('Error: ' . $e->getMessage());
         }
      }
   }


   function printVoucherView($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }

      // Obtener los detalles de la factura
      $data = $this->InvoicesModel->getInvoiceDetails($id);

      if (!$data) {
         throw new \RuntimeException('Factura no encontrada.');
      }

      // Crear PDF
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      // Pasar los datos a la vista
      $viewData = [
         'data' => $data,
      ];
      $html = view('admin/invoices/print', $viewData);

      // Generar el PDF
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      try {
         $dompdf->render();
      } catch (\Exception $e) {
         throw new \RuntimeException('Error al generar el PDF: ' . $e->getMessage());
      }

      // Stream del PDF al navegador
      $dompdf->stream('voucher_' . $id . '.pdf', ['Attachment' => 0]);
   }




   public function printVoucher($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }

      // Obtener los detalles del pago
      $data = $this->InvoicesModel->getInvoiceDetails($id);

      if (!$data) {
         throw new \RuntimeException('Pago no encontrado.');
      }

      // Configuración de Dompdf
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);

      $viewData = ['data' => $data];
      $html = view('admin/invoices/print', $viewData); // Generar el HTML para el PDF

      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();

      // Ruta del archivo PDF en public/uploads/pdf/pagos/
      $directory = FCPATH . 'uploads/pdf/invoices/';  // FCPATH apunta a la carpeta public/
      $filePath = $directory . 'voucher_' . $id . '.pdf';

      // Verificar si la carpeta existe, si no, crearla
      if (!is_dir($directory)) {
         mkdir($directory, 0755, true); // Crear la carpeta si no existe
      }

      // Guardar el archivo PDF
      $output = $dompdf->output();
      if (file_put_contents($filePath, $output)) {
         log_message('info', 'PDF guardado en: ' . $filePath);
      } else {
         log_message('error', 'Error al guardar el PDF.');
      }

      return $filePath; // Devolver la ruta del archivo PDF
   }




   public function sendVoucherByEmail($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }

      // Generar el PDF y obtener la ruta
      $filePath = $this->printVoucher($id);

      // Obtener la información de la factura
      $data = $this->InvoicesModel->getInvoiceDetails($id);

      if (!$data) {
         throw new \RuntimeException('Factura no encontrada.');
      }
      $empresaEmail = $data['email'];
      // Configurar el correo electrónico

      $filePath = $this->printVoucher($id);
      $this->email->setTo($empresaEmail);
      $this->email->setFrom(setting('App.email'));
      $this->email->setSubject('Factura y Pagos - Voucher #' . $id);
      $this->email->setMessage('Adjunto encontrarás el PDF con la información de la factura y los pagos realizados.');

      // Adjuntar el PDF
      if (file_exists($filePath)) {
         $this->email->attach($filePath);
      } else {
         log_message('error', 'El archivo PDF no existe en: ' . $filePath);
         return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontró el PDF para enviar.']);
      }

      // Enviar el correo
      if ($this->email->send()) {
         log_message('info', 'Correo enviado correctamente a: juanfranco561@gmail.com');
         return $this->response->setJSON(['status' => 'success', 'message' => 'Correo enviado correctamente.']);
      } else {
         // Obtener los errores del envío de correo
         $errorMessage = $this->email->printDebugger(['headers']);
         log_message('error', 'Error al enviar el correo: ' . $errorMessage);
         return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo enviar el correo.']);
      }
   }



   public function show($id = null)
   {
      try {
         // Obtener la factura
         $data = $this->InvoicesModel->select('invoices.id, invoices.client_id, invoices.date_invoice, invoices.invoice_total, invoices.invoice_subtotal, invoices.tax, invoices.amount_paid, invoices.amount_due, invoices.notes, invoices.created_at, invoices.updated_at, invoices.uuid, owners.id AS owId, owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone')
            ->join('owners', 'owners.id = invoices.client_id')
            ->find($id);

         // Si no se encuentra la factura, retornar un error 404
         if (!$data) {
            return $this->failNotFound('Factura no encontrada.');
         }

         // Consultar los detalles de la factura
         $invoiceDetails = $this->db->table('invoice_details')
            ->select('invoice_details.id, invoice_details.invoice_id, invoice_details.product_id, invoice_details.product_name, invoice_details.quantity, invoice_details.price')
            ->where('invoice_details.invoice_id', $id)
            ->get()
            ->getResultArray();

         // Construcción de la tabla de datos

         $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
         $table .= '<tr><th>Cliente</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
         $table .= '<tr><th>Identificación</th><td>' . $data['ic'] . '</td></tr>';
         $table .= '<tr><th>Email</th><td>' . $data['email'] . '</td></tr>';
         $table .= '<tr><th>Dirección</th><td>' . $data['address'] . '</td></tr>';
         $table .= '<tr><th>Teléfono</th><td>' . $data['phone'] . '</td></tr>';
         $table .= '</table><br />';


         $table .= '<hr><br /><table class="table table-sm activate-select dt-responsive nowrap w-100">';
         $table .= '<tr><th>Total de la Factura</th><td>' . $data['invoice_total'] . '</td></tr>';
         $table .= '<tr><th>Subtotal</th><td>' . $data['invoice_subtotal'] . '</td></tr>';
         $table .= '<tr><th>Impuesto</th><td>' . $data['tax'] . '</td></tr>';
         $table .= '<tr><th>Cantidad Pagada</th><td>' . $data['amount_paid'] . '</td></tr>';
         $table .= '<tr><th>Cantidad Debida</th><td>' . $data['amount_due'] . '</td></tr>';
         $table .= '<tr><th>Notas</th><td>' . $data['notes'] . '</td></tr>';
         $table .= '<tr><th>Creado en</th><td>' . $data['created_at'] . '</td></tr>';
         $table .= '<tr><th>Actualizado en</th><td>' . $data['updated_at'] . '</td></tr>';
         $table .= '<tr><th>Uuid</th><td>' . $data['uuid'] . '</td></tr>';
         $table .= '</table>';

         // Agregar detalles de la factura si existen
         if (!empty($invoiceDetails)) {
            $table .= '<h4>Detalles de la Factura</h4>';
            $table .= '<table class="table table-sm table-bordered">';
            $table .= '<thead class="table-dark"><tr><th>SKU</th><th>Nombre</th><th>Cantidad</th><th>Precio Unitario</th><th>Total</th></tr></thead>';
            $table .= '<tbody>';

            $total = 0; // Inicializamos la variable para el total de la factura

            foreach ($invoiceDetails as $detail) {
               $productTotal = $detail['quantity'] * $detail['price']; // Calculamos el total por producto
               $total += $productTotal; // Sumamos al total de la factura

               $table .= '<tr>';
               $table .= '<td>' . $detail['product_id'] . '</td>';
               $table .= '<td>' . $detail['product_name'] . '</td>';
               $table .= '<td>' . $detail['quantity'] . '</td>';
               $table .= '<td>' . $detail['price'] . '</td>';
               $table .= '<td>' . $productTotal . '</td>'; // Mostramos el total por producto
               $table .= '</tr>';
            }


            // Añadir una fila para mostrar el total general de la factura
            $table .= '<tr>';
            $table .= '<td class="table-light" colspan="4" class="text-right"><strong>Total de la Factura</strong></td>';
            $table .= '<td class="table-secondary">' . number_format($total, 2) . '</td>'; // Formateamos el total con dos decimales
            $table .= '</tr>';
            // TAX
            $table .= '<tr>';
            $table .= '<td class="table-light" colspan="4" class="text-right"><strong>TAX</strong></td>';
            $table .= '<td class="table-secondary">' . number_format($data['tax'], 2) . '</td>'; // Formateamos el total con dos decimales
            $table .= '</tr>';
            // TOTAL
            $table .= '<tr>';
            $table .= '<td class="table-light" colspan="4" class="text-right"><strong>TOTAL</strong></td>';
            $table .= '<td class="table-secondary">' . number_format($data['amount_due'], 2) . '</td>'; // Formateamos el total con dos decimales
            $table .= '</tr>';

            $table .= '</tbody></table>';
         } else {
            $table .= '<p>No se encontraron detalles para esta factura.</p>';
         }

         return $this->respond($table);
      } catch (\Exception $e) {
         // Manejo de errores generales
         return $this->failServerError('Lo siento, ocurrió un error. Por favor contacte al administrador.');
      }
   }



   public function edit($id = null)
   {
      try {
         $data_owners = $this->OwnersModel->findAll();
         $data_invoices = $this->InvoicesModel->find($id);
         $data_invoicesDetails = $this->InvoiceDetailsModel->where('invoice_id', $id)->findAll();
         if ($data_invoices) {
            $data = [
               'data_owners' => $data_owners,
               'data_invoices' => $data_invoices,
               'data_invoicesDetails' => $data_invoicesDetails
            ];
            return view('admin/invoices/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }


   public function update($id = null)
   {
      // Validar datos de la factura
      $request = [
         'client_id' => $this->request->getPost('client_id'),
         'invoice_total' => $this->request->getPost('invoice_total'),
         'invoice_subtotal' => $this->request->getPost('invoice_subtotal'),
         'tax' => $this->request->getPost('tax'),
         'amount_paid' => $this->request->getPost('amount_paid'),
         'amount_due' => $this->request->getPost('amount_due'),
         'notes' => $this->request->getPost('notes'),
         'updated_at' => date('Y-m-d H:i:s'),
      ];

      // Definir las reglas de validación
      $validation = \Config\Services::validation();
      $validation->setRules($this->rules());

      if ($validation->run($request) === FALSE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $validation->getErrors()
         ], 400);
      } else {
         try {

            $this->db->transBegin();

            // Actualizar la factura
            $this->InvoicesModel->update($id, $request);

            // Obtener los detalles actuales de la factura
            $existingDetails = $this->InvoiceDetailsModel->where('invoice_id', $id)->findAll();

            // Recoger los nuevos detalles del formulario
            $product_ids = $this->request->getPost('product_id') ?? [];
            $product_names = $this->request->getPost('product_name') ?? [];
            $quantities = $this->request->getPost('quantity') ?? [];
            $prices = $this->request->getPost('price') ?? [];
            $detail_ids = $this->request->getPost('detail_id') ?? []; // Necesitamos el ID de los detalles actuales para poder actualizarlos

            // Crear arrays para insertar y actualizar los detalles
            $invoiceDetailsData = [];
            $invoiceDetailsToUpdate = [];

            // Procesar los detalles recibidos
            for ($i = 0; $i < count($product_ids); $i++) {
               if (isset($detail_ids[$i]) && !empty($detail_ids[$i])) {
                  // Detalle existente, actualizarlo
                  $invoiceDetailsToUpdate[] = [
                     'id' => $detail_ids[$i],  // ID del detalle actual
                     'product_id' => $product_ids[$i],
                     'product_name' => $product_names[$i],
                     'quantity' => $quantities[$i],
                     'price' => $prices[$i],
                  ];
               } else {
                  // Nuevo detalle, agregarlo
                  $invoiceDetailsData[] = [
                     'invoice_id' => $id,
                     'product_id' => $product_ids[$i],
                     'product_name' => $product_names[$i],
                     'quantity' => $quantities[$i],
                     'price' => $prices[$i],
                  ];
               }
            }

            // Actualizar los detalles existentes
            foreach ($invoiceDetailsToUpdate as $detail) {
               $this->InvoiceDetailsModel->update($detail['id'], $detail);
            }

            // Insertar los nuevos detalles
            if (count($invoiceDetailsData) > 0) {
               $this->InvoiceDetailsModel->insertBatch($invoiceDetailsData);
            }

            // Eliminar los detalles que no están en los datos recibidos
            $existingDetailIds = array_column($existingDetails, 'id');
            $receivedDetailIds = array_filter($detail_ids); // Filtramos los detalles recibidos (solo los existentes)
            $detailsToDelete = array_diff($existingDetailIds, $receivedDetailIds); // Detalles a eliminar

            if (!empty($detailsToDelete)) {
               $this->InvoiceDetailsModel->whereIn('id', $detailsToDelete)->delete();
            }

            // Validar la transacción
            if ($this->db->transStatus() === FALSE) {
               $this->db->transRollback();
               return $this->failServerError('Error al actualizar la factura.');
            } else {
               $this->db->transCommit();
               return $this->respondUpdated([
                  'status' => 200,
                  'message' => 'Factura actualizada exitosamente.',
                  'invoice_id' => $id
               ]);
            }
         } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->failServerError('Error: ' . $e->getMessage());
         }
      }
   }


   public function delete($id = null)
   {
      try {
         $data = $this->InvoicesModel->find($id);
         if ($data) {
            $this->InvoicesModel->delete($id);
            return $this->respondDeleted([
               'status' => 200,
               'message' => 'Data deleted.'
            ]);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   private function rules()
   {
      return [
         'client_id' => 'required|integer',
         'invoice_total' => 'required|decimal',
         'invoice_subtotal' => 'required|decimal',
         'tax' => 'required|decimal',
         'amount_paid' => 'required|decimal',
         'amount_due' => 'required|decimal',
         'notes' => 'permit_empty|string',
      ];
   }
}
