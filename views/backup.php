<?php 

	$title = "Backup";

ob_start(); ?>

	<h1>Compte rendu de vos backups</h1>

	<table class="table">
	  <thead class="thead-dark">
	    <tr>
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