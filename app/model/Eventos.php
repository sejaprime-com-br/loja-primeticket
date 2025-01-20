<?php
class Eventos {

    public function getUltimosEventosEncerradosRand($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'dia2', 
        month(pg.data_fim) AS 'mes2', year(pg.data_ini) AS 'ano2'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim < CURRENT_DATE()
        GROUP BY pg.data_ini, pg.id
        ORDER BY RAND() LIMIT 4");
        return $arrDados;
    }

    public function getEventosAbertos($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'dia2', 
        month(pg.data_fim) AS 'mes2', year(pg.data_ini) AS 'ano2'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim > CURRENT_DATE()
        GROUP BY pg.data_ini, pg.id");
        return $arrDados;
    }

    public function getEventosAbertosOutros($objSqlCliente, $cliente_admin){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'dia2', 
        month(pg.data_fim) AS 'mes2', year(pg.data_ini) AS 'ano2'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim > CURRENT_DATE()
        GROUP BY pg.data_ini, pg.id");
        return $arrDados;
    }

    public function getMenorValorEvento($objSqlCliente, $idGrupo){
        $arrDados = $objSqlCliente->executaQuery("SELECT prod2.valorVarejo FROM produto prod2 
        WHERE prod2.produto_grupo = " . $idGrupo . " AND prod2.ecommerce = '1' AND prod2.ativo = '1' AND prod2.valorVarejo > 0 
        ORDER BY prod2.valorVarejo ASC LIMIT 1");
        return $arrDados;
    }

    public function getEvento($objSqlCliente, $idGrupo){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'diaInicial', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'diaFinal', 
        month(pg.data_fim) AS 'mes_final', year(pg.data_fim) AS 'ano_final'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        WHERE pg.id = ".$idGrupo." AND pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim < CURRENT_DATE()
        GROUP BY pg.id");
        return $arrDados;
    }

    public function getEventosFotografo($objSqlCliente, $idFotografo){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', pg.data_fim AS 'data_final', day(pg.data_ini) AS 'diaInicial', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'diaFinal', 
        month(pg.data_fim) AS 'mes_final', year(pg.data_fim) AS 'ano_final'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        INNER JOIN produto_grupo_fotografo pgf ON (pgf.produto_grupo = pg.id)
        WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim < CURRENT_DATE()
        AND pgf.usuario = ".(int)$idFotografo."
        GROUP BY pg.id");
        return $arrDados;
    }

    public function getEventoFotografo($objSqlCliente, $idGrupo, $idFotografo){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'diaInicial', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'diaFinal', 
        month(pg.data_fim) AS 'mes_final', year(pg.data_fim) AS 'ano_final'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        INNER JOIN produto_grupo_fotografo pgf ON (pgf.produto_grupo = pg.id)
        WHERE pg.id = ".(int)$idGrupo." AND pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim < CURRENT_DATE()
        AND pgf.usuario = ".(int)$idFotografo."
        GROUP BY pg.id");
        return $arrDados;
    }

    public function getFotosEventoFotografo($objSqlCliente, $idGrupo, $idFotografo){
        $arrDados = $objSqlCliente->executaQuery("SELECT * FROM produto_grupo_fotografo_galeria pgfg 
        WHERE pgfg.usuario = " . (int)$idFotografo . " AND pgfg.produto_grupo = " . (int)$idGrupo);
        return $arrDados;
    }

    public function getFotosEventoFotografos($objSqlCliente, $idGrupo){
        $arrDados = $objSqlCliente->executaQuery("SELECT pgfg.*, us.nome AS 'fotografo'
        FROM produto_grupo_fotografo_galeria pgfg INNER JOIN usuario us ON (us.id = pgfg.usuario)
        WHERE pgfg.produto_grupo = " . (int)$idGrupo . "
        GROUP BY pgfg.usuario");
        return $arrDados;
    }

    public function getFotosEventoFotografoId($objSqlCliente, $idGrupo, $idFotografo){
        $arrDados = $objSqlCliente->executaQuery("SELECT pgfg.*, us.nome AS 'fotografo'
        FROM produto_grupo_fotografo_galeria pgfg INNER JOIN usuario us ON (us.id = pgfg.usuario)
        WHERE pgfg.usuario = " . (int)$idFotografo . " AND pgfg.produto_grupo = " . (int)$idGrupo);
        return $arrDados;
    }

    public function getTagEvento($objSqlAdmin, $idEvento, $idFornecedor){
        $arrDados = $objSqlAdmin->executaQuery("SELECT fipg.id, fg.nome AS 'tag'
        FROM fornecedor_interesse_produto_grupo fipg INNER JOIN fornecedor_grupo fg ON (fg.id = fipg.fornecedor_grupo)
        LEFT JOIN fornecedor_subgrupo fsg ON (fsg.id = fipg.fornecedor_subgrupo)
        WHERE fipg.fornecedor = " . (int)$idFornecedor . " AND fipg.produto_grupo = " . (int)$idEvento . "
        LIMIT 1");
        return $arrDados;
    }

    public function getUltimosEventosEncerradosRandHome($objSqlAdmin, $cliente_admin){
        $arrDadosFinal = array();
        if((int)$cliente_admin == 1){
            $arrDadosAdmin = $objSqlAdmin->executaQuery("SELECT ec.*, cl.tipo, cl.nome AS 'nomeEmp', cl.fantasia, cl.razao, cl.fornecedor, cl.bdLogin, cl.bdBase, cl.bdLocal, cl.bdSenha
            FROM eventos_encerrados ec INNER JOIN cliente cl ON (cl.id = ec.cliente)
            WHERE cl.bdLogin != '' AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdSenha != ''
            ORDER BY ec.data_cadastro DESC LIMIT 4");
        
            if(isset($arrDadosAdmin[0]['id']) && intval($arrDadosAdmin[0]['id']) > 0){
                foreach($arrDadosAdmin as $arr){
                    $objSqlCliente = new sql($arr['bdLogin'], $arr['bdBase'], $arr['bdLocal'], $arr['bdSenha']);
                    $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
                    pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
                    month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'dia2', 
                    month(pg.data_fim) AS 'mes2', year(pg.data_ini) AS 'ano2'
                    FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
                    WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.id = " . (int)$arr['produto_grupo']);
                    if(isset($arrDados[0]) && (int)$arrDados[0]['id'] > 0){
                        $arrDados[0]['nomeEmp'] = $arr['tipo'] == 'F' ? $arr['nomeEmp'] : ( $arr['fantasia'] != '' ? $arr['fantasia'] : $arr['razao'] );
                        $arrDados[0]['fornecedor'] = $arr['fornecedor'];
                        $arrDados[0]['cliente'] = $arr['cliente'];
                        $arrDadosFinal[] = $arrDados[0];
                    }
                }
            }
        } else {
            $strFornecedores = '';
            $arrDadosAdminCliente = $objSqlAdmin->executaQuery("SELECT cl.id, cl.tipo, cl.nome AS 'nomeEmp', cl.fantasia, cl.razao, cl.fornecedor, cl.bdLogin, cl.bdBase, cl.bdLocal, cl.bdSenha
            FROM cliente cl WHERE cl.bdLogin != '' AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdSenha != '' AND cl.id = " . (int)$cliente_admin);
            $objSqlCliente = new sql($arrDadosAdminCliente[0]['bdLogin'], $arrDadosAdminCliente[0]['bdBase'], $arrDadosAdminCliente[0]['bdLocal'], $arrDadosAdminCliente[0]['bdSenha']);
            $sqlBuscaT = "SELECT fs.* FROM fornecedor_servico fs 
            WHERE fs.tipo_do_fornecedor IN('L') AND fs.tem_loja IN('S') AND fs.fornecedorAdmin > 0";
            $arrDadosTerceiro = $objSqlCliente->executaQuery($sqlBuscaT);
            $intCont = 0;
            $strFornecedores .= (int)$arrDadosAdminCliente[0]['fornecedor'];
            foreach($arrDadosTerceiro as $arrForn){
                $intCont++;
                $strFornecedores .= "," . (int)$arrForn['fornecedorAdmin'];
            }

            $strEventosAbertos = "SELECT ec.*, cl.tipo, cl.nome AS 'nomeEmp', cl.fantasia, cl.razao, cl.fornecedor, cl.bdLogin, cl.bdBase, cl.bdLocal, cl.bdSenha
            FROM eventos_encerrados ec INNER JOIN cliente cl ON (cl.id = ec.cliente)
            WHERE cl.bdLogin != '' AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdSenha != '' AND cl.fornecedor IN(".$strFornecedores.")
            ORDER BY ec.data_cadastro DESC LIMIT 4";
            $arrDadosAdmin = $objSqlAdmin->executaQuery($strEventosAbertos);

            if(isset($arrDadosAdmin[0]['id']) && intval($arrDadosAdmin[0]['id']) > 0){
                foreach($arrDadosAdmin as $arr){
                    $objSqlCliente = new sql($arr['bdLogin'], $arr['bdBase'], $arr['bdLocal'], $arr['bdSenha']);
                    $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
                    pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
                    month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano', day(pg.data_fim) AS 'dia2', 
                    month(pg.data_fim) AS 'mes2', year(pg.data_ini) AS 'ano2'
                    FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
                    WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.id = " . (int)$arr['produto_grupo']);
                    if(isset($arrDados[0]) && (int)$arrDados[0]['id'] > 0){
                        $arrDados[0]['nomeEmp'] = $arr['tipo'] == 'F' ? $arr['nomeEmp'] : ( $arr['fantasia'] != '' ? $arr['fantasia'] : $arr['razao'] );
                        $arrDados[0]['fornecedor'] = $arr['fornecedor'];
                        $arrDados[0]['cliente'] = $arr['cliente'];
                        $arrDadosFinal[] = $arrDados[0];
                    }
                }
            }
        }

        return $arrDadosFinal;
    }

}