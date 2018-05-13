<?php 
	
	$title = "Ajout d'un serveur distant";

ob_start(); ?>

	<h1>Ajout d'un serveur distant</h1>

	<div class="alert alert-info" role="alert">
		Pour vous connecter sur le serveur local, utilisez l'adresse IP 127.0.0.1
	</div>

	<form method="post" action=""> 
		<div class="form-group">
			<label>Adresse IP du serveur</label>
			<input type="text" name="ip" class="form-control">
		</div>
		<div class="form-group">
			<label>Nom que vous voulez donner au serveur</label>
			<input type="text" name="server_name" class="form-control">
		</div>
		<div class="form-group">
			<label>Utilisateur du serveur</label>
			<input type="text" name="server_user" class="form-control">
		</div>
		<div class="form-group">
			<label>Mot de passe de l'utilisateur</label>
			<input type="password" name="server_password" class="form-control"></textarea>
		</div>
		<div class="form-group">
			<label>Utilisateur mySql du serveur distant</label>
			<input type="text" name="db_user" class="form-control">
		</div>
		<div class="form-group">
			<label>Mot de passe mySql du serveur distant</label>
			<input type="password" name="db_password" class="form-control">
		</div>
		<button type="submit" class="btn btn-primary" name="submit">Ajouter</button>
	</form>

	<a href="<?= BASE_URL ?>login/">Se connecter</a>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>