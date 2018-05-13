<?php

if (isset($_SESSION['server'])) {
	$_SESSION['error'] = "Vous êtes déjà connecté";
	header('Location: '.BASE_URL); 
}

require('models/Server.php');

$bdd = new PDO("mysql:host=localhost;dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);

$req = $bdd->prepare("SELECT ip, serverName FROM server");
$req->execute();
$servers = $req->fetchAll();

if (isset($_POST['server']) && isset($_POST['server_user']) && isset($_POST['server_password'])) {

	$ip = $_POST['server'];
	$serverUser = $_POST['server_user'];
	$serverPassword = $_POST['server_password'];

	$searchServer = $bdd->prepare("SELECT * FROM server WHERE ip = :ip AND serverUser = :serverUser");
	$searchServer->execute([
		'ip' => $ip,
		'serverUser' => $serverUser
	]);

	$server = $searchServer->fetch();

	if (!empty($server)) {
		$connection = ssh2_connect($ip, 22);

	 	if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
			$myServer = new Server();
		 	$myServer->getByIp($ip, $serverUser, $serverPassword);

		 	$_SESSION['server'] = serialize($myServer);
		 	header('Location: '.BASE_URL);
	 	}
	 	else {
	 		$_SESSION['error'] = "Impossible d'établir la connexion SSH sur le serveur actuellement...";
	 	}
	}
	else {
	 	$_SESSION['error'] = "Ce serveur et ces identifiants ne sont pas renseignés. Peut-être devez-vous les enregistrer en premier lieu.";
	}
}
require('views/login.php');