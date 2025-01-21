<?php
class BuscaController extends Controller
{
         
    public function busca()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  

        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $categorias = $this->model('Categorias'); 
        $locais = $this->model('Local');
        $eventos = $this->model('Eventos');        
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

        $pagination = '';
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = explode('?', $request_uri);
        $pg = 1;
        $filters = null;
        $filters_pag = null;
        if(isset($request_uri[1])){
            $filtrosx = explode('&', $request_uri[1]);
            if(isset($filtrosx[0]) && isset($filtrosx)){
                $filtrosx[0] = explode('=', $filtrosx[0]);
                if(isset($filtrosx[0][0])){
                    if($filtrosx[0][0] == 'pg'){
                        $pg = $filtrosx[0][1];
                    } else {
                        if($filtrosx[0][0] == 'data'){
                            $filters[$filtrosx[0][0]] = $filtrosx[0][1];
                        } else if($filtrosx[0][0] == 'categoria'){
                            $filters[$filtrosx[0][0]] = $filtrosx[0][1];
                        } else if($filtrosx[0][0] == 'local'){
                            $filters[$filtrosx[0][0]] = $filtrosx[0][1];
                        } else if($filtrosx[0][0] == 'cidade'){
                            $filters[$filtrosx[0][0]] = $filtrosx[0][1];
                        } else if($filtrosx[0][0] == 'busca'){
                            $filters[$filtrosx[0][0]] = $filtrosx[0][1];
                        }
                        $filters_pag[$filtrosx[0][0]] = $filtrosx[0][1];
                    }
                }
            }

            if(isset($filtrosx[1]) && isset($filtrosx)){
                $filtrosx[1] = explode('=', $filtrosx[1]);
                if(isset($filtrosx[1][0])){
                    if($filtrosx[1][0] == 'pg'){
                        $pg = $filtrosx[1][1];
                    } else {
                        if($filtrosx[1][0] == 'data'){
                            $filters[$filtrosx[1][0]] = $filtrosx[1][1];
                        } else if($filtrosx[1][0] == 'categoria'){
                            $filters[$filtrosx[1][0]] = $filtrosx[1][1];
                        } else if($filtrosx[1][0] == 'local'){
                            $filters[$filtrosx[1][0]] = $filtrosx[1][1];
                        } else if($filtrosx[1][0] == 'cidade'){
                            $filters[$filtrosx[1][0]] = $filtrosx[1][1];
                        } else if($filtrosx[1][0] == 'busca'){
                            $filters[$filtrosx[1][0]] = $filtrosx[1][1];
                        }
                        $filters_pag[$filtrosx[1][0]] = $filtrosx[1][1];
                    }
                } 
            }

            if(isset($filtrosx[2]) && isset($filtrosx)){
                $filtrosx[2] = explode('=', $filtrosx[2]);
                if(isset($filtrosx[2][0])){
                    if($filtrosx[2][0] == 'pg'){
                        $pg = $filtrosx[2][1];
                    } else {
                        if($filtrosx[2][0] == 'data'){
                            $filters[$filtrosx[2][0]] = $filtrosx[2][1];
                        } else if($filtrosx[2][0] == 'categoria'){
                            $filters[$filtrosx[2][0]] = $filtrosx[2][1];
                        } else if($filtrosx[2][0] == 'local'){
                            $filters[$filtrosx[2][0]] = $filtrosx[2][1];
                        } else if($filtrosx[2][0] == 'cidade'){
                            $filters[$filtrosx[2][0]] = $filtrosx[2][1];
                        } else if($filtrosx[2][0] == 'busca'){
                            $filters[$filtrosx[2][0]] = $filtrosx[2][1];
                        }
                        $filters_pag[$filtrosx[2][0]] = $filtrosx[2][1];
                    }
                } 
            }

            if(isset($filtrosx[3]) && isset($filtrosx)){
                $filtrosx[3] = explode('=', $filtrosx[3]);
                if(isset($filtrosx[3][0])){
                    if($filtrosx[3][0] == 'pg'){
                        $pg = $filtrosx[3][1];
                    } else {
                        if($filtrosx[3][0] == 'data'){
                            $filters[$filtrosx[3][0]] = $filtrosx[3][1];
                        } else if($filtrosx[3][0] == 'categoria'){
                            $filters[$filtrosx[3][0]] = $filtrosx[3][1];
                        } else if($filtrosx[3][0] == 'local'){
                            $filters[$filtrosx[3][0]] = $filtrosx[3][1];
                        } else if($filtrosx[3][0] == 'cidade'){
                            $filters[$filtrosx[3][0]] = $filtrosx[3][1];
                        } else if($filtrosx[3][0] == 'busca'){
                            $filters[$filtrosx[3][0]] = $filtrosx[3][1];
                        }
                        $filters_pag[$filtrosx[3][0]] = $filtrosx[3][1];
                    }
                } 
            }

            if(isset($filtrosx[4]) && isset($filtrosx)){
                $filtrosx[4] = explode('=', $filtrosx[4]);
                if(isset($filtrosx[4][0])){
                    if($filtrosx[4][0] == 'pg'){
                        $pg = $filtrosx[4][1];
                    } else {
                        if($filtrosx[4][0] == 'data'){
                            $filters[$filtrosx[4][0]] = $filtrosx[4][1];
                        } else if($filtrosx[4][0] == 'categoria'){
                            $filters[$filtrosx[4][0]] = $filtrosx[4][1];
                        } else if($filtrosx[4][0] == 'local'){
                            $filters[$filtrosx[4][0]] = $filtrosx[4][1];
                        } else if($filtrosx[4][0] == 'cidade'){
                            $filters[$filtrosx[4][0]] = $filtrosx[4][1];
                        } else if($filtrosx[4][0] == 'busca'){
                            $filters[$filtrosx[4][0]] = $filtrosx[4][1];
                        }
                        $filters_pag[$filtrosx[4][0]] = $filtrosx[4][1];
                    }
                } 
            }
        }

        $limite_por_pagina = $this->limite_pag;
        $inicio = isset($pg) ? ($pg > 0 ? $pg - 1 : 0) : 0;
        $eventos_busca_html = '';

        $arrEventosTotal = $eventos->getEventosBuscaTotal($objSqlAdmin, $confDominio[0]['cliente_admin'], $filters);
        $totalEventos = isset($arrEventosTotal[0]['total']) ? $arrEventosTotal[0]['total'] : 0;
        $totalPages = ceil($totalEventos / $limite_por_pagina);

        /*$arrEventosBusca = $eventos->getEventosBusca($objSqlAdmin, $confDominio[0]['cliente_admin'], $inicio, $limite_por_pagina, $filters);
        if(isset($arrEventosBusca[0]['id'])){
            foreach($arrEventosBusca as $arrEvA){
                $nomeLocal   = $arrEvA['nomeEmp'];
                $ingresso_id = trim($arrEvA['ingresso_id']);
                $loja_motor  = trim($arrEvA['motor']);
                $URL_MOTOR_LOCAL = $loja_motor == 'motor_eventos' ? URL_MOTOR_EVENTO : URL_MOTOR_INGRESSO;
                if($confDominio[0]['dominio'] == 'squareticket.com.br'){
                    $URL_MOTOR_LOCAL = $loja_motor == 'motor_eventos' ? URL_MOTOR_EVENTO_SQUARE : URL_MOTOR_INGRESSO_SQUARE;
                }
                $ticket_id       = $loja_motor == 'motor_eventos' ? COMPANY_ID : TICKET_ID;

                $objSqlCliente = new sql($arrEvA['bdLogin'], $arrEvA['bdBase'], $arrEvA['bdLocal'], $arrEvA['bdSenha']);
                $idEvento    = (int)$arrEvA['id'];
                $idLocal      = (int)$arrEvA['cliente'];
                $idFornecedor = (int)$arrEvA['fornecedor'];
                $arrTagEvento = $eventos->getTagEvento($objSqlAdmin, $idEvento, $idFornecedor);
                $fotoEvento  = $arrEvA['imagem_webp'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem_webp'] : ( $arrEvA['imagem'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem'] : URL_IMAGE_SEMFOTO );
                $mes_evento  = (int)$arrEvA['mes'];
                $mes_extenso = Uteis::getMesExtenso($mes_evento);
                $dia_extenso = Uteis::getDiaExtenso($arrEvA['data']);
                $arrValorEv  = $eventos->getMenorValorEvento($objSqlCliente, $idEvento);
                $menor_valor = Uteis::formataValorBR($arrValorEv[0]['valorVarejo']);
                $urlMotor    = $URL_MOTOR_LOCAL . 'index.php?'.$ticket_id.'=' . $ingresso_id . '&acao=detalhes-produto&grupo=' . (int)$idEvento;
                $dia1 = (int)$arrEvA['dia'] < 10 ? '0'.$arrEvA['dia'] : $arrEvA['dia'];
                $dia2 = (int)$arrEvA['dia2'] < 10 ? '0'.$arrEvA['dia2'] : $arrEvA['dia2'];
                $mes1 = $arrEvA['mes'];
                $mes2 = $arrEvA['mes2'];
                $ano1 = $arrEvA['ano'];
                $ano2 = $arrEvA['ano2'];
                $txtDataEvento = Uteis::montaData($dia1, $dia2, $mes1, $mes2, $ano1, $ano2);
                $txtTagHtml    = isset($arrTagEvento[0]['tag']) && $arrTagEvento[0]['tag'] != '' ? '<span class="tag">'.$arrTagEvento[0]['tag'].'</span>' : '';
                $eventos_abertos_html .= 
                '<div class="slide">
                    <a href="'.$urlMotor.'" target="_blank">
                        <div class="card-evento">
                            <div class="card-img">
                                <div class="calendar">
                                    <div class="calendar-body">
                                       <span>
                                            <i class="far fa-calendar-alt"></i>
                                            ' . $txtDataEvento . '
                                        </span>    
                                    </div>
                                </div>
                                ' . $txtTagHtml . '
                                <img src="'.$fotoEvento.'" alt="img-evento">
                            </div>
                            <div class="card-body">
                                <div class="dc mb-5">
                                    <a href="'.$urlMotor.'" target="_blank" class="text-black"><h5 class="card-title mb-4">'.$arrEvA['nomeGrupo'].'</h5></a>
                                    <ul>
                                        <li> 
                                            <a class="link-local text-black" href="#">
                                                <i class="bx bx-map"></i> ' . $nomeLocal . '</li>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="footer-card d-flex align-items-center justify-content-between">
                                    <div class="valor-e d-flex flex-column">
                                        <small>Apartir de </small>
                                        <small>R$ <span class="preco-evento">'.$menor_valor.'</span></small>
                                    </div>
                                    <div class="comprar">
                                       <a href="'.$urlMotor.'" target="_blank" class="btn btn-comprar"> <i class="fab fa-opencart"></i> Comprar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            }
        }*/

        if(intval($totalEventos) > $limite_por_pagina){
            $pagination .= Uteis::pagination($filters_pag, (int)$pg, 'busca', (int)$totalEventos, (int)$limite_por_pagina, (int)$totalPages, $this->url);
        }

        $cidades_select   = '';
        $categoria_select = '';
        $categorias_html  = '';
        $arrCategorias    = $categorias->getCategorias($objSqlAdmin);
        if(isset($arrCategorias[0]['id']) && $arrCategorias[0]['id'] != ''){
            foreach($arrCategorias as $cat){
                $categoria_id  = (int)$cat['id'];
                if(intval($categoria_id) > 0){
                    $categoria_select .= '<option value="'.$categoria_id.'">'.strtoupper($cat['nome']).'</option>';
                }
            }
        }

        $local_select   = '';
        $locais_html    = '';
        $local_sem_foto = $this->url . 'public/img/sem_foto.jpg';
        $arrLocais      = (int)$confDominio[0]['cliente_admin'] == 1 ? $locais->getLocaisPrimeTicket($objSqlAdmin) : $locais->getLocaisDominioTerceiro($objSqlAdmin, $confDominio[0]['cliente_admin']);
        if(isset($arrLocais[0]['id'])){
            foreach($arrLocais as $arrL){
                $cliente_id  = (int)$arrL['id'];
                $tituloLocal = $arrL['tipo'] == 'F' ? $arrL['nome'] : ( $arrL['fantasia'] != '' ? $arrL['fantasia'] : $arrL['razao'] );
                if(intval($cliente_id) > 0){
                    $local_select .= '<option value="'.$cliente_id.'">'.strtoupper($tituloLocal).'</option>';
                }
            }
        }
       
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'] . ' - Busca',
                'version' => date('YmdHis'),
                'pagina' => 'contato',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'categorias_html' => $categorias_html,
                'eventos_abertos_html' => $eventos_abertos_html,
                'categoria_select' => $categoria_select,
                'local_select' => $local_select,
                'cidades_select' => $cidades_select,
                'pagination' => $pagination,
                'eventos_busca_html' => $eventos_busca_html
            )
        );

        return $this->twig->render('busca.html', $values);
    }

}