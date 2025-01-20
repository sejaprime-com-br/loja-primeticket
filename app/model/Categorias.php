<?php
class Categorias {

    public function getCategorias($objSqlAdmin){
        $sqlBusca = "SELECT fg.* FROM fornecedor_grupo fg WHERE fg.fornecedor_tipo NOT IN(1, 3)";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

}