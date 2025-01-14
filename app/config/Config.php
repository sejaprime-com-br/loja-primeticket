<?php
  $base_url    = $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://'.$_SERVER['HTTP_HOST'].'/nucleo/loja-primeticket/' : 'https://' . $_SERVER['HTTP_HOST'] . '/';
  $dominio_url = $_SERVER['SERVER_NAME'] == 'localhost' ? $_SERVER['HTTP_HOST'].'/nucleo/loja-primeticket' : $_SERVER['HTTP_HOST'];
  define('PREFIX', 'SIS_LOJA_PRIME');
  define('LIMITE_PAG', 50);
  define('DOMINIO_URL', $dominio_url);
  define('DOMINIO_URL_DEFAULT', 'primeticket.com.br');

  if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost:8080') {
    $GLOBALS['base_admin'] = 'nucleode_admin';
    $GLOBALS['login_admin'] = 'root';
    $GLOBALS['local_admin'] = 'localhost';
    $GLOBALS['senha_admin'] = 'root';
    $GLOBALS['port_admin']  = 3306;
    $GLOBALS['AMBIENTE'] = 'DEV';
  } else {
    $GLOBALS['base_admin'] = 'nucleode_admin';
    $GLOBALS['login_admin'] = 'root';
    $GLOBALS['local_admin'] = '104.131.90.95';
    $GLOBALS['senha_admin'] = '+Dnw$3S5%][6pccc';
    $GLOBALS['port_admin']  = 3306;
    $GLOBALS['AMBIENTE'] = 'PROD';
  }

  // incluindo o arquivo de conexão com o banco de dados
  require_once 'sql.php';
?>