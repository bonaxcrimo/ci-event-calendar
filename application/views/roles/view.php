
    <input type="hidden" name="roleid" value="<?= @$data->roleid ?>">
    <div class="form-group">
        <label for="">rolename</label>
        <input name="rolename" readonly=""  required="true" id="rolename"  value="<?= @$data->rolename ?>" placeholder="Role Name" class="form-control">
    </div>
    <div class="form-group">
        <label for="">Role Permission</label>
        <table id="tableFolder" class="table table-bordered table-striped" style="width:100%">
            <thead>
                <tr>
                    <th><input type="checkbox" name="select_all" disabled="" value="1" id="select-all"></th>
                    <th>class</th>
                    <th>method</th>
                    <th>displayname</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <hr>
    <div class="form-group text-right">
        <span id="btn-submit"></span>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    </div>
<script>
    $(document).ready(function(){
         $('#checkall').click(function () {
             $('input:checkbox').prop('checked', this.checked);
         });

      var tableFolder= $("#tableFolder").DataTable({
        responsive: false,
        "processing":true,
        "pagingType": "simple",
        "serverSide":true,
        "pageLength": -1,
        "order":[[1,'asc']],
        "ajax":{
            "url":"<?= base_url() ?>extension/ajax_list_acos",
            "type":"POST"
        },"initComplete":function(){
           if(oper!="add"){
                var acos = "<?= @$data->acos ?>";
            }
            var ex = acos.split(", ");
            for(j=0;j<ex.length;j++){
                // if(rows[i]['acosid']==ex[j]){
                  console.log(".checkbox-"+ex[j]);
              $(".checkbox-"+ex[j]).attr('checked','')
                // }
            }
        },
        scrollX:true,
        scrollY: "250px",
        "lengthMenu": [[10, 15, 50, -1], [10, 25, 50, "All"]],
        //set kolom inisial
        "columnDefs":[{
            "targets":0,//first column/numbering kolom
            "orderable":false,//set not orderable,
            "searchable":false,
           'className': 'dt-body-center',
           'render': function (data, type, full, meta){
               return '<input type="checkbox" disabled class="checkbox-' + $('<div/>').text(data).html() + '" name="role_permission[]" id="role_permission" value="' + $('<div/>').text(data).html() + '">';
           }
        }]
      });
      $('#select-all').on('click', function(){
          // Get all rows with search applied
          var rows = tableFolder.rows({ 'search': 'applied' }).nodes();
          // Check/uncheck checkboxes for all rows in the table
          $('input[type="checkbox"]', rows).prop('checked', this.checked);
       });

       // Handle click on checkbox to set state of "Select all" control
       $('#tableFolder tbody').on('change', 'input[type="checkbox"]', function(){
          // If checkbox is not checked
          if(!this.checked){
             var el = $('#select-all').get(0);
             // If "Select all" control is checked and has 'indeterminate' property
             if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control
                // as 'indeterminate'
                el.indeterminate = true;
             }
          }
       });
         $(document).on('shown.bs.modal', '#myModal', function () {
            tableFolder.columns.adjust();
        });
      $("#tableFolder tbody").on( 'click', 'tr', function () {
          if ( $(this).hasClass('selected') ) {
              $(this).removeClass('selected');
          }
          else {
              tableFolder.$('tr.selected').removeClass('selected');
              $(this).addClass('selected');
          }

      } );
    })
</script>