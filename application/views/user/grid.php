<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User
        <small>Index</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Index</a></li>
        <li class="active">User</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Data User</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <p>
                      <button class="btn btn-primary" onclick="add()" <?= hasPermission('user','add')?'':'disabled' ?>><i class="fa fa-plus"> </i> Add Data</button>
                      <button class="btn btn-default" onclick="reload_table()"><i class="fa fa-refresh"> </i> Reload Data</button>
                    </p>
                        <table id="table" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Userpk</th>
                                    <th class="big-col">aksi</th>
                                    <th>userid</th>
                                    <th>username</th>
                                    <th>dashboard</th>
                                    <th>roles_name</th>
                                    <th>modifiedon</th>
                                    <th>modifiedby</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>userid</th>
                                    <th>username</th>
                                    <th>dashboard</th>
                                    <th>roles_name</th>
                                    <th>modifiedby</th>
                                    <th>modifiedon</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
              <div id="useracl">

              </div>
            </div>
        </div>
    </section>
    <!-- modal -->
    <div id="myModal" class="modal fade modal-fullscreen" tabindex="-1" role="dialog" style="top: -20px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
<!--                     <h5 class="modal-title">user</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">user</h3>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                  <span id="btn-submit"></span>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal -->
</div>
<script>
  var table,oper;
  var url;
   function roles(userpk){
        page="<?php echo base_url()?>useracl/index?userpk="+userpk;
        $('#useracl').html('<img src="<?php echo base_url()?>libraries/img/loading.gif">').load(page);
    }
  $(document).ready(function(){
      $('#table tfoot th').each( function () {
          var title = $(this).text();
          if(title!='')
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
      });
      $(document).on('shown.bs.modal', '#myModal', function () {
        $('[autofocus]', this).focus();
      });
      table = $("#table").DataTable({
          responsive: false,
          "processing":true,
          "serverSide":true,
          "order":[],
          "ajax":{
              "url":"<?= base_url() ?>user/ajax_list",
              "type":"POST"
          },
          scrollX:true,
          scrollY: "400px",
          "initComplete":function(){
            $("#table tbody tr:first-child").click();
          },
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
          //set kolom inisial
          "columnDefs":[{
              "targets":[0],//first column/numbering kolom
              "orderable":false,//set not orderable
          },{
                "targets": [1],
                "visible": false,
                "searchable": false
            }]
      });
      $("#table tbody").on( 'click', 'tr', function () {
          var data = table.row(this).data();
          roles(data[1]);
          if ( $(this).hasClass('selected') ) {
              $(this).removeClass('selected');
          }
          else {
              table.$('tr.selected').removeClass('selected');
              $(this).addClass('selected');
          }
      } );
      // end data table
      table.columns().every( function () {
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
  });
  //end document ready
  function view(userpk){
    page='<?php echo base_url(); ?>user/view/'+userpk;
    $.ajax({
        type: "GET",
        url: page,
        dataType: 'html',
        success: function(res) {
            $('.modal-title').html('View user');
            $('.modal-body').html(res);
            $('#myModal').modal({backdrop: 'static'},'show');
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
  }
  function add(){
    page='<?php echo base_url(); ?>user/add';
     $.ajax({
        type: "GET",
        url: page,
        dataType: 'html',
        success: function(res) {
            $('.modal-title').html('Add user');
            $('.modal-body').html(res);
            oper="";
            url = '<?php echo base_url(); ?>user/add';
            $("#btn-submit").html("<button id='btnSave' class='btn btn-primary' onclick='saveData();'>Save</button>")
            $('#myModal').modal({backdrop: 'static'},'show');
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
  }
  function edit(userpk){
    page='<?php echo base_url(); ?>user/edit/'+userpk;
     $.ajax({
        type: "GET",
        url: page,
        dataType: 'html',
        success: function(res) {
            $('.modal-title').html('Edit user');
            $('.modal-body').html(res);
            oper="";
            url = '<?php echo base_url(); ?>user/edit/'+userpk;
            $("#btn-submit").html("<button id='btnSave' class='btn btn-primary' onclick='saveData();'>Save</button>")
            $('#myModal').modal({backdrop: 'static'},'show');
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
  }
  function deleted(userpk){
    page='<?php echo base_url(); ?>user/delete/'+userpk;
     $.ajax({
        type: "GET",
        url: page,
        dataType: 'html',
        success: function(res) {
            $('.modal-title').html('Delete user');
            $('.modal-body').html(res);
            url = '<?php echo base_url(); ?>user/delete/'+userpk;
            oper="del";
            $("#btn-submit").html("<button id='btnSave' class='btn btn-danger' onclick='saveData();'>Delete</button>")
            $('#myModal').modal({backdrop: 'static'},'show');
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
  }
  function validate(){
      var status = true;
      $('#fm :input[required]:visible').each(function(index,element){
          if($(element).val().trim()==""){
              status=false;
              alert($(element).attr('id') + " tidak boleh kosong");
              $(element).focus();
              return false;
          }
      })
      return status;
  }
  function reload_table()
  {
      table.ajax.reload(null,false); //reload datatable ajax

  }
  function saveData(){
    if(validate()){
      $.ajax({
        type: "POST",
        url: url,
        enctype: 'multipart/form-data',
        data : $("#fm").serialize(),
        dataType: "json",
        async: true,
        success: function(result) {
            if(result.status=="sukses"){
                if(oper=="del"){
                   $('#myModal').modal('hide');
                  reload_table();
                }else{
                  $('#myModal').modal('hide');
                  reload_table();
                }

            }else {
               console.log("gagal");
            }
        }
    }).responseText
    }
  }
</script>