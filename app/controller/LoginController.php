<?php
class LoginController extends Controller
{
         
    public function login()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
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
       
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Login Fotográfo',
                'version' => date('YmdHis'),
                'pagina' => 'login',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml
            )
        );

        return $this->twig->render('login.html', $values);
    }

    public function esqueci_senha()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
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
       
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Esqueci Minha Senha Fotográfo',
                'version' => date('YmdHis'),
                'pagina' => 'esqueci-minha-senha',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml
            )
        );

        return $this->twig->render('esqueci.html', $values);
    }

    public function auth($arrDadoLogin){
        if (!isset($_SESSION)) {
            session_start();
        }  

        $booSucesso = false;
        $mensagem = '';
        $local = $this->model('Local');
        $user = $this->model('User');
        
        if(isset($arrDadoLogin['login_codigo']) && $arrDadoLogin['login_codigo'] != '' && isset($arrDadoLogin['login_user']) && $arrDadoLogin['login_user'] != '' && isset($arrDadoLogin['login_senha']) && $arrDadoLogin['login_senha'] != ''){
            $arrDadoLogin['login_user']   = trim($arrDadoLogin['login_user']);
            $arrDadoLogin['login_senha']  = trim($arrDadoLogin['login_senha']);

            if(Uteis::validaCodigoAcesso(trim($arrDadoLogin['login_codigo']))){
                $arrDadoLogin['login_codigo'] = trim($arrDadoLogin['login_codigo']);
                $objSqlAdmin = new sql($GLOBALS['login_admin'], $GLOBALS['base_admin'], $GLOBALS['local_admin'], $GLOBALS['senha_admin']);
                $booSucesso = true;
                $arrItem = $local->getLocalCodigo($objSqlAdmin, $arrDadoLogin['login_codigo']);
                if (isset($arrItem[0]) && $arrItem != '') {
                    $arrItem = $arrItem[0];
    
                    if(isset($arrItem['ativo']) && intval($arrItem['ativo']) == 0){
                        $booSucesso = false;
                        $mensagem = 'Usuário não está mais ativo!'; 
                    } else {
                        $objSqlCliente = new sql($arrItem['bdLogin'], $arrItem['bdBase'], $arrItem['bdLocal'], $arrItem['bdSenha']);
                        $arrLogin = $user->getUser($arrDadoLogin['login_user'], $arrDadoLogin['login_senha'], $objSqlCliente);
    
                        if (isset($arrLogin[0]['funcionario_ativo']) && intval($arrLogin[0]['funcionario_ativo']) == 0) {
                            $booSucesso = false;
                            $mensagem = 'Usuário não está ativo!'; 
                        } else if (isset($arrLogin[0]) && $arrLogin != '') {
                            $usuario_id = $arrLogin[0]['id'];
                            $_SESSION[PREFIX]['idFotografo']   = (int)$usuario_id;
                            $_SESSION[PREFIX]['idLocal']       = (int)$arrItem['id'];
                            $_SESSION[PREFIX]['objSqlCliente'] = $objSqlCliente;
                        } else {
                            $booSucesso = false;
                            $mensagem = 'Usuário ou senha incorreta!'; 
                        }
                    }
                } else {
                    $booSucesso = false;
                    $mensagem = 'Código de acesso incorreto!'; 
                }
            } else {
                $booSucesso = false;
                $mensagem = 'Código de acesso incorreto!'; 
            }
        } else {
            $booSucesso = false; 
            $mensagem = 'Informe os campos obrigatórios!';
        }

        return array('sucesso' => $booSucesso, 'mensagem' => $mensagem);
    }

    public function logout(){
        if (!isset($_SESSION)) {
            session_start();
        }  

        if(isset($_SESSION[PREFIX]['idFotografo'])){
            unset($_SESSION[PREFIX]['idFotografo']);
            if(isset($_SESSION[PREFIX]['objSqlCliente'])){
                unset($_SESSION[PREFIX]['objSqlCliente']);
            }
            if(isset($_SESSION[PREFIX]['idLocal'])){
                unset($_SESSION[PREFIX]['idLocal']);
            }
            return array('sucesso' => true, 'mensagem' => '');
        } else {
            return array('sucesso' => false, 'mensagem' => '');
        }
    }

}