<?php echo $header; ?><?php echo $column_left; ?>
<script src="//storage.googleapis.com/installer/khipu.js"></script>
<div class="container">
    <div id="wait-msg" class="success">
        <p><?php echo $wait_message ?></p>
		<p><?php echo $start_khipu ?></p>
    </div>
    <div id="khipu-chrome-extension-div" style="display: none"></div>
    <div id="khipu-chrome-status-div" style="display: none"></div>
</div>

<?php echo $javascript ?>
<?php echo $footer; ?>
