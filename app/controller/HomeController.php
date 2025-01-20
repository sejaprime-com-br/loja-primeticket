<?php
class HomeController extends Controller
{
    public function home()
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

        $categorias_html = '';
        $arrCategorias   = $categorias->getCategorias($objSqlAdmin);
        if(isset($arrCategorias[0]['id']) && $arrCategorias[0]['id'] != ''){
            foreach($arrCategorias as $cat){
                $categoria_id  = (int)$cat['id'];
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

        $locais_html    = '';
        $local_sem_foto = $this->url . 'public/img/sem_foto.jpg';
        $arrLocais      = (int)$confDominio[0]['cliente_admin'] == 1 ? $locais->getLocaisPrimeTicket($objSqlAdmin) : $locais->getLocaisDominioTerceiro($objSqlAdmin, $confDominio[0]['cliente_admin']);
        if(isset($arrLocais[0]['id'])){
            foreach($arrLocais as $arrL){
                $cliente_id  = (int)$arrL['id'];
                $fotoLocal   = URL_S3 . '/'. $cliente_id . '/logoPortal.jpg?v=' . date('YmdHis');
                $tituloLocal = $arrL['tipo'] == 'F' ? $arrL['nome'] : ( $arrL['fantasia'] != '' ? $arrL['fantasia'] : $arrL['razao'] );
                $urlLocal    = $this->url . 'detalhes-local/'.$cliente_id.'/'.Uteis::urltitle($tituloLocal);
                $locais_html .= '
                <div class="slide">
                    <a href="'.$urlLocal.'"><img src="'.$fotoLocal.'"></a>
                </div>';
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
                'eventos_aconteceram_html' => $ultimos_eventos_html
            )
        );

        return $this->twig->render('home.html', $values);
    }
}