<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?= $title ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<style type="text/css">
		main {
			margin: 50px;
		}
		.navbar-right > li {
			margin-left: 20px;
		}
	</style>
</head>
<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		  <a class="navbar-brand" href="<?= BASE_URL ?>">Backup your Databases</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarNav">
		    <ul class="navbar-nav">
		      <li class="nav-item active">
		        <a class="nav-link" href="<?= BASE_URL ?>">Accueil</a>
		      </li>
		    </ul>
		  </div>

		  <?php if (isset($_SESSION['server'])) { 

			  	$server = unserialize($_SESSION['server']);
			  	$ip = $server->getIp();
			  	$serverName = $server->getServerName();
			  	$user = $server->getServerUser();
		  	?>

			  <ul class="nav navbar-nav navbar-right text-white">
		        <li>Connecté en tant que <?= $user ?> sur le serveur <?= $serverName . ' ('. $ip .')' ?></li>
		        <li><a href="<?= BASE_URL ?>logout/">Déconnexion</a></li>
		      </ul>

		   <?php } ?>
		</nav>
	</header>
	<main>

		<?php if (isset($_SESSION['error'])) { 

			echo '<div class="alert alert-danger" role="alert">'.
				$_SESSION['error'].
			'</div>';

			unset($_SESSION['error']);
		} ?>

		<?php if (isset($_SESSION['info'])) { 

			echo '<div class="alert alert-info" role="alert">'.
				$_SESSION['info'].
			'</div>';

			unset($_SESSION['info']);
		} ?>


		<?= $body ?>

	</main>
</body>
</html>