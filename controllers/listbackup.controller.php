<?php 

if (empty($_SESSION['server'])) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

require('models/Server.php');
require('controllers/list.controller.php');

$server = unserialize($_SESSION['server']);
$backupDir = $server->getDirPath() .'/';

$listBackups = getListBackups($backupDir);
$listBackupsFormat = getFormattedListBackups($backupDir);

require('views/listbackup.php');