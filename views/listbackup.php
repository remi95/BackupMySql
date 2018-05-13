<?php

	$title = "Liste des backups";

ob_start(); ?>

	<h1>A partir de quelle sauvegarde voulez-vous restaurer vos base de données</h1>

	<ul class="list-group">
		<?php for($i=0; $i < count($listBackupsFormat); $i++){ ?>
		   <li class="list-group-item"><?= $listBackupsFormat[$i] ?>
		   		<div class="btn-group float-right" role="group" aria-label="Basic example">
		   			<a class="btn btn-success" href="<?= BASE_URL .'restore/'. $listBackups[$i] .'/all/' ?>" role="button">Restaurer toute la BDD</a>
		   			<a class="btn btn-primary" href="<?= BASE_URL .'listdatabases/'.$listBackups[$i] ?>" role="button">Restaurer une BDD spécifique</a>
				</div>
		   </li>
		<?php } ?>
	</ul>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>