<form action="<?php echo $action; ?>" method="post">
	<input type="hidden" name="receiver_id" value="<?php echo $receiver_id ?>">
	<input type="hidden" name="subject" value="<?php echo $subject ?>"/>
	<input type="hidden" name="body" value="<?php echo $body ?>">
	<input type="hidden" name="amount" value="<?php echo $amount ?>">
	<input type="hidden" name="notify_url" value="<?php echo $notify_url ?>"/>
	<input type="hidden" name="return_url" value="<?php echo $return_url ?>"/>
	<input type="hidden" name="cancel_url" value="<?php echo $cancel_url ?>"/>
	<input type="hidden" name="custom" value="<?php echo $custom ?>">
	<input type="hidden" name="transaction_id" value="<?php echo $transaction_id ?>">
	<input type="hidden" name="payer_email" value="<?php echo $payer_email ?>">
	<input type="hidden" name="picture_url" value="<?php echo $picture_url ?>">

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

