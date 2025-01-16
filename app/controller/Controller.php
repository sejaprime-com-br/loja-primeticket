<?php
ini_set('max_execution_time', 0); //Isso aqui é para resetar e não dar mais erro de tempo de execução
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../helpers/Uteis.php';
require __DIR__ . '/../../vendor/autoload.php';
Twig_Autoloader::register();

$motor_ingresso = $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://'.$_SERVER['HTTP_HOST'].'/nucleo/ticket2/' : 'https://ticket.nucleodeturismo.com.br/';
$motor_evento   = $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://'.$_SERVER['HTTP_HOST'].'/nucleo/eventos/' : 'https://eventos.nucleodeturismo.com.br/';
$motor_ingresso_square = $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://'.$_SERVER['HTTP_HOST'].'/nucleo/ticket2/' : 'https://ticket.squareticket.com.br/';
$motor_evento_square   = $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://'.$_SERVER['HTTP_HOST'].'/nucleo/eventos/' : 'https://eventos.squareticket.com.br/';

DEFINE('url', $base_url);
$DEV = $_SERVER['SERVER_NAME'] == 'localhost' ? TRUE : FALSE;
DEFINE('DEV', $DEV);
DEFINE('AMBIENTE', $_SERVER['SERVER_NAME'] == 'localhost' ? 'DEV' : 'PROD');
DEFINE('URL_S3', 'https://primeimg2.nyc3.cdn.digitaloceanspaces.com/arquivos');
DEFINE('URL_S3_FAVICON', 'https://primeimg2.nyc3.cdn.digitaloceanspaces.com/arquivos/1/');
DEFINE('URL_S3_LOGO', 'https://primeimg2.nyc3.cdn.digitaloceanspaces.com/arquivos/1/');
DEFINE('URL_IMAGE_SEMFOTO', $base_url . 'public/img/sem_foto.jpg?v=' . date('YmdHis'));
DEFINE('URL_MOTOR_INGRESSO', $motor_ingresso);
DEFINE('URL_MOTOR_EVENTO', $motor_evento);
DEFINE('URL_MOTOR_INGRESSO_SQUARE', $motor_ingresso_square);
DEFINE('URL_MOTOR_EVENTO_SQUARE', $motor_evento_square);
DEFINE('TICKET_ID', 'ticket_id');
DEFINE('COMPANY_ID', 'company_id');

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