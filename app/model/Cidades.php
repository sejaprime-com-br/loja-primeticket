<?php
class Cidades {

    public function getCidadesClientesAdmin($objSqlAdmin){
        $sqlBusca = "SELECT cl.cidade, cid.nome AS 'cidadeNome'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        WHERE cl.ativo = '1' AND cl.bloqueado IN('N') AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdLogin != '' AND cl.bdSenha != ''
        GROUP BY cl.cidade";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

    public function getCidadesClientesTerceiro($objSqlAdmin, $cliente_admin){
        $strFornecedores = '';
        $arrDadosAdminCliente = $objSqlAdmin->executaQuery("SELECT cl.id, cl.tipo, cl.nome AS 'nomeEmp', cl.fantasia, cl.razao, cl.fornecedor, cl.bdLogin, cl.bdBase, cl.bdLocal, cl.bdSenha
        FROM cliente cl WHERE cl.bdLogin != '' AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdSenha != '' AND cl.id = " . (int)$cliente_admin);
        $objSqlCliente = new sql($arrDadosAdminCliente[0]['bdLogin'], $arrDadosAdminCliente[0]['bdBase'], $arrDadosAdminCliente[0]['bdLocal'], $arrDadosAdminCliente[0]['bdSenha']);
        $sqlBuscaT = "SELECT fs.* FROM fornecedor_servico fs 
        WHERE fs.tipo_do_fornecedor IN('L') AND fs.tem_loja IN('S') AND fs.fornecedorAdmin > 0";
        $arrDadosTerceiro = $objSqlCliente->executaQuery($sqlBuscaT);
        $intCont = 0;
        $strFornecedores .= (int)$arrDadosAdminCliente[0]['fornecedor'];
        if(isset($arrDadosTerceiro[0])){
            foreach($arrDadosTerceiro as $arrForn){
                $intCont++;
                $strFornecedores .= "," . (int)$arrForn['fornecedorAdmin'];
            }
        }

        $sqlBusca = "SELECT cl.cidade, cid.nome AS 'cidadeNome'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        WHERE cl.ativo = '1' AND cl.bloqueado IN('N') AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdLogin != '' AND cl.bdSenha != ''
        AND cl.fornecedor IN(".$strFornecedores.")
        GROUP BY cl.cidade";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        return $arrDados;
    }

}