<style>
input{
  text-transform: none;
}
#userid,#username{
text-transform: uppercase;
}
</style>
<form method="post" id="fm">
<input type="hidden" name="userpk" value="<?= @$data->userpk ?>">
<table class="table table-condensed" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>userid</td>
    <td><input disabled type="text" name="userid" required="true" id="userid" value="<?= @$data->userid ?>" class="form-control"></td>
  </tr>
  <tr>
    <td>username</td>
    <td><input disabled type="text" name="username" required="true" id="username" value="<?= @$data->username ?>"  class="form-control"></td>
  </tr>
  <?php
  if(@$data->password==''){
    ?>
    <tr>
      <td>password</td>
      <td>: <input disabled type="text" name="password" id="password" required=""></td>
    </tr>
    <?php
    }
  ?>
  <tr>
    <td>dashboard</td>
    <td><input disabled type="text" name="dashboard" id="dashboard" class="form-control" value="<?= @$data->dashboard ?>"></td>
  </tr>
  <tr>
    <td>Roles</td>
    <td><input readonly="" type="text" value="<?=  @$data->roles_name ?>" class="form-control">
    </td>
  </tr>
</table>
</form>
<?php
// echo form_close();
?>