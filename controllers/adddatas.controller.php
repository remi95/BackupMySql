<?php

$title = $_POST['title'];
$description = $_POST['description'];
$date = $_POST['date'];
$author = $_POST['author'];

if (isset($title) && isset($description) && isset($date) && isset($author)) {

	$bdd = new PDO("mysql:host=localhost;dbname=appli_web;charset=utf8", "appli_web", "erty");

	$qry = $bdd->prepare("INSERT INTO myTable (title, description, date, author) VALUES (:title, :description, :date, :author)");
	$qry->execute([
		'title' => $title,
		'description' => $description,
		'date' => $date,
		'author' => $author
	]);

	$_SESSION['info'] = 'Votre ajout dans la base de donnée a été effectué.';

	header('Location: http://dev.backup.loc/index.php');

}