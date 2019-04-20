<style>
input{
  text-transform: none;
}
#userid,#username{
text-transform: uppercase;
}
</style>
<table class="table table-condensed" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>acoid</td>
    <td><input readonly="" type="text" name="userid" required="true" id="acoid" value="<?= @$row->acoid ?>" class="form-control"></td>
  </tr>
</table>
<?php
// echo form_close();
?>