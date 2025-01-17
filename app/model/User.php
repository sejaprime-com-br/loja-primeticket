<?php
class User {

    public function getUser($usuario, $senha, $objSqlCliente){
        $sqlBuscaUser = "SELECT us.*, fu.ativo AS 'funcionario_ativo' 
        FROM usuario us inner join funcionario fu ON fu.id = us.funcionario
        inner join funcionario_grupo fg ON fg.id = fu.funcionario_grupo
        WHERE us.usuario = '" . trim($usuario) . "' AND us.senha = '" . md5($senha) . "'
        AND fg.nome = 'FOTOGRAFO'";
        $arrDados = $objSqlCliente->executaQuery($sqlBuscaUser);
        return $arrDados;
    }

    public function getUserId($idUser, $objSqlCliente){
        $sqlBuscaUser = "SELECT us.*, fu.ativo AS 'funcionario_ativo' 
        FROM usuario us inner join funcionario fu ON fu.id = us.funcionario
        inner join funcionario_grupo fg ON fg.id = fu.funcionario_grupo
        WHERE us.id = " . intval($idUser);
        $arrDados = $objSqlCliente->executaQuery($sqlBuscaUser);
        return $arrDados;
    }

}