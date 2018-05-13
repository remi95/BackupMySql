<?php

$title = "Résultat de la restauration";

ob_start(); ?>

	<h1>Résultat de la restauration à partir de : <?= $backupName ?></h1>

	<table class="table">
	  <thead class="thead-dark">
	    <tr>
		    <th>Base de données</th>
		    <th>Rapport</th>
	    </tr>
	  </thead>
	  <tbody>
	    <tr>
		   <td><?= $db ?></td>
		   <td><?= $result ?></td>
	    </tr>
	  </tbody>
	</table>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>