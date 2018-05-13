<?php 

if (empty($_SESSION['server'])) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

require('models/Server.php');
require('controllers/list.controller.php');

$server = unserialize($_SESSION['server']);
$backupDir = $server->getDirPath() .'/';

$listBdd = getRemoteListBdd($backupDir);
$backup = $_GET['backup'];
$backupName = formatBackup($_GET['backup']);

if ($backupName == false) {
	$_SESSION['error'] = "Il semblerait que la backup ne soit pas au format attendu...";
	header('Location: '.BASE_URL.'404/');
}

require('views/listdatabases.php');