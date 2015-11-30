<form action="<?php echo $action; ?>" method="post">

	<?php echo $bank_selector_label ?>

	<select id="root-bank" name="root_bank" style="width: auto;"></select>
	<select id="bank-id" name="bank_id" style="display: none; width: auto;"></select>

	<div class="buttons">
		<div class="right">
			<input type="submit" value="<?php echo $button_confirm; ?>" class="button"/>
		</div>
	</div>
</form>
<?php echo $javascript ?><br/>

