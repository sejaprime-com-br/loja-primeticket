<?php
class Redes {

    public function getRedes($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT r.* FROM rede_social r WHERE r.rede IN(1, 2, 3) AND r.link != ''");
        return $arrDados;
    }

}