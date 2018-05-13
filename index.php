<?php
session_start();
require "config.php";

if(isset($_GET['page']) && !preg_match('#[./\\\]+#',$_GET['page']) && is_file('controllers/'.$_GET['page'].'.controller.php'))
{
	require 'controllers/'.$_GET['page'].'.controller.php';
}
elseif(empty($_GET['page']))
{
	require 'controllers/home.controller.php';
}

else
{
	require 'controllers/404.controller.php';
}