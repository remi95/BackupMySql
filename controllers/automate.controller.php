<?php 

if (empty($_SESSION['server'])) {
	$_SESSION['error'] = "Vous devez vous connecter à votre serveur distant";
	header('Location: '.BASE_URL.'login/'); 
}

require('models/Server.php');

$server = unserialize($_SESSION['server']);
$ip = $server->getIp();
$serverUser = $server->getServeruser();
$serverPassword = $server->getServerPassword();
$dbUser = $server->getDbUser();
$dbPassword = $server->getDbPassword();
$backupDir = $server->getDirPath() .'/';

$copyCrontab = 'crontab -l > '.$backupDir.'mycron';
$installCrontab = 'crontab '.$backupDir.'mycron';
$removeTmpCrontab = 'rm '.$backupDir.'mycron';

$bdd = new PDO("mysql:host=localhost;dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);

if ($_POST['crontab'] == "isCrontab") {
	
	$command = 'echo "* * * * * php /var/www/backup/controllers/script_backup.controller.php '.$ip.' '.$serverUser.' '.$serverPassword.' '.$dbUser.' '.$dbPassword.' '.$backupDir.'" >> '.$backupDir.'mycron';

	$sql = $bdd->prepare('UPDATE server SET crontab = :crontab WHERE ip = :ip AND serverUser = :serverUser');
	$sql->execute(array(
		'crontab' => 1,
		'ip' => $ip,
		'serverUser' => $serverUser
	));

	$server->setCrontab(true);
	$_SESSION['server'] = serialize($server);

	$_SESSION['info'] = "L'automatisation des backups est activée pour ce serveur";
}
else {
	$command = 'sed -i "/'.$ip.' '.$serverUser.' '.$serverPassword.' '.$dbUser.' '.$dbPassword.'/d" '.$backupDir.'mycron';

	$sql = $bdd->prepare('UPDATE server SET crontab = :crontab WHERE ip = :ip AND serverUser = :serverUser');
	$sql->execute(array(
		'crontab' => 0,
		'ip' => $ip,
		'serverUser' => $serverUser
	));

	$server->setCrontab(false);
	$_SESSION['server'] = serialize($server);

	$_SESSION['info'] = "L'automatisation des backups est desactivée pour ce serveur";
}

system($copyCrontab, $o1);
system($command, $o2);
system($installCrontab, $o3);
system($removeTmpCrontab, $o4);

header('Location: '.BASE_URL);