<?php

require('models/Server.php');

function initBackupDir(){
	$dirname = '/home/'.USER.'/Documents/';
	if (is_dir($dirname.'backups'))
		return;
	else {
		$oldmask = umask(0);
		mkdir($dirname.'backups', 0777, true);
		umask($oldmask);
	}
}

function createServerBackupDir($path){
	if (is_dir($path))
		return;
	else {
		$oldmask = umask(0);
		mkdir($path, 0777, true);
		umask($oldmask);
	}
}

if (isset($_POST['ip']) && isset($_POST['server_name']) && isset($_POST['server_user']) && isset($_POST['server_password']) && isset($_POST['db_user']) && isset($_POST['db_password'])) {

	$ip = $_POST['ip'];
	$serverName = $_POST['server_name'];
	$serverUser = $_POST['server_user'];
	$serverPassword = $_POST['server_password'];
	$dbUser = $_POST['db_user'];
	$dbPass = $_POST['db_password'];

	$connection = ssh2_connect($ip, 22);

	 if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {

	 	$bdd = new PDO("mysql:host=localhost;dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);

	 	$req = $bdd->prepare("INSERT INTO server(id, ip, serverName, serverUser, dbUser, dbPass, crontab) VALUES (DEFAULT, :ip, :serverName, :serverUser, :dbUser, :dbPass, :crontab)");
	 	$req->execute(array(
	 		'ip' => $ip,
	 		'serverName' => $serverName,
	 		'serverUser' => $serverUser,
	 		'dbUser' => $dbUser,
	 		'dbPass' => $dbPass,
	 		'crontab' => false
	 	));

	 	$myServer = new Server();
	 	$myServer->getByIp($ip, $serverUser, $serverPassword);

	 	initBackupDir();
	 	createServerBackupDir($myServer->getDirPath());

	 	$_SESSION['server'] = serialize($myServer);
	 	header('Location: '.BASE_URL);
	 }
	 else {
	 	$_SESSION['error'] = "Impossible d'établir la connexion avec ce serveur et ces identifiants";
	 	header('Location: '.BASE_URL.'register/');
	 }
}

$_SESSION['error'] = "Veuillez correctement remplir tous les champs";
require('views/register.php');