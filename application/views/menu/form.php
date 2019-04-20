<style>
	input{text-transformnone;}
</style>
<script>
     $(document).ready(function(){
        $("#btnBuka").click(function(){
            $("#formlookup").hide();
            $("#formlookup").dialog({
                top:10,
                width:'auto',
                height:'auto',
                modal:false,
                title:"View Data",
                buttons:[{
                    html:"<img class='icon' src='<?php echo base_url(); ?>libraries/icon/16x16/cancel.png'>Cancel",
                    click:function(){
                        $(this).dialog('close');
                    }
                }],open:function(){
                }
            });
        });
    });
</script>
<form method="post" id="fm" >
	<input type="hidden" name="menuid" value="<?= @$data->menuid ?>">
    <table class="table table-condensed" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>menuname</td>
            <td><input name="menuname" id="menuname" required="true"  value="<?= @$data->menuname ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>menuseq</td>
            <td><input name="menuseq"   id="menuseq" required="true"  value="<?= @$data->menuseq ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>menuparent</td>
            <td><input name="menuparent"  id="menuparent"  required="true"  value="<?= @$data->menuparent ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>menuicon</td>
            <td><input name="menuicon" id="menuicon"  required="true"  value="<?= @$data->menuicon ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>routeid</td>
            <td><input type="hidden" name="acoid" id="acoid" value="<?= @$data->acoid ?>">
                <div class="input-group">
                    <input type="text" name="aconame" class="form-control" id="aconame" required=""  value="<?= @$data->acoid!=0?@$detail->class.'/'.@$detail->method:'' ?>">
                    <span class="input-group-addon" id="openfolder"><i class="glyphicon glyphicon-option-horizontal"></i></span>
                </div>
      <!--           <span>
                    <input type="text" name="aconame" id="aconame" class="form-control"  value="<?= @$data->acoid!=0?@$detail->class.'/'.@$detail->method:'' ?>">
                    <button type="button" id="btnBuka" class="btn-small btn-dalam">...</button>
                </span> -->
                <!-- <input name="aconame" class="easyui-textbox" id="aconame" required="true"   value="<?= @$detail->class.'/'.@$detail->method ?>"    style="width:100%"> -->
            </td>
        </tr>
        <tr>
            <td>link</td>
            <td><input name="link" class="form-control"   value="<?= @$data->link ?>"></td>
        </tr>
    </table>
</form>
<script>
    $("#openfolder").click(function(){
        page='<?php echo base_url(); ?>extension/acos';
        $.ajax({
            type: "GET",
            url: page,
            dataType: 'html',
            success: function(res) {
               $('#folderModal .modal-body').html(res);
               $('#folderModal').modal('show');
            },
            error:function(request, status, error) {
                console.log("ajax call went wrong:" + request.responseText);
            }
        });

    });
</script>