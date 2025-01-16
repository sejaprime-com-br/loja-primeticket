<?php
require 'vendor/autoload.php';
require_once 'app/config/Config.php';
require_once 'app/controller/Controller.php';
require_once 'app/controller/HomeController.php';
require_once 'app/controller/QuemSomosController.php';
require_once 'app/controller/ContatoController.php';
require_once 'app/controller/EventoController.php';
require_once 'app/controller/LocalController.php';
require_once 'app/controller/LoginController.php';

$controllerHome = new HomeController();
$controllerQuemSomos = new QuemSomosController();
$controllerContato = new ContatoController();
$controllerEvento = new EventoController();
$controllerLocal = new LocalController();
$controllerLogin = new LoginController();

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

//detalhes do evento
$app->group('/detalhes-evento', function () use ($app, $controllerEvento, $base_url) {
	$app->get('/', function ($request, $response, $args) use ($app, $base_url) {
		return $response->withHeader('Location', $base_url);
	});

	$app->get('/{idLocal}/{idEvento}', function ($request, $response, $args) use ($controllerEvento) {
		$controller = $controllerEvento;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal'], $args['idEvento']));
		return $response->withStatus(200);
	});

	$app->get('/{idLocal}/{idEvento}/', function ($request, $response, $args) use ($controllerEvento) {
		$controller = $controllerEvento;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal'], $args['idEvento']));
		return $response->withStatus(200);
	});

	$app->get('/{idLocal}/{idEvento}/{tituloEvento}', function ($request, $response, $args) use ($controllerEvento) {
		$controller = $controllerEvento;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal'], $args['idEvento']));
		return $response->withStatus(200);
	});

	$app->get('/{idLocal}/{idEvento}/{tituloEvento}/', function ($request, $response, $args) use ($controllerEvento) {
		$controller = $controllerEvento;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal'], $args['idEvento']));
		return $response->withStatus(200);
	});
});

//detalhes do local
$app->group('/detalhes-local', function () use ($app, $controllerLocal, $base_url) {
	$app->get('/', function ($request, $response, $args) use ($app, $base_url) {
		return $response->withHeader('Location', $base_url);
	});

	$app->get('/{idLocal}', function ($request, $response, $args) use ($controllerLocal) {
		$controller = $controllerLocal;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal']));
		return $response->withStatus(200);
	});

	$app->get('/{idLocal}/', function ($request, $response, $args) use ($controllerLocal) {
		$controller = $controllerLocal;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal']));
		return $response->withStatus(200);
	});

	$app->get('/{idLocal}/{tituloLocal}', function ($request, $response, $args) use ($controllerLocal) {
		$controller = $controllerLocal;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal']));
		return $response->withStatus(200);
	});

	$app->get('/{idLocal}/{tituloLocal}/', function ($request, $response, $args) use ($controllerLocal) {
		$controller = $controllerLocal;
		$body = $response->getBody();
		$body->write($controller->detalhes($args['idLocal']));
		return $response->withStatus(200);
	});
});

//login fotografo
$app->get('/login', function ($request, $response, $args) use ($controllerLogin) {
	$controller = $controllerLogin;
	$body = $response->getBody();
	$body->write($controller->login());
    return $response->withStatus(200);
});

$app->run();