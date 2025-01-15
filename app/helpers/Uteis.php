<?php
class Uteis
{
    public static function formataValorBR($valor){
        return number_format($valor,2,',','.');
    }

    public static function formataValorDB($valor){
        return number_format($valor,2,'.','');
    }

    public static function formataDecode($str){
        return utf8_decode($str);
    }

    public static function soNumeros($strString){
        return preg_replace('/[^0-9]/', '', $strString);
    }

    public static function formatDate($strDate){
        $strDate = DateTime::createFromFormat('Y-m-d', $strDate);
        return $strDate->format('d/m/Y');
    }

    public static function pagination($get, $pg, $pagina, $totalRegistros, $limite_por_pagina, $totalPages, $url){
        $gets = isset($get) && $get != '' ? http_build_query($get) : '';
        $pagination = '';

        if(intval($totalRegistros) > $limite_por_pagina){
            $inicio_pg = ((($pg - $limite_por_pagina) > 1) ? $pg - $limite_por_pagina : 1);
            $fim = ((($pg+$limite_por_pagina) < $totalPages) ? $pg+$limite_por_pagina : $totalPages);
            $adjacentes = 2;
            $ultima_pag = $totalPages;

            $gets = $gets != '' ? '&' . $gets : '';
            $pagination .= '<ul class="pagination" role="menubar" aria-label="Pagination">';
            if($pg > 1){
                $pg_anterior = $pg - 1;
                $url_extrato_anterior = $url . $pagina . '?pg=' . $pg_anterior . $gets;
                $pagination .= '<li class="anterior"><a href="'.$url_extrato_anterior.'">Anterior</a></li>';
            }

            if ($ultima_pag <= 5){
                for ($i=1; $i< $ultima_pag+1; $i++){
                    $url_extrato = $url . $pagina . '?pg=' . $i . $gets;
                    $pagination .= $pg == $i ? '<li class="current"><a href="">'.$i.'</a></li>' : '<li><a href="'.$url_extrato.'">'.$i.'</a></li>';
                }
            } 

            if ($ultima_pag > 5){
	            $penultima = $ultima_pag - 1;

                if ($pg < 1 + (2 * $adjacentes)){
                    for ($i=1; $i< 2 + (2 * $adjacentes); $i++){
                        $url_extrato = $url . $pagina . '?pg=' . $i . $gets;
                        $pagination .= $pg == $i ? '<li class="current"><a href="">'.$i.'</a></li>' : '<li><a href="'.$url_extrato.'">'.$i.'</a></li>';
                    }
                    $pagination .= '<li><a>...</a></li>';
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=' . $penultima . $gets.'">'.$penultima.'</a></li>';
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=' . $ultima_pag . $gets.'">'.$ultima_pag.'</a></li>';
                } elseif($pg > (2 * $adjacentes) && $pg < $ultima_pag - 3){
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=1' . $gets.'">1</a></li>';				
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=2' . $gets.'">2</a></li>';	
                    $pagination .= '<li><a>...</a></li>';	
                    for ($i = $pg-$adjacentes; $i<= $pg + $adjacentes; $i++){
                        $url_extrato = $url . $pagina . '?pg=' . $i . $gets;
                        $pagination .= $pg == $i ? '<li class="current"><a href="">'.$i.'</a></li>' : '<li><a href="'.$url_extrato.'">'.$i.'</a></li>';
                    }
                    $pagination .= '<li><a>...</a></li>';
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=' . $penultima . $gets.'">'.$penultima.'</a></li>';
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=' . $ultima_pag . $gets.'">'.$ultima_pag.'</a></li>';
                } else {
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=1' . $gets.'">1</a></li>';				
                    $pagination .= '<li><a href="'.$url . $pagina . '?pg=2' . $gets.'">2</a></li>';
                    $pagination .= '<li><a>...</a></li>';
                    for ($i = $ultima_pag - (4 + (2 * $adjacentes)); $i <= $ultima_pag; $i++){
                        $url_extrato = $url . $pagina . '?pg=' . $i . $gets;
                        $pagination .= $pg == $i ? '<li class="current"><a href="">'.$i.'</a></li>' : '<li><a href="'.$url_extrato.'">'.$i.'</a></li>';
                    }
                }
            }

            if($pg != $totalPages){
                $pg_proxima = $pg + 1;
                $url_extrato_proximo = $url . $pagina . '?pg=' . $pg_proxima . $gets;
                $pagination .= '<li class="proxima"><a href="'.$url_extrato_proximo.'">Próxima</a></li>';
            }
            $pagination .= '</ul>';
        }

        return $pagination;
    }

    public static function gerarTokenUsuario($idUser){    
        $intNumCaractere = 50;
        $strDicionario = '0123456789abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ';
        $strCodigo = '';
        $intLimite = intval(strlen($strDicionario)) - 1;
        $strCodigo .= date('YmdHis').$idUser.$strDicionario[rand(0, $intLimite)];
        return $strCodigo;
    }

    public static function utf8($str){
        return utf8_encode($str);
    }

    public static function utf8_permissao($str){
        return $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost:8080' ? utf8_encode($str) : $str;
    }

    public static function removeValor($taxa){
        $taxa = trim($taxa);
        $taxa = str_replace('R$', '', $taxa);
        $taxa = str_replace(',','.',str_replace('.','',$taxa));
        return $taxa;
    }

    public static function getMesExtenso($mes){
        $arrayMes = array(
            '1'  => 'Janeiro',
            '2'  => 'Fevereiro',
            '3'  => 'Março',
            '4'  => 'Abril',
            '5'  => 'Maio',
            '6'  => 'Junho',
            '7'  => 'Julho',
            '8'  => 'Agosto',
            '9'  => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        );
        return $arrayMes[$mes];
    }

    public static function getDiaExtenso($data){
        $dia_semana = date('w', strtotime($data));
        $arrayDia = array(
            '0'  => 'DOMINGO',
            '1'  => 'SEGUNDA FEIRA',
            '2'  => 'TERÇA FEIRA',
            '3'  => 'QUARTA FEIRA',
            '4'  => 'QUINTA FEIRA',
            '5'  => 'SEXTA FEIRA',
            '6'  => 'SÁBADO'
        );
        return $arrayDia[$dia_semana];
    }

}