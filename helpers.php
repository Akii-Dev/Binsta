<?php

function displayTemplate($template, $data)
{
	$loader = new \Twig\Loader\FilesystemLoader('../views');
	$twig = new \Twig\Environment($loader, [
		'debug' => true,
	]);
	$twig->addExtension(new \Twig\Extension\DebugExtension());
	$user = null;
	if (isset($_SESSION["id"]) && $_SESSION["id"] !== "") {
		$user = R::findOne("user", "id=?", array($_SESSION["id"]));
	}
	$data = array('data' => $data, 'user' => $user);

	$twig->display($template, $data);
}

function error($errorNumber, $errorMessage)
{
	$loader = new \Twig\Loader\FilesystemLoader('../views');
	$twig = new \Twig\Environment($loader);
	$errors = array('number' => $errorNumber, 'message' => $errorMessage);

	$twig->display("/error.twig", $errors);
	http_response_code(404);
	exit();
}

function getPath(): array
{
	$path = strtok($_GET['q'], '?');
	if ($path === '/') {
		return [];
	}

	return explode('/', trim($path, '/'));
}
