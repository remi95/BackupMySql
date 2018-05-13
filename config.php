<?php 

$user = get_current_user();
define('USER', $user);

$backup_dir = '/home/'.USER.'/Documents/backups/';
define('BACKUP_DIR', $backup_dir);

$base_url = 'http://dev.backup.loc/';
define('BASE_URL', $base_url);

$dbname = 'remote_servers';
$dbuser = 'appli_web';
$dbpass = 'erty';
define('DB_NAME', $dbname);
define('DB_USER', $dbuser);
define('DB_PASS', $dbpass);