<?php
class EventoController extends Controller
{
         
    public function detalhes($idLocal, $idEvento)
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $local = $this->model('Local'); 
        $redes = $this->model('Redes'); 
        $eventos = $this->model('Eventos');
        $objSqlAdmin = new sql($GLOBALS['login_admin'], $GLOBALS['base_admin'], $GLOBALS['local_admin'], $GLOBALS['senha_admin']);
        $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL);
        if(!isset($confDominio[0]['id'])){ //se não existir o dominio no cadastro vai pegar os dados default primeticket
            $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL_DEFAULT);
        }
        $faviconHtml = '';
        if(isset($confDominio[0]['favicon'])){
            $favicon = URL_S3_FAVICON . $confDominio[0]['favicon'];
            $faviconHtml = '<link rel="icon" type="image/png" href="'.$favicon.'">';
        }
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $urlSistema = 'https://sistema.squareticket.com.br';
        }
        $logoLojaHtml = URL_S3_LOGO . $confDominio[0]['imagem_webp'];
        $resultLocalCliente = $local->getLocalDominio($objSqlAdmin, $confDominio[0]['cliente_admin']);
        $objSqlClienteLocal = new sql($resultLocalCliente[0]['bdLogin'], $resultLocalCliente[0]['bdBase'], $resultLocalCliente[0]['bdLocal'], $resultLocalCliente[0]['bdSenha']);
        $resultLocal = $confDominio[0]['cliente_admin'] == 1 ? $local->getLocalAdmin($objSqlAdmin, $idLocal) : $local->getLocalTerceiro($objSqlAdmin, $idLocal, $objSqlClienteLocal, $confDominio[0]['cliente_admin']);
        
        $objSqlCliente = new sql($resultLocal[0]['bdLogin'], $resultLocal[0]['bdBase'], $resultLocal[0]['bdLocal'], $resultLocal[0]['bdSenha']);
        $nomeLocal = $resultLocal[0]['tipo'] == 'F' ? $resultLocal[0]['nome'] : ( $resultLocal[0]['fantasia'] != '' ? $resultLocal[0]['fantasia'] : $resultLocal[0]['razao'] );
        $cidade = $resultLocal[0]['cidade'];
        $cidade_g = $resultLocal[0]['cidade_g'];
        $estado = $resultLocal[0]['estado'];
        $estado_g = $resultLocal[0]['estado_g'];

        $arrEvento   = $eventos->getEvento($objSqlCliente, $idEvento);
        $nomeEvento  = isset($arrEvento[0]['nomeGrupo']) && $arrEvento[0]['nomeGrupo'] != '' ? $arrEvento[0]['nomeGrupo'] : '';
        $mesExtenso  = Uteis::getMesExtenso($arrEvento[0]['mes']);
        $ano         = (int)$arrEvento[0]['ano'];
        if((int)$arrEvento[0]['mes'] == (int)$arrEvento[0]['mes_final'] && (int)$arrEvento[0]['ano'] == (int)$arrEvento[0]['ano_final']){
            $data_evento = 'De ' . ( intval($arrEvento[0]['diaInicial']) < 9 ? '0'.$arrEvento[0]['diaInicial'] : $arrEvento[0]['diaInicial'] ) . ' - ' . ( intval($arrEvento[0]['diaFinal']) < 9 ? '0'.$arrEvento[0]['diaFinal'] : $arrEvento[0]['diaFinal'] ) . ' de ' . $mesExtenso . ' de ' . $ano;
        } else {
            $mesExtenso2 = Uteis::getMesExtenso($arrEvento[0]['mes_final']);
            if((int)$arrEvento[0]['ano'] < (int)$arrEvento[0]['ano_final']){
                $ano2        = (int)$arrEvento[0]['ano_final'];
                $data_evento = 'De ' . ( intval($arrEvento[0]['diaInicial']) < 9 ? '0'.$arrEvento[0]['diaInicial'] : $arrEvento[0]['diaInicial'] ) . ' de ' . $mesExtenso . ' de ' . $ano . ' - ' . ( intval($arrEvento[0]['diaFinal']) < 9 ? '0'.$arrEvento[0]['diaFinal'] : $arrEvento[0]['diaFinal'] ) . ' de ' . $mesExtenso2 . ' de ' . $ano2;
            } else {
                $data_evento = 'De ' . ( intval($arrEvento[0]['diaInicial']) < 9 ? '0'.$arrEvento[0]['diaInicial'] : $arrEvento[0]['diaInicial'] ) . ' de ' . $mesExtenso . ' - ' . ( intval($arrEvento[0]['diaFinal']) < 9 ? '0'.$arrEvento[0]['diaFinal'] : $arrEvento[0]['diaFinal'] ) . ' de ' . $mesExtenso2 . ' de ' . $ano;
            }
        }

        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Detalhes do Evento',
                'version' => date('YmdHis'),
                'pagina' => 'evento',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'data_evento' => $data_evento,
                'nomeLocal' => $nomeLocal,
                'estado' => $estado,
                'estado_g' => $estado_g,
                'cidade' => $cidade,
                'cidade_g' => $cidade_g,
                'nomeEvento' => $nomeEvento,
                'idLocal' => $idLocal,
                'titleLocal' => Uteis::urltitle($nomeLocal)
            )
        );

        return $this->twig->render('detalhes-evento-expirado.html', $values);
    }
}