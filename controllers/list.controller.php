<?php

if (empty($_SESSION['server'])) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

// $host = $server->getIp();
// $dbUser = $server->getDbUser();
// $dbPass = $server->getDbPassword();
$bdd = new PDO("mysql:host=localhost;charset=utf8", 'appli_web', 'erty');

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

function getFormattedListBackups($dir){
	$listBackups = getListBackups($dir);

	for ($k = 0; $k < count($listBackups); $k++) {
		$listBackups[$k] = formatBackup($listBackups[$k]);
	}

	return $listBackups;
}

function getRemoteListBdd($destination) {
	$server = unserialize($_SESSION['server']);
	$serverIp = $server->getIp();
	$serverUser = $server->getServeruser();
	$serverPassword = $server->getServerPassword();
	$dbUser = $server->getDbUser();
	$dbPass = $server->getDbPassword();

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

function getListBdd() {
	global $bdd;
	$result = $bdd->query("SHOW DATABASES");
	$listBdd = [];
	$excepts = ["information_schema", "performance_schema"];
	while ($row = $result->fetch()) {
		array_push($listBdd, $row['Database']);
	}

	for ($i = 0; $i < count($excepts); $i++) {
		if (($key = array_search($excepts[$i], $listBdd)) !== false) {
		    unset($listBdd[$key]);
		}
	}

	$listDatabases = array_values($listBdd);

	return $listDatabases;
}

function formatBackup($dir) {
	$year = substr($dir, 8, 2);
	$month = substr($dir, 10, 2);
	$day = substr($dir, 12, 2);
	$hour = substr($dir, 15, 2);
	$minute = substr($dir, 17, 2);
	$format = "Backup du ".$day.".".$month.".".$year." à ".$hour.":".$minute;
	$dir = $format;

	if (!ctype_digit($year) || !ctype_digit($month) || !ctype_digit($day) || !ctype_digit($hour) || !ctype_digit($minute)) {
		return false;
	}
	return $dir;
}