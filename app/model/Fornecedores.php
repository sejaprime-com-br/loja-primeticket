<?php
class Fornecedores {

    public function getFornecedores($objSqlAdmin){
        $sqlBusca = "SELECT f.* FROM fornecedor f WHERE f.ativo = '1'";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

    public function getFornecedoresTransacao($objSqlAdmin){
        $sqlBusca = "SELECT t.* 
        FROM transacao t INNER JOIN fornecedor f ON (f.id = t.fornecedor)
        INNER JOIN fornecedor_interesse fi ON (fi.fornecedor = f.id)
        WHERE fi.fornecedor_tipo IN(1, 2, 4, 5, 6)
        GROUP BY t.fornecedor
        LIMIT 10";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

}