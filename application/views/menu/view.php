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
            <td><input disabled name="menuname"  required="true"  value="<?= @$data->menuname ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>menuseq</td>
            <td><input disabled name="menuseq"  required="true"  value="<?= @$data->menuseq ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>menuparent</td>
            <td><input disabled name="menuparent"  required="true"  value="<?= @$data->menuparent ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>menuicon</td>
            <td><input disabled name="menuicon"  required="true"  value="<?= @$data->menuicon ?>" class="form-control"></td>
        </tr>
        <tr>
            <td>routeid</td>
            <td><input type="hidden" name="acoid" id="acoid" value="<?= @$data->acoid ?>">
                <input disabled type="text" name="aconame" id="aconame" class="form-control" value="<?= @$data->acoid!=0?@$detail->class.'/'.@$detail->method:'' ?>">
            </td>
        </tr>
        <tr>
            <td>link</td>
            <td><input disabled name="link"  required="true"  value="<?= @$data->link ?>" class="form-control"></td>
        </tr>
    </table>
</form>
