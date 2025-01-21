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
        $banner_evento = $this->model('BannerEvento');
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
        $resultLocalCliente = $local->getLocalDominio($objSqlAdmin, $confDominio[0]['cliente_admin']);
        $objSqlClienteLocal = new sql($resultLocalCliente[0]['bdLogin'], $resultLocalCliente[0]['bdBase'], $resultLocalCliente[0]['bdLocal'], $resultLocalCliente[0]['bdSenha']);
        $resultLocal = $confDominio[0]['cliente_admin'] == 1 ? $local->getLocalAdmin($objSqlAdmin, $idLocal) : $local->getLocalTerceiro($objSqlAdmin, $idLocal, $objSqlClienteLocal, $confDominio[0]['cliente_admin']);
        
        $ingresso_id = trim($resultLocal[0]['ingresso_id']);
        $loja_motor  = trim($resultLocal[0]['motor']);

        $URL_MOTOR_LOCAL = $loja_motor == 'motor_eventos' ? URL_MOTOR_EVENTO : URL_MOTOR_INGRESSO;
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $URL_MOTOR_LOCAL = $loja_motor == 'motor_eventos' ? URL_MOTOR_EVENTO_SQUARE : URL_MOTOR_INGRESSO_SQUARE;
        }
        $ticket_id       = $loja_motor == 'motor_eventos' ? COMPANY_ID : TICKET_ID;

        $objSqlCliente = new sql($resultLocal[0]['bdLogin'], $resultLocal[0]['bdBase'], $resultLocal[0]['bdLocal'], $resultLocal[0]['bdSenha']);
        $nomeLocal = $resultLocal[0]['tipo'] == 'F' ? $resultLocal[0]['nome'] : ( $resultLocal[0]['fantasia'] != '' ? $resultLocal[0]['fantasia'] : $resultLocal[0]['razao'] );
        $cidade = $resultLocal[0]['cidade'];
        $cidade_g = $resultLocal[0]['cidade_g'];
        $estado = $resultLocal[0]['estado'];
        $estado_g = $resultLocal[0]['estado_g'];
        $idFornecedor = (int)$resultLocal[0]['fornecedor'];

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

        $arrBannerEvento = $banner_evento->getBannerEvento($objSqlCliente, $idEvento);
        $bannerEvento = isset($arrBannerEvento[0]['arquivoBanner']) && $arrBannerEvento[0]['arquivoBanner'] != '' ? URL_S3 . '/'. (int)$idLocal . '/' . trim($arrBannerEvento[0]['arquivoBanner']) : 'https://images.unsplash.com/photo-1468234847176-28606331216a?q=80&w=2677&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';

        $galeria_imagens = $eventos->getFotosEventoFotografos($objSqlCliente, $idEvento);
        $urlFotoS3      = URL_S3 . '/' . $idLocal . '/';

        $galeria_imagens_html = "";
        $version = date('YmdHis');
        if(isset($galeria_imagens[0])){
            $galeria_imagens_html .=
                    '<section class="tituloGaleria">
                        <div class="container">   
                            <div class="borda-titulo">
                                <div class="separator"></div>
                                <div class="titulo">
                                    <h2>Fotos</h2>
                                    <p class="mb-4">Galeria de Fotos</p>
                                </div>
                            </div>
                        </div>
                    </section>';  
            $i = 0;
            foreach($galeria_imagens as $arrFot){
                $i++;
                $idFotografo = (int)$arrFot['usuario'];
                $tituloGaleria = $i == 1 ? "tituloGaleria" : "tituloGaleria2";
                $galeria_imagens_html .=
                    '<section class="'.$tituloGaleria.'">
                        <div class="container">   
                            <span><strong>Fotos tiradas por:</strong> '.$arrFot['fotografo'].'</span>
                        </div>
                        <div class="galeria" id="galeria">';
                        $galeria_imagens_fotografo = $eventos->getFotosEventoFotografoId($objSqlCliente, $idEvento, $idFotografo);
                        foreach($galeria_imagens_fotografo as $arrFotosEv){
                            $galeria_imagens_html .= 
                            '<a href="'.$urlFotoS3.trim($arrFotosEv['imagem_webp']).'?v='.$version.'" title="'.$nomeEvento.'">
                                <img src="'.$urlFotoS3.trim($arrFotosEv['imagem_webp']).'?v='.$version.'" alt="'.$nomeEvento.'">
                            </a>';
                        }
                    $galeria_imagens_html .= 
                        '</div>
                    </section>';
            }
        }

        $eventos_abertos = '';
        $arrEventosAbertos = $eventos->getEventosAbertos($objSqlCliente);
        if(isset($arrEventosAbertos[0]['id'])){
            foreach($arrEventosAbertos as $arrEvA){
                $idEvento    = (int)$arrEvA['id'];
                $arrTagEvento = $eventos->getTagEvento($objSqlAdmin, $idEvento, $idFornecedor);
                $fotoEvento  = $arrEvA['imagem_webp'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem_webp'] : ( $arrEvA['imagem'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem'] : URL_IMAGE_SEMFOTO );
                $mes_evento  = (int)$arrEvA['mes'];
                $mes_extenso = Uteis::getMesExtenso($mes_evento);
                $dia_extenso = Uteis::getDiaExtenso($arrEvA['data']);
                $arrValorEv  = $eventos->getMenorValorEvento($objSqlCliente, $idEvento);
                $menor_valor = Uteis::formataValorBR($arrValorEv[0]['valorVarejo']);
                $dia1 = (int)$arrEvA['dia'] < 10 ? '0'.$arrEvA['dia'] : $arrEvA['dia'];
                $dia2 = (int)$arrEvA['dia2'] < 10 ? '0'.$arrEvA['dia2'] : $arrEvA['dia2'];
                $mes1 = $arrEvA['mes'];
                $mes2 = $arrEvA['mes2'];
                $ano1 = $arrEvA['ano'];
                $ano2 = $arrEvA['ano2'];
                $txtTagHtml    = isset($arrTagEvento[0]['tag']) && $arrTagEvento[0]['tag'] != '' ? '<span class="tag">'.$arrTagEvento[0]['tag'].'</span>' : '';
                $txtDataEvento = Uteis::montaData($dia1, $dia2, $mes1, $mes2, $ano1, $ano2);
                $urlMotor    = $URL_MOTOR_LOCAL . 'index.php?'.$ticket_id.'=' . $ingresso_id . '&acao=detalhes-produto&grupo=' . (int)$idEvento;
                $eventos_abertos .= 
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
                                '. $txtTagHtml . '
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
        }

        $eventos_similares = '';
        /*$arrEventosAbertosOutros = $eventos->getEventosAbertosOutros($objSqlCliente, $confDominio[0]['cliente_admin']);
        if(isset($arrEventosAbertosOutros[0]['id'])){
            $eventos_similares .= 
            '<section style="background-color: #eee;">
                <div class="container">
                    <div class="borda-titulo">
                        <div class="separator "></div>
                        <div class="titulo ">
                            <h2>Veja Também</h2>
                            <p class="mb-4">Eventos similiares em outras casas</p>
                        </div>
                    </div>
                    <div class="carousel-eventos slider">';      
                    foreach($arrEventosAbertosOutros as $arrEvA){
                        $fotoEvento  = $arrEvA['imagem_webp'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem_webp'] : ( $arrEvA['imagem'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEvA['imagem'] : URL_IMAGE_SEMFOTO );
                        $mes_evento  = (int)$arrEvA['mes'];
                        $mes_extenso = Uteis::getMesExtenso($mes_evento);
                        $dia_extenso = Uteis::getDiaExtenso($arrEvA['data']);
                        $idEvento    = (int)$arrEvA['id'];
                        $arrValorEv  = $eventos->getMenorValorEvento($objSqlCliente, $idEvento);
                        $menor_valor = Uteis::formataValorBR($arrValorEv[0]['valorVarejo']);
                        $categoria   = 1;
                        $urlMotor    = $URL_MOTOR_LOCAL . 'index.php?'.$ticket_id.'=' . $ingresso_id . '&acao=detalhes-produto&grupo=' . (int)$idEvento . '&categoria=' . (int)$categoria;
                        $eventos_similares .= 
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
            $eventos_similares .= '
                    </div>
                </div>
            </section>';    
        }*/

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
                'titleLocal' => Uteis::urltitle($nomeLocal),
                'bannerEvento' => $bannerEvento,
                'galeria_imagens' => $galeria_imagens_html,
                'urlFotoS3' => $urlFotoS3,
                'eventos_abertos' => $eventos_abertos,
                'eventos_similares_html' => $eventos_similares
            )
        );

        return $this->twig->render('detalhes-evento-expirado.html', $values);
    }

    public function eventos_abertos()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  
        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $categorias = $this->model('Categorias'); 
        $locais = $this->model('Local');
        $eventos = $this->model('Eventos');
        $fornecedores = $this->model('Fornecedores');
        $objSqlAdmin = new sql($GLOBALS['login_admin'], $GLOBALS['base_admin'], $GLOBALS['local_admin'], $GLOBALS['senha_admin']);
        $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL);
        if(!isset($confDominio[0]['id'])){ //se não existir o dominio no cadastro vai pegar os dados default primeticket
            $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL_DEFAULT);
        }
        $faviconHtml = '';
        $logoLojaHtml = '';
        if(isset($confDominio[0]['favicon']) && $confDominio[0]['favicon'] != ''){
            $favicon = URL_S3_FAVICON . $confDominio[0]['favicon'];
            $faviconHtml = '<link rel="icon" type="image/png" href="'.$favicon.'">';
        }
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $urlSistema = 'https://sistema.squareticket.com.br';
        }
        $logoLojaHtml = URL_S3_LOGO . $confDominio[0]['imagem_webp'];

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
                $fotoCategoria = isset($cat['arquivo']) && $cat['arquivo'] != '' ? URL_S3_LOGO . $cat['arquivo'] : $this->url . 'public/img/sem_foto.jpg';
                $urlCategoria  = $this->url . 'busca/?categoria=' . $categoria_id;
                $categorias_html .= 
                    '<div class="slide">
                        <a href="'.$urlCategoria.'" target="_blank" rel="noopener noreferrer">
                            <div class="categoria-item">
                                <div class="img-categoria">
                                    <img src="'.$fotoCategoria.'" alt="">
                                </div>
                                <div class="titulo-categoria">
                                    '.strtoupper($cat['nome']).'
                                </div>
                            </div>
                        </a>
                    </div>';
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
                $fotoLocalOriginal = URL_S3 . '/'. $cliente_id . '/logoPortal.jpg';
                $fotoLocal = !file_exists($fotoLocalOriginal) ? URL_S3 . '/'. $cliente_id . '/logoPortal.jpg?v=' . date('YmdHis') : $local_sem_foto;
                $urlLocal    = $this->url . 'detalhes-local/'.$cliente_id.'/'.Uteis::urltitle($tituloLocal);
            }
        }

        $eventos_abertos_html = '';
        $arrEventosAbertos = $eventos->getEventosAbertosHome($objSqlAdmin, $confDominio[0]['cliente_admin']);
        if(isset($arrEventosAbertos[0]['id'])){
            foreach($arrEventosAbertos as $arrEvA){
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
        }

        $ultimos_eventos_html = '';
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'],
                'version' => date('YmdHis'),
                'pagina' => 'home',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'categorias_html' => $categorias_html,
                'locais_html' => $locais_html,
                'eventos_aconteceram_html' => $ultimos_eventos_html,
                'eventos_abertos_html' => $eventos_abertos_html,
                'categoria_select' => $categoria_select,
                'local_select' => $local_select,
                'cidades_select' => $cidades_select
            )
        );

        return $this->twig->render('eventos_abertos.html', $values);
    }

    public function eventos_encerrados()
    {
        if (!isset($_SESSION)) {
            session_start();
        }  
        $urlSistema = 'https://sistema.nucleodeturismo.com.br';
        $admin = $this->model('Admin'); 
        $categorias = $this->model('Categorias'); 
        $locais = $this->model('Local');
        $eventos = $this->model('Eventos');
        $fornecedores = $this->model('Fornecedores');
        $objSqlAdmin = new sql($GLOBALS['login_admin'], $GLOBALS['base_admin'], $GLOBALS['local_admin'], $GLOBALS['senha_admin']);
        $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL);
        if(!isset($confDominio[0]['id'])){ //se não existir o dominio no cadastro vai pegar os dados default primeticket
            $confDominio = $admin->getDominioPrimeTicket($objSqlAdmin, DOMINIO_URL_DEFAULT);
        }
        $faviconHtml = '';
        $logoLojaHtml = '';
        if(isset($confDominio[0]['favicon']) && $confDominio[0]['favicon'] != ''){
            $favicon = URL_S3_FAVICON . $confDominio[0]['favicon'];
            $faviconHtml = '<link rel="icon" type="image/png" href="'.$favicon.'">';
        }
        if($confDominio[0]['dominio'] == 'squareticket.com.br'){
            $urlSistema = 'https://sistema.squareticket.com.br';
        }
        $logoLojaHtml = URL_S3_LOGO . $confDominio[0]['imagem_webp'];

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
                $fotoCategoria = isset($cat['arquivo']) && $cat['arquivo'] != '' ? URL_S3_LOGO . $cat['arquivo'] : $this->url . 'public/img/sem_foto.jpg';
                $urlCategoria  = $this->url . 'busca/?categoria=' . $categoria_id;
                $categorias_html .= 
                    '<div class="slide">
                        <a href="'.$urlCategoria.'" target="_blank" rel="noopener noreferrer">
                            <div class="categoria-item">
                                <div class="img-categoria">
                                    <img src="'.$fotoCategoria.'" alt="">
                                </div>
                                <div class="titulo-categoria">
                                    '.strtoupper($cat['nome']).'
                                </div>
                            </div>
                        </a>
                    </div>';
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
                $fotoLocalOriginal = URL_S3 . '/'. $cliente_id . '/logoPortal.jpg';
                $fotoLocal = !file_exists($fotoLocalOriginal) ? URL_S3 . '/'. $cliente_id . '/logoPortal.jpg?v=' . date('YmdHis') : $local_sem_foto;
                $urlLocal    = $this->url . 'detalhes-local/'.$cliente_id.'/'.Uteis::urltitle($tituloLocal);
            }
        }

        $ultimos_eventos_html = '';
        $arrUltimosEventos = '';
        $arrUltimosEventos = $eventos->getUltimosEventosEncerradosRandHome($objSqlAdmin, $confDominio[0]['cliente_admin']);
        if(isset($arrUltimosEventos[0]['id']) && intval($arrUltimosEventos[0]['id']) > 0){
            foreach($arrUltimosEventos as $arrEv){
                $nomeLocal    = $arrEv['nomeEmp'];
                $idLocal      = (int)$arrEv['cliente'];
                $idFornecedor = (int)$arrEv['fornecedor'];
                $idEvento     = (int)$arrEv['id'];
                $arrTagEvento = $eventos->getTagEvento($objSqlAdmin, $idEvento, $idFornecedor);
                $fotoEvento   = $arrEv['imagem_webp'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEv['imagem_webp'] : ( $arrEv['imagem'] != '' ? URL_S3 . '/' . $idLocal . '/' . $arrEv['imagem'] : URL_IMAGE_SEMFOTO );
                $mes_evento   = (int)$arrEv['mes'];
                $mes_extenso  = Uteis::getMesExtenso($mes_evento);
                $dia_extenso  = Uteis::getDiaExtenso($arrEv['data']);
                $tituloEvento = Uteis::urltitle($arrEv['nomeGrupo']);
                $detalhes_evento = $this->url . 'detalhes-evento/'.$idLocal.'/'.$idEvento.'/'.$tituloEvento;
                $dia1 = (int)$arrEv['dia'] < 10 ? '0'.$arrEv['dia'] : $arrEv['dia'];
                $dia2 = (int)$arrEv['dia2'] < 10 ? '0'.$arrEv['dia2'] : $arrEv['dia2'];
                $mes1 = $arrEv['mes'];
                $mes2 = $arrEv['mes2'];
                $ano1 = $arrEv['ano'];
                $ano2 = $arrEv['ano2'];
                $txtDataEvento = Uteis::montaData($dia1, $dia2, $mes1, $mes2, $ano1, $ano2);
                $txtTagHtml    = isset($arrTagEvento[0]['tag']) && $arrTagEvento[0]['tag'] != '' ? '<span class="tag">'.$arrTagEvento[0]['tag'].'</span>' : '';
                $ultimos_eventos_html .= 
                        '<div class="col-lg-3">
                            <a href="'.$detalhes_evento.'">
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
                                        <h5 class="card-title mb-4 text-black">' . $arrEv['nomeGrupo'] . '</h5>
                                        <p class="card-text">
                                        
                                        </p>
                                        <ul>
                                            <li>
                                                <a class="link-local text-black" href="#">
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
        
        $eventos_abertos_html = '';
        $values = array(
            'estrutura' => array(
                'url'    => $this->url,
                'title'  => $confDominio[0]['titulo'],
                'version' => date('YmdHis'),
                'pagina' => 'home',
                'favicon' => $faviconHtml,
                'nomeEmpresa' => $confDominio[0]['titulo'],
                'urlSistema' => $urlSistema,
                'logoLoja' => $logoLojaHtml,
                'categorias_html' => $categorias_html,
                'locais_html' => $locais_html,
                'eventos_aconteceram_html' => $ultimos_eventos_html,
                'eventos_abertos_html' => $eventos_abertos_html,
                'categoria_select' => $categoria_select,
                'local_select' => $local_select,
                'cidades_select' => $cidades_select
            )
        );

        return $this->twig->render('eventos_encerrados.html', $values);
    }

}