<?php
class Admin {

    public function getDominioPrimeTicket($objSqlAdmin, $dominio){
        $sqlBusca = "SELECT dp.* FROM dominio_primeticket dp WHERE dp.dominio = '".$dominio."' AND dp.ativo = '1'";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

}