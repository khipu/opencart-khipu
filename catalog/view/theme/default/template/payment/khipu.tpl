<form action="<?php echo $action; ?>" method="post">

	<?php echo $bank_selector_label ?>

	<select id="root-bank" name="root_bank" style="width: auto;"></select>
	<select id="bank-id" name="bank_id" style="display: none; width: auto;"></select>

    <div class="buttons clearfix">
        <div class="pull-right">
            <input type="submit" value="<?php echo $button_confirm; ?>" data-loading-text="Loading..." class="btn btn-primary">
        </div>
    </div>
</form>
<?php echo $javascript ?><br/>

