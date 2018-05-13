<?php 

	$title = "Pas non trouvée";

ob_start(); ?>

	<h1>La page demandée ne semble pas exister.</h1>

<?php $body = ob_get_clean();

require 'templates/layout.php';
?>