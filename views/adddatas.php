<?php 

	$title = "Ajouter des données";


ob_start(); ?>

	<h1>Ajouter des données dans la table myTable de la base de données appli_web</h1>

	<form method="post" action="controllers/adddatas.controller.php"> 
		<div class="form-group">
			<label>Title</label>
			<input type="text" name="title" class="form-control">
		</div>
		<div class="form-group">
			<label>Description</label>
			<textarea name="description" class="form-control"></textarea>
		</div>
		<div class="form-group">
			<label>Date</label>
			<input type="date" name="date" class="form-control"/>
		</div>
		<div class="form-group">
			<label>Author</label>
			<input type="text" name="author" class="form-control">
		</div>
		<button type="submit" class="btn btn-primary" name="submit">Submit</button>
	</form>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>