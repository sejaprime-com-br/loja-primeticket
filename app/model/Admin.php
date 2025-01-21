<?php
class Admin {

    public function getDominioPrimeTicket($objSqlAdmin, $dominio){
        $sqlBusca = "SELECT dp.* FROM dominio_primeticket dp WHERE dp.dominio = '".$dominio."' AND dp.ativo = '1'";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

    public function getConfEmail($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT 
                                                                      IF(tipo='F', nome, IFNULL(fantasia, razao)) AS 'nomeEmpresa',
                                                                      emailIngresso, 
                                                                      emailIngressoHost, 
                                                                      emailIngressoSenha,
                                                                      emailIngressoPort,
                                                                      emailIngressoSecure
                                                                    FROM empresa 
                                                                    WHERE id = 1");
        return $arrDados;
    }

}