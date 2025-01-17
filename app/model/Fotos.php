<?php
class Fotos {

    public function getFoto($objSqlCliente, $idFoto, $idFotografo){
        $arrDados = $objSqlCliente->executaQuery("SELECT pgfg.* FROM produto_grupo_fotografo_galeria pgfg WHERE pgfg.usuario = " . $idFotografo .  " AND pgfg.id = " . intval($idFoto));
        return $arrDados;
    }

    public function deleteFoto($objSqlCliente, $idFoto, $idFotografo){
        $arrDados = $objSqlCliente->executaQuery("DELETE FROM produto_grupo_fotografo_galeria WHERE usuario = " . $idFotografo . " AND id = " . intval($idFoto));
        return $arrDados;
    }

}