<?php

	if (empty($_SESSION['server'])) {
		$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
		header('Location: '.BASE_URL.'login/'); 
	}

	require('models/Server.php');
	require('list.controller.php');

	$server = unserialize($_SESSION['server']);
	$serverBackupsDir = $server->getDirPath() .'/';
	$backupDir = $_GET['backup'];
	$targetDir = $serverBackupsDir.$backupDir.'/';

	$backupName = formatBackup($_GET['backup']);
	$db = $_GET['database'];

	$listBdd = getRemoteListBdd($serverBackupsDir);

	if ($backupName == false) {
		$_SESSION['error'] = "Il semblerait que la backup ne soit pas au format attendu...";
		header('Location: '.BASE_URL.'404/');
	}


	function restoreAll($listBdd){
		global $targetDir;
		$listResults = [];

		for ($k=0; $k < count($listBdd); $k++) {
			$output = restore($targetDir, $listBdd[$k]);
			if ($output == 0)
				array_push($listResults, "OK");
			else 
				array_push($listResults, "Un problème est survenu : " . $output);
		}
		
		return $listResults;
	}

	function restoreOne($db){
		global $targetDir;
		$result;
		$output = restore($targetDir, $db);
			if ($output == 0)
				$result = 'OK';
			else 
				$result = "Un problème est survenu : " . $output;
		return $result;
	}
	
	function restore($targetDir, $database){
		$server = unserialize($_SESSION['server']);
		$serverIp = $server->getIp();
		$serverUser = $server->getServeruser();
		$serverPassword = $server->getServerPassword();
		$dbUser = $server->getDbUser();
		$dbPassword = $server->getDbPassword();

		$filenameStart = 'backup_'.$database;
		$listBackups = scandir($targetDir);
		for($j=0; $j < count($listBackups); $j++){
			if (strpos($listBackups[$j], $filenameStart) !== false) {
				$filename = $listBackups[$j];

				$connection = ssh2_connect($serverIp, 22);
				if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
					ssh2_scp_send($connection, $targetDir.$filename, $filename);
					$command =  'mysql '.$database.' --user='.$dbUser.' --password='.$dbPassword.' < '.$filename;
					$restoreBackup = ssh2_exec($connection, $command);
					stream_set_blocking($restoreBackup, true);
					$output = ssh2_fetch_stream($restoreBackup, SSH2_STREAM_STDIO);
					$result = stream_get_contents($output);
					$removeRemoteFile = 'rm '.$filename;
					ssh2_exec($connection, $removeRemoteFile);
					return $result;
				}
			}
		}
		return 1;
	}


	if ($db == 'all'){
		$listResults = restoreAll($listBdd);
		require('views/restoreall.php');
	}
	elseif (in_array($db, $listBdd)) {
		$result = restoreOne($db);
		require('views/restore.php');
	}
	else {
		$_SESSION['error'] = "Il semblerait que cette abse de données n'existe pas";
		header('Location: '.BASE_URL.'404/');
	}
