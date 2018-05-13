<?php 

	$title = "Automatisation des backups";

ob_start(); ?>

	<h1>Gérer l'automatisation des backups (toutes les minutes)</h1>

	<form method="post" action="<?= BASE_URL ?>automate/"> 
		<div class="form-group">
			<label>Activer / Désactiver l'automatisation des backups</label>
			<input 
			type="checkbox" 
			name="crontab" 
			value="isCrontab"
			<?php if ($isCrontab) { echo 'checked'; } ?>
			>
		</div>
		<button type="submit" class="btn btn-primary" name="submit">Enregistrer</button>
	</form>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>