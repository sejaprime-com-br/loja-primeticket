<?php
class BannerEvento {

    public function getBannerEvento($objSqlCliente, $idEvento){
        $arrDados = $objSqlCliente->executaQuery("SELECT pg.arquivoBanner FROM produto_grupo pg WHERE pg.ativo = 'S' AND pg.id = " . $idEvento . " LIMIT 1");
        return $arrDados;
    }
    
}