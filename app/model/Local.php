<?php
class Local {

    public function getLocalAdmin($objSqlAdmin, $idLocal){
        $arrDados = $objSqlAdmin->executaQuery("SELECT cl.*, cid.nome AS 'cidade_g', cid.estado, est.nome AS 'estado_g'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        INNER JOIN estado est ON (est.id = cid.estado)
        WHERE cl.id = " . $idLocal . " AND cl.ativo = '1'");
        return $arrDados;
    }

    public function getLocalTerceiro($objSqlAdmin, $idLocal){
        $arrDados = $objSqlAdmin->executaQuery("SELECT cl.* FROM cliente cl WHERE cl.id = " . $idLocal . " AND cl.ativo = '1'");
        return $arrDados;
    }

    public function getLocalCliente($objSqlCliente, $idLocal){
        $arrDados = $objSqlCliente->executaQuery("SELECT cl.* FROM cliente cl WHERE cl.id = " . $idLocal . " AND cl.ativo = '1'");
        return $arrDados;
    }
}