<?php 

	$title = "Connexion";

ob_start(); ?>

	<h1>Connexion à un serveur distant</h1>

	<form method="post" action=""> 
		<div class="form-group">
			<label>Serveur</label>
			<select name="server">
				<?php foreach ($servers as $server): ?>
					<option value="<?= $server['ip'] ?>">
						<?= $server['serverName'].' ('.$server['ip'].')' ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label>Utilisateur du serveur</label>
			<input type="text" name="server_user" class="form-control">
		</div>
		<div class="form-group">
			<label>Mot de passe du serveur</label>
			<input type="password" name="server_password" class="form-control"></textarea>
		</div>
		<button type="submit" class="btn btn-primary" name="submit">Connexion</button>
	</form>

	<a href="<?= BASE_URL ?>register/">Enregistrer une nouvelle connexion</a>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>