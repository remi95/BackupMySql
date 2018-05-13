<?php 

if (empty($_SESSION['server'])) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

require('models/Server.php');



require('views/home.php');