<?php

class Server {

	private $ip;
	private $serverName;
	private $serverUser;
	private $serverPassword;
	private $dbUser;
	private $dbPassword;
	private $dirPath;
	private $crontab;

	public function __construct(){
		
	}

	public static function withIP($ip) {
		$instance = new self();
		$instance->getByIp($ip);
		return $instance;
	}

	public function getByIp($ip, $serverUser, $serverPassword){
		$bdd = new PDO("mysql:host=localhost;dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
		$req = $bdd->prepare('SELECT * FROM server WHERE ip = :ip AND serverUser = :serverUser');
		$req->execute(array(
			'ip' => $ip,
			'serverUser' => $serverUser
		));
		$result = $req->fetch();
		$this->setIp($result['ip']);
		$this->setServerName($result['serverName']);
		$this->setServerUser($result['serverUser']);
		$this->setServerPassword($serverPassword);
		$this->setDbUser($result['dbUser']);
		$this->setDbPassword($result['dbPass']);
		$backupPath = '/home/'.USER.'/Documents/backups/'.$serverUser.'_at_'.$result['serverName'];
		$this->setDirPath($backupPath);
		$this->setCrontab($result['crontab']);
	}

	public function getIp(){
		return $this->ip;
	}

	public function getServerName(){
		return $this->serverName;
	}

	public function getServerUser(){
		return $this->serverUser;
	}

	public function getServerPassword(){
		return $this->serverPassword;
	}

	public function getDbUser(){
		return $this->dbUser;
	}

	public function getDbPassword(){
		return $this->dbPassword;
	}

	public function getDirPath(){
		return $this->dirPath;
	}

	public function isCrontab(){
		return $this->crontab;
	}

	public function setIp($ip){
		$this->ip = $ip;
	}

	public function setServerName($serverName){
		$this->serverName = $serverName;
	}

	public function setServerUser($serverUser){
		$this->serverUser = $serverUser;
	}

	public function setServerPassword($serverPassword){
		$this->serverPassword = $serverPassword;
	}

	public function setDbUser($dbUser){
		$this->dbUser = $dbUser;
	}

	public function setDbPassword($dbPassword){
		$this->dbPassword = $dbPassword;
	}

	public function setDirPath($dirPath){
		$this->dirPath = $dirPath;
	}

	public function setCrontab($crontab){
		$this->crontab = $crontab;
	}
}