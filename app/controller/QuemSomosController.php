<?php
class QuemSomosController extends Controller
{
         
    public function quemsomos()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $local = $this->model('Local'); 
        $personalizacao = $this->model('PersonalizacaoLayout'); 
        $objSqlAdmin = new sql($GLOBALS['login_admin'], $GLOBALS['base_admin'], $GLOBALS['local_admin'], $GLOBALS['senha_admin']);
        $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL);
        if(!isset($confDominio[0]['id'])){ //se não existir o dominio no cadastro vai pegar os dados default primeticket
            $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL_DEFAULT);
        }
        $faviconHtml = '';
        if(isset($confDominio[0]['favicon']) && $confDominio[0]['favicon'] != ''){
            $favicon = URL_S3_FAVICON . $confDominio[0]['favicon'];
            $faviconHtml = '<link rel="icon" type="image/png" href="'.$favicon.'">';
        }
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $urlSistema = 'https://sistema.squareticket.com.br';
        }
        $logoLojaHtml = URL_S3_LOGO . $confDominio[0]['imagem_webp'];
       
        $cliente_admin = (int)$confDominio[0]['cliente_admin'];
        if((int)$cliente_admin > 1){
            $resultLocalCliente = $local->getLocalDominio($objSqlAdmin, $confDominio[0]['cliente_admin']);
            $objSqlCliente = new sql($resultLocalCliente[0]['bdLogin'], $resultLocalCliente[0]['bdBase'], $resultLocalCliente[0]['bdLocal'], $resultLocalCliente[0]['bdSenha']);
            $arrPersonalizacao = $personalizacao->personalizacao_motor($objSqlCliente);
            $quem_somos_html = isset($arrPersonalizacao[0]['mapa']) && $arrPersonalizacao[0]['mapa'] != '' ? $arrPersonalizacao[0]['mapa'] : 'Uma empresa que disponibiliza ferramentas para a venda online de ingressos, passaportes, tickets e entradas, dando mais visibilidade para o seu evento e impulsionando suas vendas. Além disso, proporcionamos um sistema que organiza a portaria ou bilheteria do seu evento, aprimorando a gestão do seu negócio e evitando erros.';
        } else {
            $quem_somos_html = 'Uma empresa que disponibiliza ferramentas para a venda online de ingressos, passaportes, tickets e entradas, dando mais visibilidade para o seu evento e impulsionando suas vendas. Além disso, proporcionamos um sistema que organiza a portaria ou bilheteria do seu evento, aprimorando a gestão do seu negócio e evitando erros.';
        }

        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Quem Somos',
                'version' => date('YmdHis'),
                'pagina' => 'quemsomos',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'quem_somos' => $quem_somos_html
            )
        );

        return $this->twig->render('quemsomos.html', $values);
    }
}