<?php 

if (empty($_SESSION['server'])) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

unset($_SESSION['server']);
header('Location: http://dev.backup.loc/login/');