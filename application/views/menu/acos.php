<div class="form-group">
  <input type="text" class="form-control" placeholder="Masukkan Nama Folder" id="foldersearch">
</div>
<table id="tableFolder" class="table table-bordered table-striped" style="width:100%">
    <thead>
        <tr>
            <th>id</th>
            <th>class</th>
            <th>method</th>
            <th>displayname</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th></th>
            <th>class</th>
            <th>method</th>
            <th>displayname</th>
        </tr>
    </tfoot>
</table>
<script>
  $(document).ready(function(){
  $('#tableFolder tfoot th').each( function () {
      var title = $(this).text();
      if(title!='')
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
  });

  var tableFolder= $("#tableFolder").DataTable({
    "sDom": '<"top"l>rt<"bottom" <"col-md-5"i><"col-md-7"p>><"clear">',
    responsive: false,
    "processing":true,
    "pagingType": "simple",
    "serverSide":true,
    "order":[[1,'asc']],
    "ajax":{
        "url":"<?= base_url() ?>extension/ajax_list_acos",
        "type":"POST"
    },"initComplete":function(){
    },
    scrollX:true,
    scrollY: "250px",
    "lengthMenu": [[10, 15, 50, -1], [10, 25, 50, "All"]],
    //set kolom inisial
    "columnDefs":[{
        "targets":[0],//first column/numbering kolom
        "orderable":false,//set not orderable,
        "visible":false,
        "searchable":false,
    }]
  });
     $(document).on('shown.bs.modal', '#folderModal', function () {
        tableFolder.columns.adjust();
    });
  $("#foldersearch").keyup(function(){
    tableFolder.search( $(this).val()).draw();
  });
  $("#tableFolder tbody").on( 'click', 'tr', function () {
      if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
      }
      else {
          tableFolder.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
      }

      var data =tableFolder.row( this ).data()[1] + "/"+tableFolder.row( this ).data()[2];
      var acosid  = tableFolder.row(this).data()[0];
      // var acosid = $(tableFolder.row(this).data()[0]).val();
      // data=data.slice(0, -1);
      $("#aconame").val(data);
      $("#acoid").val(acosid);
      $("#folderModal .close").click()
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
});
</script>