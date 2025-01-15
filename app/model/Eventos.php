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

    public function getMenorValorEvento($objSqlCliente, $idGrupo){
        $arrDados = $objSqlCliente->executaQuery("SELECT prod2.valorVarejo FROM produto prod2 
        WHERE prod2.produto_grupo = " . $idGrupo . " AND prod2.ecommerce = '1' AND prod2.ativo = '1' AND prod2.valorVarejo > 0 
        ORDER BY prod2.valorVarejo ASC LIMIT 1");
        return $arrDados;
    }

}