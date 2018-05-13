<?php

$title = "Résultats de la restauration";

ob_start(); ?>

	<h1>Résultats de la restauration de toutes les bases de données à partir de : <?= $backupName ?></h1>

	<table class="table">
	  <thead class="thead-dark">
	  		<th scope="col">#</th>
		    <th>Base de données</th>
		    <th>Rapport</th>
	   </tr>
	   </thead>
	   <tbody>
	   <?php for($i=0; $i < count($listBdd); $i++){ ?>
		   <tr>
		   		<td scope="row"><?= $i +1 ?></td>
			   <td><?= $listBdd[$i] ?></td>
			   <td><?= $listResults[$i] ?></td>
		   </tr>
		<?php } ?>
		</tbody>
	</table>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>