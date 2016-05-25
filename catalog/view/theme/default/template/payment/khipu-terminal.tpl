<?php echo $header; ?><?php echo $column_left; ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/atmosphere/2.1.2/atmosphere.min.js"></script>
<script src="//storage.googleapis.com/installer/khipu-1.1.jquery.js"></script>
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
