<?php
class PersonalizacaoLayout {

    public function personalizacao_motor($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT pl.* FROM personalizacao_layout pl");
        return $arrDados;
    }
    
}