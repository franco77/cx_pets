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
                  <button class="btn btn-sm btn-danger bulk-delete">Eliminar</button>
                  <button class="btn btn-sm btn-primary refresh" purpose="add">Refrescar</button>
               </div>
               <button class="btn btn-sm btn-primary form-action" purpose="add">Agregar</button>
            </div>
            <table id="datatable" class="table table-sm activate-select dt-responsive nowrap w-100">
               <thead>
                  <tr>
                     <th style="width: 0px"><input type="checkbox" class="check-items"></th>
                     <th>Ic</th>
                     <th>First Name</th>
                     <th>Last Name</th>
                     <th>Email</th>
                     <th>Address</th>
                     <th style="width: 0px">#</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
</div>


<div class="modal fade modal-form" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal Title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <!-- Aquí va el contenido del modal -->
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
               "data": "ic"
            },
            {
               "data": "first_name"
            },
            {
               "data": "last_name"
            },
            {
               "data": "email"
            },
            {
               "data": "address"
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