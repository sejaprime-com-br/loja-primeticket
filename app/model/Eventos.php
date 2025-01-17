<?php
class Eventos {

    public function getUltimosEventosEncerradosRand($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim < CURRENT_DATE()
        GROUP BY pg.data_ini, pg.id
        ORDER BY RAND() LIMIT 4");
        return $arrDados;
    }

    public function getEventosAbertos($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano'
        FROM produto_grupo pg INNER JOIN produto prod ON (prod.produto_grupo = pg.id)
        WHERE pg.ativo = 'S' AND pg.tipo IN('P') AND pg.data_fim != '' AND pg.data_fim > CURRENT_DATE()
        GROUP BY pg.data_ini, pg.id");
        return $arrDados;
    }

    public function getEventosAbertosOutros($objSqlCliente, $cliente_admin){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.id, pg.nome AS 'nomeGrupo',
        pg.imagem, pg.imagem_webp, pg.data_ini AS 'data', day(pg.data_ini) AS 'dia', 
        month(pg.data_ini) AS 'mes', year(pg.data_ini) AS 'ano'
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

}