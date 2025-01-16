<?php
class LocalController extends Controller
{
         
    public function detalhes($idLocal)
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $local = $this->model('Local'); 
        $personalizacao = $this->model('PersonalizacaoLayout'); 
        $banner = $this->model('BannerMotor'); 
        $redes = $this->model('Redes'); 
        $eventos = $this->model('Eventos');

        $objSqlAdmin = new sql($GLOBALS['login_admin'], $GLOBALS['base_admin'], $GLOBALS['local_admin'], $GLOBALS['senha_admin']);
        $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL);
        if(!isset($confDominio[0]['id'])){ //se não existir o dominio no cadastro vai pegar os dados default primeticket
            $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL_DEFAULT);
        }

        $redirect_local = '';
        if(isset($confDominio[0]['id'])){
            if((int)$idLocal == 1){
                $redirect_local = $this->url;
            }
        
            $resultLocalCliente = $local->getLocalDominio($objSqlAdmin, $confDominio[0]['cliente_admin']);
            $objSqlClienteLocal = new sql($resultLocalCliente[0]['bdLogin'], $resultLocalCliente[0]['bdBase'], $resultLocalCliente[0]['bdLocal'], $resultLocalCliente[0]['bdSenha']);
            $resultLocal = $confDominio[0]['cliente_admin'] == 1 ? $local->getLocalAdmin($objSqlAdmin, $idLocal) : $local->getLocalTerceiro($objSqlAdmin, $idLocal, $objSqlClienteLocal, $confDominio[0]['cliente_admin']);
            if(!isset($resultLocal[0]['id'])){
                $redirect_local = $this->url;
            }
        }

        $ingresso_id = trim($resultLocal[0]['ingresso_id']);
        $loja_motor  = trim($resultLocal[0]['motor']);
        $objSqlCliente = new sql($resultLocal[0]['bdLogin'], $resultLocal[0]['bdBase'], $resultLocal[0]['bdLocal'], $resultLocal[0]['bdSenha']);
        $arrPersonalizacao = $personalizacao->personalizacao_motor($objSqlCliente);

        $arrBannerMotor = $banner->banner_motor($objSqlCliente);
        $arquivoBanner = isset($arrBannerMotor[0]['arquivo_webp']) && $arrBannerMotor[0]['arquivo_webp'] != '' ? $arrBannerMotor[0]['arquivo_webp'] : ( isset($arrBannerMotor[0]['arquivo']) && $arrBannerMotor[0]['arquivo'] != '' ? $arrBannerMotor[0]['arquivo'] : '' );
        $banner_local = $arquivoBanner != '' ? URL_S3 . '/' . $idLocal . '/' . $arquivoBanner : $this->url . 'public/img/boate-noturna-scaled.jpg';

        $nomeLocal = $resultLocal[0]['tipo'] == 'F' ? $resultLocal[0]['nome'] : ( $resultLocal[0]['fantasia'] != '' ? $resultLocal[0]['fantasia'] : $resultLocal[0]['razao'] );
        $redes_sociais_html = '';
        $informacoes_local_html = '';
        $localizacao_html = '';
        $horario_funcionamento_html = '';
        $cidade = $resultLocal[0]['cidade'];
        $cidade_g = $resultLocal[0]['cidade_g'];
        $estado = $resultLocal[0]['estado'];
        $estado_g = $resultLocal[0]['estado_g'];

        $faviconHtml = '';
        if(isset($confDominio[0]['favicon'])){
            $favicon = URL_S3_FAVICON . $confDominio[0]['favicon'];
            $faviconHtml = '<link rel="icon" type="image/png" href="'.$favicon.'">';
        }
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $urlSistema = 'https://sistema.squareticket.com.br';
        }

        $logoLojaHtml = URL_S3_LOGO . $confDominio[0]['imagem_webp'];

        $URL_MOTOR_LOCAL = $loja_motor == 'motor_eventos' ? URL_MOTOR_EVENTO : URL_MOTOR_INGRESSO;
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $URL_MOTOR_LOCAL = $loja_motor == 'motor_eventos' ? URL_MOTOR_EVENTO_SQUARE : URL_MOTOR_INGRESSO_SQUARE;
        }
        $ticket_id       = $loja_motor == 'motor_eventos' ? COMPANY_ID : TICKET_ID;

        $arrRedesSociais = $redes->getRedes($objSqlCliente);
        if(isset($arrRedesSociais[0])){
            $redes_sociais_html = '
            <ul class="rede-local">';
                foreach($arrRedesSociais as $arrR){
                    if((int)$arrR['rede'] == 1 && $arrR['link'] != ''){
                        $redes_sociais_html .= '<li class="facebook">
                            <a href="'.$arrR['link'].'" target="_blank">
                                <i class="fab fa-facebook"></i>
                            </a>
                        </li>';
                    }
                    if((int)$arrR['rede'] == 2 && $arrR['link'] != ''){
                        $redes_sociais_html .= '<li class="instagram">
                            <a href="'.$arrR['link'].'" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>';
                    }
                    if((int)$arrR['rede'] == 3 && $arrR['link'] != ''){
                        $redes_sociais_html .= '<li class="youtube">
                            <a href="'.$arrR['link'].'" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>';
                    }
                }
            $redes_sociais_html .= '</ul>';
        }

        if(isset($arrPersonalizacao[0]['horario_funcionamento']) && $arrPersonalizacao[0]['horario_funcionamento'] != ''){
            $horario_funcionamento_html .= '
            <li class="info-1">
                <i class="far fa-clock"></i>
                <div class="txt">
                    <span>Horário de funcionamento</span>
                    <span>'.$arrPersonalizacao[0]['horario_funcionamento'].'</span>
                </div>
            </li>';
        }

        if(isset($arrPersonalizacao[0]['mapa']) && $arrPersonalizacao[0]['mapa'] != ''){
            $localizacao_html .= '
            <a class="link-gps mb-3" href="#">
                <li class="info-1">
                    <i class="fas fa-location-arrow"></i>
                    <div class="txt">
                        <span> Como Chegar</span>
                        <span>Localização</span>
                        <small>Clique aqui !</small>
                    </div>
                </li>
            </a>';
        }

        $informacoes_local_html = 
        '<h2 class="mb-3">Informações do local</h2>
        <div class="d-flex justify-content-between flex-wrap">
            <ul class="d-flex">
                ' . $horario_funcionamento_html . '
                ' . $localizacao_html . '
            </ul>
            ' . $redes_sociais_html . '
        </div>';

        $ultimos_eventos = '';
        $arrUltimosEventos = $eventos->getUltimosEventosEncerradosRand($objSqlCliente);
        if(isset($arrUltimosEventos[0]['id'])){
            foreach($arrUltimosEventos as $arrEv){
                $fotoEvento   = $arrEv['imagem_webp'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEv['imagem_webp'] : ( $arrEv['imagem'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEv['imagem'] : URL_IMAGE_SEMFOTO );
                $mes_evento   = (int)$arrEv['mes'];
                $mes_extenso  = Uteis::getMesExtenso($mes_evento);
                $dia_extenso  = Uteis::getDiaExtenso($arrEv['data']);
                $idEvento     = (int)$arrEv['id'];
                $tituloEvento = Uteis::urltitle($arrEv['nomeGrupo']);
                $detalhes_evento = $this->url . 'detalhes-evento/'.$idLocal.'/'.$idEvento.'/'.$tituloEvento;
                $ultimos_eventos .= 
                        '<div class="col-lg-3">
                            <a href="'.$detalhes_evento.'">
                                <div class="card-evento">
                                    <div class="card-img">
                                        <div class="calendar">
                                            <div class="calendar-body">
                                                <span class="month-name">'.$mes_extenso.'</span>
                                                <span class="day-name">'.$dia_extenso.'</span>
                                                <span class="date-number">'.$arrEv['dia'].'</span>
                                                <span class="year">'.$arrEv['ano'].'</span>
                                            </div>
                                        </div>
                                        <!--<span class="tag">
                                            Balada
                                        </span>-->
                                        <img src="'.$fotoEvento.'" alt="img-evento">
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">' . $arrEv['nomeGrupo'] . '</h5>
                                        <p class="card-text">
                                        
                                        </p>
                                        <ul>
                                            <li>
                                                <a class="link-local" href="#">
                                                    <i class="bx bx-map"></i> ' . $nomeLocal . '
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                        </div>';
            }
        }

        $eventos_abertos = '';
        $arrEventosAbertos = $eventos->getEventosAbertos($objSqlCliente);
        if(isset($arrEventosAbertos[0]['id'])){
            foreach($arrEventosAbertos as $arrEvA){
                $fotoEvento  = $arrEvA['imagem_webp'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem_webp'] : ( $arrEvA['imagem'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem'] : URL_IMAGE_SEMFOTO );
                $mes_evento  = (int)$arrEvA['mes'];
                $mes_extenso = Uteis::getMesExtenso($mes_evento);
                $dia_extenso = Uteis::getDiaExtenso($arrEvA['data']);
                $idEvento    = (int)$arrEvA['id'];
                $arrValorEv  = $eventos->getMenorValorEvento($objSqlCliente, $idEvento);
                $menor_valor = Uteis::formataValorBR($arrValorEv[0]['valorVarejo']);
                $categoria   = 1;
                $urlMotor    = $URL_MOTOR_LOCAL . 'index.php?'.$ticket_id.'=' . $ingresso_id . '&acao=detalhes-produto&grupo=' . (int)$idEvento . '&categoria=' . (int)$categoria;
                $eventos_abertos .= 
                '<div class="slide">
                    <a href="'.$urlMotor.'" target="_blank">
                        <div class="card-evento">
                            <div class="card-img">
                                <div class="calendar">
                                    <div class="calendar-body">
                                        <span class="month-name">'.$mes_extenso.'</span>
                                        <span class="day-name">'.$dia_extenso.'</span>
                                        <span class="date-number">'.$arrEvA['dia'].'</span>
                                        <span class="year">'.$arrEvA['ano'].'</span>
                                    </div>
                                </div>
                                <!-- <span class="tag">
                                    Balada
                                </span> -->
                                <img src="'.$fotoEvento.'" alt="img-evento">
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="dc">
                                    <a href="'.$urlMotor.'" target="_blank"><h5 class="card-title">'.$arrEvA['nomeGrupo'].'</h5></a>
                                    <ul>
                                        <li> 
                                            <a class="link-local" href="#">
                                                <i class="bx bx-map"></i> ' . $nomeLocal . '</li>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div style="flex-shrink: 0;" class="valor-e d-flex flex-column">
                                    <small>Apartir de </small>
                                    <small>R$ <span class="preco-evento">'.$menor_valor.'</span></small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            }
        }
       
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Detalhes do Local',
                'version' => date('YmdHis'),
                'pagina' => 'evento',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'redirect' => $redirect_local,
                'informacoes_local' => $informacoes_local_html,
                'banner_local' => $banner_local,
                'nomeLocal' => $nomeLocal,
                'estado' => $estado,
                'estado_g' => $estado_g,
                'cidade' => $cidade,
                'cidade_g' => $cidade_g,
                'ultimos_eventos' => $ultimos_eventos,
                'eventos_abertos' => $eventos_abertos
            )
        );

        return $this->twig->render('detalhes-local.html', $values);
    }
}