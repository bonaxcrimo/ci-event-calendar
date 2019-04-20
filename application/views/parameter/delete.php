<form id="fm" method="post" novalidate>
    <input type="hidden" name="oper" id="oper" value="del">
    <input type="hidden" name="parameter_key" value="<?= @$data->parameter_key ?>">
    <div class="alert alert-danger">
        Are you sure delete this data?
    </div>
    <?php $this->load->view('parameter/view'); ?>
</form>