<?php
class sql {

    private $Conexao;

    //construtor abre conexão banco
    public function __construct($login = '', $base = '', $endereco = '', $senha = '') {
        if ($login == '' && $base = '' && $endereco = '' && $senha = '') {
            $this->login    = $_SESSION[PREFIX]['bd']['login'];
            $this->senha    = $_SESSION[PREFIX]['bd']['senha'];
            $this->base     = $_SESSION[PREFIX]['bd']['base'];
            $this->endereco = $_SESSION[PREFIX]['bd']['local'];
            $this->port     = 3306;
        } else {
            $this->login    = $login;
            $this->senha    = $senha;
            $this->base     = $base;
            $this->endereco = $endereco;
            $this->port     = 3306;
        }
    }

    public function executaQuery($sql){
        $this->Conexao = 'mysql:host='.$this->endereco.';port='.$this->port.';dbname='.$this->base;
        $Conecta = new PDO($this->Conexao, $this->login, $this->senha);
        try{
          $Conecta->exec("SET CHARACTER SET utf8");
          $queryList = $Conecta->prepare($sql);
          $queryList->execute();
          $restultListAll = $queryList->fetchAll(PDO::FETCH_ASSOC);

          $this->Conexao = null; 

          return $restultListAll;
        }  catch (PDOException $erroListUsuarioAll){
          echo 'Erro ao listar os registros ' . $erroListUsuarioAll->getMessage();
        }
    }

    function executaQueryPaginada($strQuery, $intPagina, $intQuantidade = 50) {
        $intQuantidade = 50;
        $intLimite = 0;
        for ($intI = 1; $intI <= $intPagina; $intI ++) {
            $intLimite += $intQuantidade;
        }
        return $this->executaQuery($strQuery . " LIMIT " . ($intLimite - $intQuantidade) . ", " . $intQuantidade);
    }

    function executaInsert($campos, $tabela, $tipo = "", $debug = '') {
        $this->Conexao = 'mysql:host='.$this->endereco.';port='.$this->port.';dbname='.$this->base;
        $Conecta = new PDO($this->Conexao, $this->login, $this->senha);
        $fields = implode(", ",array_keys($campos));
        $values = "'".implode("', '",array_values($campos))."'";

        $sqlCreate = "INSERT INTO ".$tabela." (".$fields.") VALUES (".$values.")";
        try{
            $queryCreate = $Conecta->prepare($sqlCreate);
            $queryCreate->execute();

            if ($tipo == 1) {
                $last_id = $Conecta->lastInsertId();
                $this->Conexao = null; 	
                return $last_id;
            } else {
                $this->Conexao = null; 
                return $sqlCreate;
            }
        }  catch (PDOException $erroCreate){
            echo 'Erro ao cadastrar tabela ' . $tabela . ' '.$erroCreate->getMessage();
        } 
    }

    function executaUpdate($campos, $tabela, $chaveupdt = 'id', $debug = '') {
        $this->Conexao = 'mysql:host='.$this->endereco.';port='.$this->port.';dbname='.$this->base;
        $Conecta = new PDO($this->Conexao, $this->login, $this->senha);

        $strupdate = "";

        foreach ($campos as $chave => $valor) {
            if ($chave != 'id') {
                $strupdate .= "," . $chave . "=" . (($valor == 'NULL' || $valor == '') ? "NULL" : "'" . $valor . "' ");
            }
        }

        $strupdate = substr($strupdate, 1, strlen($strupdate));
        $sqlUpdate = "UPDATE " . $tabela . " SET " . $strupdate . " WHERE " . $chaveupdt . " = " . $campos[$chaveupdt];
        try{
            $queryUpdate = $Conecta->prepare($sqlUpdate);
            $queryUpdate->execute();
            $restultUpdate = $queryUpdate->fetchAll(PDO::FETCH_ASSOC);

            $this->Conexao = null; 

            return $restultUpdate;
        }  catch (PDOException $erroUpdate){
            echo 'Erro ao editar tabela ' . $tabela . ' ' .$erroUpdate->getMessage();
        }
    }

    function executaDelete($id, $tabela, $chave = "") {
        $this->Conexao = 'mysql:host='.$this->endereco.';port='.$this->port.';dbname='.$this->base;
        $Conecta = new PDO($this->Conexao, $this->login, $this->senha);

        $retorno = '';
        if ($chave != "") {
            $sqlDeletex = "DELETE FROM " . $tabela . " WHERE " . $chave . " = '" . $id . "' ";
        } else {
            $sqlDeletex = "DELETE FROM " . $tabela . " WHERE id = '" . $id . "' ";
        }
        try{
            $queryDeletex = $Conecta->prepare($sqlDeletex);
            $objResult = $queryDeletex->execute();
            if ($objResult) {
                $retorno = TRUE;
            } else {
                $retorno = FALSE;
            }
            $this->Conexao = null; 
            return $retorno;
        } 
        catch (PDOException $erroDeletex){
            echo 'Erro ao deletar registro '.$erroDeletex->getMessage();
        }
    }
}