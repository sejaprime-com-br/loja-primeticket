<?php
ini_set('max_execution_time', 0); //Isso aqui é para resetar e não dar mais erro de tempo de execução
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../helpers/Uteis.php';
require __DIR__ . '/../../vendor/autoload.php';
Twig_Autoloader::register();

DEFINE('url', $base_url);
$DEV = $_SERVER['SERVER_NAME'] == 'localhost' ? TRUE : FALSE;
DEFINE('DEV', $DEV);
DEFINE('AMBIENTE', $_SERVER['SERVER_NAME'] == 'localhost' ? 'DEV' : 'PROD');
DEFINE('URL_S3', 'https://primeimg2.nyc3.cdn.digitaloceanspaces.com/arquivos');
DEFINE('URL_S3_FAVICON', 'https://primeimg2.nyc3.cdn.digitaloceanspaces.com/arquivos/1/');
DEFINE('URL_S3_LOGO', 'https://primeimg2.nyc3.cdn.digitaloceanspaces.com/arquivos/1/');
DEFINE('URL_IMAGE_SEMFOTO', $base_url . 'public/img/sem_foto.jpg?v=' . date('YmdHis'));

class Controller
{
    public $url;
    public $twig;
    public $limte_pag;
    public $ambiente; // valida o ambiente se é dev ou prod
    public $title_sistema;

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem('app/views');
        $this->twig = new Twig_Environment($loader, [
            'debug' => true,
        ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->url = url;
        $this->limite_pag = LIMITE_PAG;
        $this->ambiente = AMBIENTE;
    }

    public function model($model){
        require_once __DIR__ . '/../model/'.$model.'.php';
        return new $model;
    }
}