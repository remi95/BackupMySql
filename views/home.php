<?php 
	
	$title = "Accueil";

ob_start(); ?>

	<h1>Bienvenue - Que voulez-vous faire ?</h1>

	<div class="list-group">
		<a href="<?= BASE_URL ?>backup/" class="list-group-item list-group-item-action">
			Créer une sauvegarde de toutes les bases de données
		</a>
		<a href="<?= BASE_URL ?>listbackup/" class="list-group-item list-group-item-action">
			Restaurer les bases de données depuis une sauvegarde
		</a>
		<a href="<?= BASE_URL ?>crontab/" class="list-group-item list-group-item-action">
			Automatiser les backups
		</a>
	</div>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>