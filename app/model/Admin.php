<?php
class Admin {

    public function getDominioPrimeTicket($objSqlAdmin, $dominio){
        $arrDados = $objSqlAdmin->executaQuery("SELECT dp.* FROM dominio_primeticket dp WHERE dp.dominio = '".$dominio."' AND dp.ativo = '1'");
        return $arrDados;
    }

}