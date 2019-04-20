<input type="hidden" name="parameter_key" value="<?= @$data->parameter_key ?>">
<table class="table table-condensed" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>parametergrpid</td>
        <td><input type="text" class="form-control" name="parametergrpid" id="parametergrpid" required="true" value="<?= @$data->parametergrpid ?>" required=""></td>
    </tr>
    <tr>
        <td>parameterid</td>
        <td><input type="text" class="form-control" name="parameterid" id="parameterid" required="true" value="<?= @$data->parameterid ?>" required=""></td>
    </tr>
    <tr>
        <td>parametertext</td>
        <td><input type="text" class="form-control" name="parametertext" id="parametertext" required="true" value="<?= @$data->parametertext ?>" required=""></td>
    </tr>
    <tr>
        <td>parametermemo</td>
        <td><input type="text" class="form-control" name="parametermemo" id="parametermemo" rows="6"  value="<?= @$data->parametermemo ?>"></td>
    </tr>
</table>