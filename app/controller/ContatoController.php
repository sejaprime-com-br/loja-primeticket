<?php
class ContatoController extends Controller
{
         
    public function contato()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $local = $this->model('Local'); 
        $personalizacao = $this->model('PersonalizacaoLayout'); 
        $contato = $this->model('Contato'); 
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
       
        $enderecoContato = '';
        $telefoneContato = '';
        $emailContato = '';        
        $horario_funcionamento = '';
        $cliente_admin = (int)$confDominio[0]['cliente_admin'];
        if((int)$cliente_admin > 1){
            $resultLocalCliente    = $local->getLocalDominio($objSqlAdmin, $confDominio[0]['cliente_admin']);
            $objSqlCliente         = new sql($resultLocalCliente[0]['bdLogin'], $resultLocalCliente[0]['bdBase'], $resultLocalCliente[0]['bdLocal'], $resultLocalCliente[0]['bdSenha']);
            $arrInfos              = $contato->getInformacoes($objSqlCliente);
            $arrPersonalizacao     = $personalizacao->personalizacao_motor($objSqlCliente);
            $enderecoTxt           = isset($arrInfos[0]['endereco']) && $arrInfos[0]['endereco'] != '' ? $arrInfos[0]['endereco'] . ( $arrInfos[0]['bairro'] != '' ? ' - ' . $arrInfos[0]['bairro'] : '' ) . ( $arrInfos[0]['numero'] != '' ? ', ' . $arrInfos[0]['numero'] : '' ) . ( $arrInfos[0]['complemento'] != '' ? ' - ' . $arrInfos[0]['complemento'] : '' ) . ( $arrInfos[0]['cidade_g'] != '' ? ' - ' . $arrInfos[0]['cidade_g'] : '' ) . ( $arrInfos[0]['uf'] != '' ? ' / ' . $arrInfos[0]['uf'] : '' ) : '';
            $enderecoContato       = $enderecoTxt;
            $telefoneContato       = isset($arrInfos[0]['whatsapp']) && $arrInfos[0]['whatsapp'] != '' ? $arrInfos[0]['whatsapp'] : ( $arrInfos[0]['telefone'] != '' ? $arrInfos[0]['telefone'] : '' );
            $emailContato          = isset($arrInfos[0]['email']) && $arrInfos[0]['email'] != '' ? $arrInfos[0]['email'] : '';
            $horario_funcionamento = isset($arrPersonalizacao[0]['horario_funcionamento']) && $arrPersonalizacao[0]['horario_funcionamento'] != '' ? $arrPersonalizacao[0]['horario_funcionamento'] : '';
        } else {
            $enderecoContato = 'Avenida Sete de Setembro - Cidade Alta, 130 - Sala 1 - Araranguá / SC';
            $telefoneContato = '(48) 99691-1921';
            $emailContato    = 'contato@sejaprime.com.br';
            $horario_funcionamento = 'Seg - Sex 8:00 - 22:00<br>Sab 8:00 - 22:00';
        }

        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Contato',
                'version' => date('YmdHis'),
                'pagina' => 'contato',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'endereco' => $enderecoContato,
                'telefone' => $telefoneContato,
                'email' => $emailContato,
                'horario' => $horario_funcionamento
            )
        );

        return $this->twig->render('contato.html', $values);
    }
}