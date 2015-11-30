<?php echo $header; ?>
<div class="container">
	<div id="wait-msg" class="error">
		<h2>Error de comunicación con khipu</h2>
		<ul>
			<li><strong>Código</strong>: <?php echo $exception->getStatus(); ?></li>
			<li><strong>Mensaje</strong>: <?php echo $exception->getMessage(); ?></li>
			<?php if(method_exists($exception, 'getErrors')) { ?>
        	<li>Errores
			<ul>
				<?php foreach($exception->getErrors() as $errorItem) { ?>
					<li><strong><?php echo $errorItem->getField(); ?></strong>: <?php echo $errorItem->getMessage(); ?></li>
				<?php } ?>
			</ul>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php echo $footer; ?>

