<?php
class Contato {

    public function getInformacoes($objSqlCliente){
        $sqlBusca = "SELECT emp.*, cid.nome AS 'cidade_g', est.uf
        FROM empresa emp INNER JOIN cidade cid ON (cid.id = emp.cidade)
        INNER JOIN estado est ON (est.id = cid.estado)
        WHERE emp.id = 1";
        $arrDados = $objSqlCliente->executaQuery($sqlBusca);
        return $arrDados;
    }

}