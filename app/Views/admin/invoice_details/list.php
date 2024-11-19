<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <div class="card-title"><?= $title ?></div>
         </div>
         <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
               <div>
                  <button class="btn btn-sm btn-primary refresh" purpose="add">Refrescar</button>
               </div>
            </div>
            <table id="datatable" class="table table-sm activate-select dt-responsive nowrap w-100" cellspacing="0">
               <thead>
                  <tr>
                     <th style="width: 0px"><input type="checkbox" class="check-items"></th>
                     <th>Invoice Id</th>
                     <th>Product Id</th>
                     <th>Product Name</th>
                     <th>Quantity</th>
                     <th>Price</th>
                     <th style="width: 0px">#</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
</div>


<div class="modal modal-form fade" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">

         </div>
      </div>
   </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<script>
   $(document).ready(function() {
      const host = "<?= $host ?>";
      const datatable = $('#datatable').DataTable({
         processing: true,
         serverSide: true,
         searching: true,
         ordering: true,
         info: true,
         autoWidth: false,
         scrollY: '45vh',
         responsive: false,
         orderCellsTop: true,
         ajax: {
            url: host + "data",
            type: "POST"
         },
         "columns": [{
               "data": "column_bulk",
               "searchable": false,
               "orderable": false
            },
            {
               "data": "invoice_id"
            },
            {
               "data": "product_id"
            },
            {
               "data": "product_name"
            },
            {
               "data": "quantity"
            },
            {
               "data": "price"
            },
            {
               "data": "column_action",
               "searchable": false,
               "orderable": false
            }
         ],
         "order": [
            [1, "DESC"]
         ]
      });

      <?= $this->include("admin/layout/partials/js_dynamic") ?>

   });
</script>
<?= $this->endSection() ?>