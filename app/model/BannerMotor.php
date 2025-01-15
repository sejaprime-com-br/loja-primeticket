<?php
class BannerMotor {

    public function banner_motor($objSqlCliente){
        $arrDados = $objSqlCliente->executaQuery("SELECT bm.* FROM banner_motor bm WHERE bm.gatilho = 'S' LIMIT 1");
        return $arrDados;
    }
    
}