<style>
    input{text-transform: none;}
</style>
<script type="text/javascript">
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
        function saveData(){
            return $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>profil/editpassword",
                enctype: 'multipart/form-data',
                data : $("#fmpassword").serialize(),
                dataType: "json",
                async: true,
                success: function(result) {
                    console.log(result);
                    // result  = JSON.parse(result);
                    if(result.status=="sukses"){
                        $("#password").val("");
                        $("#passwordbaru").val("");
                        $("#ulangpassword").val("");
                    }else {
                       alert(result.msg);
                    }
                }
            }).responseText
        }
</script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Lagu
        <small>Index</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Index</a></li>
        <li class="active">Lagu</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Ganti Password</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form  id="fmpassword">
                            <div class="form-group">
                                <label for="">userid</label>
                                <input type="text" name="userid" id="userid" value="<?= @$row->userid ?>" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label for="">Username</label>
                                <input type="text" name="userid" id="userid" value="<?= @$row->username ?>" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label for="">password</label>
                                <input type="text" name="password" id="password"  class="form-control" required="">
                            </div>
                            <div class="form-group">
                                <label for="">passwordbaru</label>
                                <input type="text" name="passwordbaru" id="passwordbaru" class="form-control" required="">
                            </div>
                            <div class="form-group">
                                <label for="">ulangpassword</label>
                                <input type="text" name="ulangpassword" id="ulangpassword" class="form-control" required="">
                            </div>
                             <a href="javascript:void(0)" class="btn btn-primary"  onclick="saveData()" >Simpan</a>

                        </form>

                    </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
