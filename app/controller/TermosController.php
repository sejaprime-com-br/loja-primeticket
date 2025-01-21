<?php
class TermosController extends Controller
{
         
    public function termos()
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

        $termosHtml = '';
        $cliente_admin = (int)$confDominio[0]['cliente_admin'];
        $termosDefault = 'Uma empresa que disponibiliza ferramentas para a venda online de ingressos, passaportes, tickets e entradas, dando mais visibilidade para o seu evento e impulsionando suas vendas. Além disso, proporcionamos um sistema que organiza a portaria ou bilheteria do seu evento, aprimorando a gestão do seu negócio e evitando erros.';
        if((int)$cliente_admin > 1){
            $resultLocalCliente = $local->getLocalDominio($objSqlAdmin, $confDominio[0]['cliente_admin']);
            $objSqlCliente = new sql($resultLocalCliente[0]['bdLogin'], $resultLocalCliente[0]['bdBase'], $resultLocalCliente[0]['bdLocal'], $resultLocalCliente[0]['bdSenha']);
            $arrPersonalizacao = $personalizacao->politicaReserva($objSqlCliente);
            $termosHtml = isset($arrPersonalizacao[0]['descricao']) && $arrPersonalizacao[0]['descricao'] != '' ? $arrPersonalizacao[0]['descricao'] : $termosDefault;
        } else {
            $termosHtml = $termosDefault;
        }
       
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Termos e Condições',
                'version' => date('YmdHis'),
                'pagina' => 'quemsomos',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'termos' => $termosHtml
            )
        );

        return $this->twig->render('termos-e-condicoes.html', $values);
    }
}