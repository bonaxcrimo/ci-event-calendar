<div class="box">
  <div class="box-header">
    <h3 class="box-title">Data User Roles</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <p>
        <button class="btn btn-primary" onclick="editAcl('<?= $userpk ?>')" <?= hasPermission('user','add')?'':'disabled' ?>><i class="fa fa-plus"> </i> User Roles</button>
        <button class="btn btn-default" onclick="reload_table_acl()"><i class="fa fa-refresh"> </i> Reload Data</button>
      </p>
      <table id="tableFolder" class="table table-bordered table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Aksi</th>
                    <th>Acoid</th>
                    <th>modifiedby</th>
                    <th>modifiedon</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Acoid</th>
                    <th>modifiedby</th>
                    <th>modifiedon</th>
                </tr>
            </tfoot>
      </table>
  </div>
</div>
<script>
  var tableFolder;
  var oper;
  var url;
  function viewAcl(useraclid){
      page='<?php echo base_url(); ?>useracl/view/'+useraclid;
      $.ajax({
          type: "GET",
          url: page,
          dataType: 'html',
          success: function(res) {
              $('.modal-title').html('View Acl');
              $('.modal-body').html(res);
              $('#myModal').modal({backdrop: 'static'},'show');
              $("#btn-submit").html("");
          },
          error:function(request, status, error) {
              console.log("ajax call went wrong:" + request.responseText);
          }
      });
    }

      function editAcl(userpk){
        page='<?php echo base_url(); ?>useracl/edit/'+userpk;
         $.ajax({
            type: "GET",
            url: page,
            dataType: 'html',
            success: function(res) {
                $('.modal-title').html('Edit Acl');
                $('.modal-body').html(res);
                oper="";
                url = '<?php echo base_url(); ?>useracl/edit/'+userpk;
                $("#btn-submit").html("<button id='btnSave' class='btn btn-primary' onclick='call();'>Save</button>")
                $('#myModal').modal({backdrop: 'static'},'show');
            },
            error:function(request, status, error) {
                console.log("ajax call went wrong:" + request.responseText);
            }
        });
      }
    $(document).ready(function(){

     $('#tableFolder tfoot th').each( function () {
          var title = $(this).text();
          if(title!='')
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
      });

      tableFolder= $("#tableFolder").DataTable({
        responsive: false,
        "processing":true,
        "pagingType": "simple",
        "serverSide":true,
        "pageLength": -1,
        "order":[[1,'asc']],
        "ajax":{
            "url":"<?= base_url() ?>useracl/ajax_list/<?= $userpk ?>",
            "type":"POST"
        },"initComplete":function(){
        },
        scrollX:true,
        scrollY: "250px",
        "lengthMenu": [[10, 15, 50, -1], [10, 25, 50, "All"]],
        //set kolom inisial
        "columnDefs":[{
            "targets":0,//first column/numbering kolom
            "orderable":false,//set not orderable,
            "searchable":false
        }]
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
      tableFolder.columns().every( function () {
            var that = this;
            $( 'input', this.footer() ).on( 'keyup change', function () {
                // table.search('').draw();
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
      } );

    })
      function reload_table_acl()
      {
          tableFolder.ajax.reload(null,false); //reload datatable ajax

      }
     function call(){
        return $.ajax({
            type: "POST",
            url: url,
            enctype: 'multipart/form-data',
            data : $("#fmacl").serialize(),
            dataType: "json",
            async: true,
            success: function(result) {
                console.log(result);
                // result  = JSON.parse(result);
                if(result.status=="sukses"){
                     if(oper=="del"){
                       $('#myModal').modal('hide');
                      reload_table_acl();
                    }else{
                      $('#myModal').modal('hide');
                      reload_table_acl();
                    }

                }else {
                   console.log("gagal");
                }
            }
        }).responseText



    }
</script>