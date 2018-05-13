<?php

	$title = "Liste des bases de données";

ob_start(); ?>


	<h1>Choisissez la base de données à restaurer</h1>
	<h2><?= $backupName?></h2>
	<div class="list-group">
		<?php foreach ($listBdd as $bdd) { ?>
			<a href="<?= BASE_URL .'restore/'. $backup .'/'. $bdd .'/' ?>" class="list-group-item list-group-item-action"> 
				<?= $bdd ?>
			</a>
		<?php } ?>
	</div>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>