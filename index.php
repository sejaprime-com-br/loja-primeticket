<?php
require 'vendor/autoload.php';
require_once 'app/config/Config.php';
require_once 'app/controller/Controller.php';
require_once 'app/controller/HomeController.php';
require_once 'app/controller/QuemSomosController.php';
require_once 'app/controller/ContatoController.php';

$controllerHome = new HomeController();
$controllerQuemSomos = new QuemSomosController();
$controllerContato = new ContatoController();

define('BASE_URL', $base_url);
// SLIM
$config = [
	'settings' => [
		'displayErrorDetails' => true,
	],
];
$app = new \Slim\App($config);

$array_conf = [
	'BASE_URL' => $base_url,
	'PREFIX' => PREFIX,
];

if (!isset($_SESSION)) {
	session_start();
}  

//página inicial
$app->get('/', function ($request, $response, $args) use ($controllerHome) {
	$controller = $controllerHome;
	$body = $response->getBody();
	$body->write($controller->home());
    return $response->withStatus(200);
});

//quem somos
$app->get('/quemsomos', function ($request, $response, $args) use ($controllerQuemSomos) {
	$controller = $controllerQuemSomos;
	$body = $response->getBody();
	$body->write($controller->quemsomos());
    return $response->withStatus(200);
});

//contato
$app->get('/contato', function ($request, $response, $args) use ($controllerContato) {
	$controller = $controllerContato;
	$body = $response->getBody();
	$body->write($controller->contato());
    return $response->withStatus(200);
});

$app->run();