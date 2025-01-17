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
require_once 'app/controller/FotografoController.php';
require_once 'app/controller/PoliticaController.php';
require_once 'app/controller/TermosController.php';

$controllerHome = new HomeController();
$controllerQuemSomos = new QuemSomosController();
$controllerContato = new ContatoController();
$controllerEvento = new EventoController();
$controllerLocal = new LocalController();
$controllerLogin = new LoginController();
$controllerFotografo = new FotografoController();
$controllerPolitica = new PoliticaController();
$controllerTermos = new TermosController();

define('BASE_URL', $base_url);
$base_urlAdmin = $base_url . 'admin';

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

$middleware_logado = function ($request, $response, $next) use ($array_conf) {
	$PREFIX = $array_conf['PREFIX'];
	if(isset($_SESSION[$PREFIX]['idFotografo']) && isset($_SESSION[$PREFIX]['idFotografo'])){
    	$newResponse = $next($request, $response);
    	return $newResponse;
	} else {
		return $response->withHeader('Location', $array_conf['BASE_URL'] . 'login');
	}
};

$middleware_logado_login = function ($request, $response, $next) use ($array_conf) {
	$PREFIX = $array_conf['PREFIX'];
	if(isset($_SESSION[$PREFIX]['idFotografo']) && isset($_SESSION[$PREFIX]['idFotografo'])){
    	return $response->withHeader('Location', $array_conf['BASE_URL'] . 'admin');
	} else {
		$newResponse = $next($request, $response);
    	return $newResponse;
	}
};

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

//politica privacidade
$app->get('/politica-privacidade', function ($request, $response, $args) use ($controllerPolitica) {
	$controller = $controllerPolitica;
	$body = $response->getBody();
	$body->write($controller->politica());
    return $response->withStatus(200);
});

//termos condicoes
$app->get('/termos-condicoes', function ($request, $response, $args) use ($controllerTermos) {
	$controller = $controllerTermos;
	$body = $response->getBody();
	$body->write($controller->termos());
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
})->add($middleware_logado_login);

//envio form login fotografo
$app->post('/auth', function ($request, $response, $args) use ($controllerLogin) {
	$controller = $controllerLogin;
	$body = $response->getBody();
	$body->write(json_encode($controller->auth($request->getParsedBody())));
    return $response->withStatus(200);
});

//envio form login fotografo
$app->post('/logout', function ($request, $response, $args) use ($controllerLogin) {
	$controller = $controllerLogin;
	$body = $response->getBody();
	$body->write(json_encode($controller->logout()));
    return $response->withStatus(200);
});

//esqueci minha senha fotografo
$app->get('/esqueci-minha-senha', function ($request, $response, $args) use ($controllerLogin) {
	$controller = $controllerLogin;
	$body = $response->getBody();
	$body->write($controller->esqueci_senha());
    return $response->withStatus(200);
});

//area de fotografo
$app->get('/admin', function ($request, $response, $args) use ($controllerFotografo) {
	$controller = $controllerFotografo;
	$body = $response->getBody();
	$body->write($controller->home());
    return $response->withStatus(200);
})->add($middleware_logado);

//area de fotos fotografo
$app->group('/admin/fotos', function () use ($app, $controllerFotografo, $base_url, $base_urlAdmin, $middleware_logado) {
	$app->get('/', function ($request, $response, $args) use ($app, $base_url, $base_urlAdmin) {
		return $response->withHeader('Location', $base_urlAdmin);
	});

	$app->get('/{idEvento}', function ($request, $response, $args) use ($controllerFotografo) {
		$controller = $controllerFotografo;
		$body = $response->getBody();
		$body->write($controller->fotos($args['idEvento']));
		return $response->withStatus(200);
	})->add($middleware_logado);

	$app->get('/{idEvento}/{tituloEvento}', function ($request, $response, $args) use ($controllerFotografo) {
		$controller = $controllerFotografo;
		$body = $response->getBody();
		$body->write($controller->fotos($args['idEvento']));
		return $response->withStatus(200);
	})->add($middleware_logado);
});

//envio form login fotografo
$app->post('/delete/foto', function ($request, $response, $args) use ($controllerFotografo) {
	$controller = $controllerFotografo;
	$body = $response->getBody();
	$body->write(json_encode($controller->deleteFoto($request->getParsedBody())));
    return $response->withStatus(200);
});

$app->run();