<style>
input{
  text-transform: none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice{
  color: black;
}
#userid,#username{
text-transform: uppercase !important;
}
</style>
<form method="post" id="fm">
<input type="hidden" name="userpk" value="<?= @$data->userpk ?>">
<table class="table table-condensed" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>userid</td>
    <td><input type="text" name="userid" required="true" id="userid" value="<?= @$data->userid ?>" required="" autofocus class="form-control"></td>
  </tr>
  <tr>
    <td>username</td>
    <td><input type="text" name="username" required="true" id="username" value="<?= @$data->username ?>" required="" class="form-control"></td>
  </tr>
  <?php
  if(@$data->password=='' || strtoupper($_SESSION['username'])=='ADMIN'){
    $req = @$data->password==''?"required":"";
    ?>
    <tr>
      <td>password</td>
      <td><input type="text" name="password" id="password" <?= $req ?> class="form-control"></td>
    </tr>
    <?php
    }
  ?>
  <tr>
    <td>dashboard</td>
    <td><input type="text" name="dashboard" id="dashboard" required="" value="<?= @$data->dashboard ?>" class="form-control"></td>
  </tr>
  <tr>
    <td>Roles</td>
    <td><link href="<?php echo base_url()?>libraries/adminlte/bower_components/select2/dist/css/select2.min.css" rel="stylesheet"/>
        <script src="<?php echo base_url()?>libraries/adminlte/bower_components/select2/dist/js/select2.min.js"></script>
            <script>
              $(document).ready(function() {
                $("#user_roles").select2({
                    placeholder: "Select a State"
                });
            });
      </script>
      <select id="user_roles" name="user_roles[]" multiple="multiple"  style="width:100%; font-size:10px;" class="form-control">
        <option value=""></option>
                <?php
                    $roles=$this->db->get('tblroles')->result();
                    $arr=explode(",",$data->roles);
                    foreach($roles as $r){
                      if(strtoupper($r->rolename)=='GUEST'){
                        continue;
                      }
                      ?>
                          <option <?php if(in_array($r->roleid,$arr)){echo"selected";} ?>  value="<?= $r->roleid ?>"><span style="color:rgb(255,0,0);"><?php echo $r->rolename ?></span></option>
                        <?php
                    }
                ?>
      </select>
</td>
  </tr>
</table>
</form>
<?php
// echo form_close();
?>