<?php

if (empty($_SESSION['server']) && !$launchedByScript) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

require('models/Server.php');
require('controllers/list.controller.php');

$listBdd = [];
$listResults = [];

$server = unserialize($_SESSION['server']);
$backupDir = $server->getDirPath() .'/';

$destination = createDir($backupDir);

if ($destination != false) {
	$listBdd = getRemoteListBdd($backupDir);
	foreach ($listBdd as $bdd) {
		$output = backup($destination, $bdd);
		if ($output == 0)
			array_push($listResults, "OK");
		else 
			array_push($listResults, "Un problème est survenu");
	}
}
else {
	$_SESSION['error'] = "Une erreur est survenue.\n Si vous avez effectué une backup dans la minute, le dossier existe déjà.\n Sinon, il se peut que vous n'ayez pas les droits d'écriture dans le dossier.";
}

function createDir($backupDir){
	$dirname = 'backups_'.date('ymd_Hi');
	if (!is_dir($backupDir.$dirname)){
		$oldmask = umask(0);
		mkdir($backupDir.$dirname, 0777, true);
		umask($oldmask);
		removeOldDir($backupDir);
		return $backupDir.$dirname.'/';
	}
	else
		return false;
}

function removeOldDir($backupDir){
	$listBackups = getListBackups($backupDir);
	$nbBackups = count($listBackups);
	if ($nbBackups > 5) {
		$command = 'rm -rf '.escapeshellarg($backupDir.end($listBackups));
		system($command, $output);
	}
}

function backup($destination, $database){
	$server = unserialize($_SESSION['server']);
	$serverIp = $server->getIp();
	$serverUser = $server->getServeruser();
	$serverPassword = $server->getServerPassword();
	$dbUser = $server->getDbUser();
	$dbPassword = $server->getDbPassword();
	
	$filename = 'backup_'.$database.'_'.date('ymd_Hi').'.sql';

	$connection = ssh2_connect($serverIp, 22);
	if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
		$command =  'mysqldump '.$database.' --user='.$dbUser.' --password='.$dbPassword.' > '.$filename;
		$createBackup = ssh2_exec($connection, $command);
		stream_set_blocking($createBackup, true);
		$endBackup = stream_get_contents($createBackup);
		ssh2_scp_recv($connection, $filename, $destination.$filename);
		$removeRemoteFile = 'rm '.$filename;
		ssh2_exec($connection, $removeRemoteFile);
		
		if (file_exists($destination.$filename)) 
			return 0;
		else 
			return "Une erreur est survenue lors de la sauvegarde";
	}
	else {
		return "Une erreur est survenue lors de la connexion SSH";
	}
}

function backupAll($destination) {
	$server = unserialize($_SESSION['server']);
	$serverIp = $server->getIp();
	$serverUser = $server->getServeruser();
	$serverPassword = $server->getServerPassword();
	$filename = 'backup_'.date('ymd_Hi').'.sql';

	$connection = ssh2_connect($serverIp, 22);
	if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
		$command =  'mysqldump --user='.$serverUser.' --password='.$serverPassword.' --all-databases > '. $filename;
		echo $command;
		$stream = ssh2_exec($connection, $command);
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
		ssh2_scp_recv($connection, $filename, $destination.$filename);
		
		if (file_exists($destination.$filename)) 
			echo "ok";
		else 
			echo "Une erreur est survenue lors de la sauvegarde";
	}
	else {
		echo "Une erreur est survenue lors de la connexion SSH";
	}
}

require('views/backup.php');
