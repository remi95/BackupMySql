<?php

if ($argc > 1) {

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
		global $argv;
		$serverIp = $argv[1];
		$serverUser = $argv[2];
		$serverPassword = $argv[3];
		$dbUser = $argv[4];
		$dbPassword = $argv[5];

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

	function getListBackups($dir){
		$listDir = scandir($dir);
		$excepts = ['.', '..', 'dbnames.txt'];

		for ($j = 0; $j < count($excepts); $j++) {
			if (($key = array_search($excepts[$j], $listDir)) !== false) {
			    unset($listDir[$key]);
			}
		}

		rsort($listDir);
		$listBackups = array_values($listDir);

		return $listBackups;
	}

	function getRemoteListBdd($destination) {
		global $argv;
		$serverIp = $argv[1];
		$serverUser = $argv[2];
		$serverPassword = $argv[3];
		$dbUser = $argv[4];
		$dbPass = $argv[5];

		$connection = ssh2_connect($serverIp, 22);
		if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
			$command = "mysql -u ".$dbUser." -p'".$dbPass."' -e 'show databases;' > dbnames.txt";
			ssh2_exec($connection, $command);
			ssh2_scp_recv($connection, 'dbnames.txt', $destination.'dbnames.txt');
			$removeRemoteFile = "rm dbnames.txt";
			ssh2_exec($connection, $removeRemoteFile);
		}
		else {
			return "Erreur lors de la récupération des bases de données";
		}

		$parseFile = file($destination.'dbnames.txt', FILE_IGNORE_NEW_LINES);
		$excepts = ["Database", "information_schema", "performance_schema"];

		for ($i = 0; $i < count($excepts); $i++) {
			if (($key = array_search($excepts[$i], $parseFile)) !== false) {
			    unset($parseFile[$key]);
			}
		}
		$dbNames = array_values($parseFile);
		return $dbNames;
	}	


	$listBdd = [];
	$listResults = [];
	$backupDir = $argv[6];	
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
}