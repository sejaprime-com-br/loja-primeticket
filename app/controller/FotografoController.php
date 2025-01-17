<?php
class FotografoController extends Controller
{
         
    public function home()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $user = $this->model('User'); 
        $evento = $this->model('Eventos');
        $local = $this->model('Local');
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

        $idFotografo   = (int)$_SESSION[PREFIX]['idFotografo'];
        $objSqlCliente = $_SESSION[PREFIX]['objSqlCliente'];
        $arrUser       = $user->getUserId($idFotografo, $objSqlCliente);
        $fotografo     = isset($arrUser[0]['nome']) ? $arrUser[0]['nome'] : 'Fotografo';
        $redirecionar_admin = '';

        $lista_eventos = $evento->getEventosFotografo($objSqlCliente, $idFotografo);
        $idLocal       = (int)$_SESSION[PREFIX]['idLocal'];
        $resultLocal   = $local->getLocalAdmin($objSqlAdmin, $idLocal);
        $cidade_g      = $resultLocal[0]['cidade_g'];
        $estado_g      = $resultLocal[0]['estado_sigla'];
        $nomeLocal     = $resultLocal[0]['tipo'] == 'F' ? $resultLocal[0]['nome'] : ( $resultLocal[0]['fantasia'] != '' ? $resultLocal[0]['fantasia'] : $resultLocal[0]['razao'] );
        $cidadeEstado  = $cidade_g . ' - ' . $estado_g;
        $urlFotoS3     = URL_S3 . '/' . $idLocal . '/';

        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Painel Fotógrafo',
                'version' => date('YmdHis'),
                'pagina' => 'fotografo',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'fotografo' => $fotografo,
                'redirecionar_admin' => $redirecionar_admin,
                'lista_eventos' => $lista_eventos,
                'nomeLocal' => $nomeLocal,
                'cidadeEstado' => $cidadeEstado,
                'urlFotoS3' => $urlFotoS3
            )
        );

        return $this->twig->render('admin_fotografo.html', $values);
    }

    public function fotos($idEvento)
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $user = $this->model('User'); 
        $evento = $this->model('Eventos'); 
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

        $objSqlCliente = $_SESSION[PREFIX]['objSqlCliente'];
        $idFotografo   = (int)$_SESSION[PREFIX]['idFotografo'];
        $arrUser       = $user->getUserId($idFotografo, $objSqlCliente);
        $fotografo     = isset($arrUser[0]['nome']) ? $arrUser[0]['nome'] : 'Fotografo';

        $arrEventoFotografo = $evento->getEventoFotografo($objSqlCliente, $idEvento, $idFotografo);
        $redirecionar_admin = '';
        if(!isset($arrEventoFotografo[0]['id'])){
            $redirecionar_admin = $this->url . 'admin';
        }

        $nomeEvento = isset($arrEventoFotografo[0]['nomeGrupo']) && $arrEventoFotografo[0]['nomeGrupo'] != '' ? $arrEventoFotografo[0]['nomeGrupo'] : '';
        
        $arrFotosEvento = $evento->getFotosEventoFotografo($objSqlCliente, $idEvento, $idFotografo);
        $txtFotos       = isset($arrFotosEvento[0]) && $arrFotosEvento != '' ? ( count($arrFotosEvento) == 0 ? 'Foto' : 'Fotos' ) : '';
        $totalFotos     = isset($arrFotosEvento[0]) && $arrFotosEvento != '' ? count($arrFotosEvento) . ' ' . $txtFotos : '';
        $idLocal        = (int)$_SESSION[PREFIX]['idLocal'];
        $urlFotoS3      = URL_S3 . '/' . $idLocal . '/';
    
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Painel Fotógrafo',
                'version' => date('YmdHis'),
                'pagina' => 'fotografo',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'fotografo' => $fotografo,
                'redirecionar_admin' => $redirecionar_admin,
                'nomeEvento' => $nomeEvento,
                'totalFotos' => $totalFotos,
                'fotosEvento' => $arrFotosEvento,
                'urlFotoS3' => $urlFotoS3,
                'idEvento' => $idEvento
            )
        );

        return $this->twig->render('admin_fotos.html', $values);
    }

}