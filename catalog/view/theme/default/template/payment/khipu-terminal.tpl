<?php echo $header; ?><?php echo $column_left; ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/atmosphere/2.1.2/atmosphere.min.js"></script>
<script src="//storage.googleapis.com/installer/khipu-1.1.js"></script>
<div class="container">
    <div id="wait-msg" class="success">
        <?php echo $wait_message ?>
    </div>
    <div id="khipu-chrome-extension-div" style="display: none"></div>
    <div id="khipu-chrome-status-div" style="display: none"></div>
</div>

<?php echo $javascript ?>
<?php echo $footer; ?>
