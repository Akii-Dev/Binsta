<?php

$root = dirname(__DIR__, 1);
require_once  $root . '/vendor/autoload.php';
require('../dbconnection.php');

$path = getPath();

session_start();

if (isset($path[0]) && $path[0] !== "") {
	$controllerName = ucfirst($path[0]) . "Controller";
	if (class_exists($controllerName)) {
		$controller = new $controllerName();
	} else {
		error(404, "Controller $controllerName does not exist");
	}
} else {
	$controller = new PostController();
}

if (isset($path[1])) {
	if (isset($_POST) && count($_POST) !== 0) {
		$show = $path[1] . "Post";
	} else {
		$show = $path[1];
	}
	if (method_exists($controller, $show)) {
		$controller->$show();
	} else {
		error(404, "The method that was called does not exist");
	}
} else {
	$controller->index();
}
