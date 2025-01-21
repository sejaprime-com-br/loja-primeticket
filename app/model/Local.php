<?php
class Local {

    public function getLocalAdmin($objSqlAdmin, $idLocal){
        $arrDadosFinal = array();
        $sqlBusca = "SELECT cl.*, cid.nome AS 'cidade_g', cid.estado, est.nome AS 'estado_g', 
        est.uf AS 'estado_sigla', (SELECT COUNT(mi.id) FROM motor_ingresso mi WHERE mi.cliente = cl.id) AS 'motor_ingresso',
        (SELECT COUNT(me.id) FROM motor_eventos me WHERE me.cliente = cl.id) AS 'motor_eventos'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        INNER JOIN estado est ON (est.id = cid.estado)
        WHERE cl.id = " . (int)$idLocal . " AND cl.ativo = '1' AND cl.bloqueado IN('N')";
        $arrDados = $objSqlAdmin->executaQuery($sqlBusca);
        if(isset($arrDados[0]['motor_ingresso']) && (int)$arrDados[0]['motor_ingresso'] > 0){
            $arrDadosFinal = $arrDados;
            $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT mi.ingressoid FROM motor_ingresso mi WHERE mi.cliente = " . intval($arrDados[0]['id']));
            $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
            $arrDadosFinal[0]['motor']       = 'motor_ingresso';
        } else if(isset($arrDados[0]['motor_eventos']) && (int)$arrDados[0]['motor_eventos'] > 0){
            $arrDadosFinal = $arrDados;
            $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT me.ingressoid FROM motor_eventos me WHERE me.cliente = " . intval($arrDados[0]['id']));
            $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
            $arrDadosFinal[0]['motor']       = 'motor_eventos';
        }
        return $arrDadosFinal;
    }

    public function getLocalTerceiro($objSqlAdmin, $idLocal, $objSqlCliente, $idLocalSistema){
        $fornecedorAdmin = 0;
        $arrDadosFinal = array();
        $arrDados = $objSqlAdmin->executaQuery("SELECT cl.*, cid.nome AS 'cidade_g', cid.estado, est.nome AS 'estado_g',
        est.uf AS 'estado_sigla', (SELECT COUNT(mi.id) FROM motor_ingresso mi WHERE mi.cliente = cl.id) AS 'motor_ingresso',
        (SELECT COUNT(me.id) FROM motor_eventos me WHERE me.cliente = cl.id) AS 'motor_eventos'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        INNER JOIN estado est ON (est.id = cid.estado)
        WHERE cl.id = " . (int)$idLocal . " AND cl.ativo = '1' AND cl.bloqueado IN('N')");
        if(isset($arrDados[0]['id']) && $arrDados[0]['id'] != '' && (int)$idLocal == (int)$idLocalSistema){
            $arrDadosFinal = $arrDados;
            if(isset($arrDados[0]['motor_ingresso']) && (int)$arrDados[0]['motor_ingresso'] > 0){
                $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT mi.ingressoid FROM motor_ingresso mi WHERE mi.cliente = " . intval($arrDados[0]['id']));
                $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
                $arrDadosFinal[0]['motor']       = 'motor_ingresso';
            } else if(isset($arrDados[0]['motor_eventos']) && (int)$arrDados[0]['motor_eventos'] > 0){
                $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT me.ingressoid FROM motor_eventos me WHERE me.cliente = " . intval($arrDados[0]['id']));
                $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
                $arrDadosFinal[0]['motor']       = 'motor_eventos';
            }
        } else if(isset($arrDados[0]['id']) && $arrDados[0]['id'] != '' && (int)$arrDados[0]['fornecedor'] > 0){
            $fornecedorAdmin = (int)$arrDados[0]['fornecedor'];
            $sqlBusca = "SELECT fs.* FROM fornecedor_servico fs 
            WHERE fs.fornecedorAdmin = " . (int)$fornecedorAdmin . " AND fs.tipo_do_fornecedor IN('L') AND fs.tem_loja IN('S')";
            $arrDadosTerceiro = $objSqlCliente->executaQuery($sqlBusca);
            if(isset($arrDadosTerceiro[0]['id'])){
                $arrDadosFinal = $arrDados;
                if(isset($arrDados[0]['motor_ingresso']) && (int)$arrDados[0]['motor_ingresso'] > 0){
                    $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT mi.ingressoid FROM motor_ingresso mi WHERE mi.cliente = " . intval($arrDados[0]['id']));
                    $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
                    $arrDadosFinal[0]['motor']       = 'motor_ingresso';
                } else if(isset($arrDados[0]['motor_eventos']) && (int)$arrDados[0]['motor_eventos'] > 0){
                    $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT me.ingressoid FROM motor_eventos me WHERE me.cliente = " . intval($arrDados[0]['id']));
                    $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
                    $arrDadosFinal[0]['motor']       = 'motor_eventos';
                }
            }
        } 
        return $arrDadosFinal;
    }

    public function getLocalTerceiroId($objSqlAdmin, $objSqlCliente, $idLocalSistema){
        $fornecedorAdmin = 0;
        $arrDadosFinal = array();
        $arrDados = $objSqlAdmin->executaQuery("SELECT cl.*, cid.nome AS 'cidade_g', cid.estado, est.nome AS 'estado_g',
        est.uf AS 'estado_sigla', (SELECT COUNT(mi.id) FROM motor_ingresso mi WHERE mi.cliente = cl.id) AS 'motor_ingresso',
        (SELECT COUNT(me.id) FROM motor_eventos me WHERE me.cliente = cl.id) AS 'motor_eventos'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        INNER JOIN estado est ON (est.id = cid.estado)
        WHERE cl.id = " . (int)$idLocalSistema . " AND cl.ativo = '1' AND cl.bloqueado IN('N')");
        if(isset($arrDados[0]['id']) && $arrDados[0]['id'] != ''){
            $arrDadosFinal = $arrDados;
            if(isset($arrDados[0]['motor_ingresso']) && (int)$arrDados[0]['motor_ingresso'] > 0){
                $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT mi.ingressoid FROM motor_ingresso mi WHERE mi.cliente = " . intval($arrDados[0]['id']));
                $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
                $arrDadosFinal[0]['motor']       = 'motor_ingresso';
            } else if(isset($arrDados[0]['motor_eventos']) && (int)$arrDados[0]['motor_eventos'] > 0){
                $arrDadosMotor = $objSqlAdmin->executaQuery("SELECT me.ingressoid FROM motor_eventos me WHERE me.cliente = " . intval($arrDados[0]['id']));
                $arrDadosFinal[0]['ingresso_id'] = trim($arrDadosMotor[0]['ingressoid']);
                $arrDadosFinal[0]['motor']       = 'motor_eventos';
            }
        } 
        return $arrDadosFinal;
    }

    public function getLocalDominio($objSqlAdmin, $idCliente){
        $arrDados = $objSqlAdmin->executaQuery("SELECT cl.*, cid.nome AS 'cidade_g', cid.estado, est.nome AS 'estado_g',
        est.uf AS 'estado_sigla'
        FROM cliente cl INNER JOIN cidade cid ON (cid.id = cl.cidade)
        INNER JOIN estado est ON (est.id = cid.estado)
        WHERE cl.id = " . (int)$idCliente . " AND cl.ativo = '1'");
        return $arrDados;
    }

    public function getLocalCodigo($objSqlAdmin, $codigoLocal){
        $codigoLocal = trim($codigoLocal);
        $sqlBuscaCodigo = "SELECT cl.* FROM cliente cl 
        WHERE cl.cdAcesso = '" . $codigoLocal . "' AND cl.ativo = '1' AND cl.bloqueado IN('N')";
        $arrDados = $objSqlAdmin->executaQuery($sqlBuscaCodigo);
        return $arrDados;
    }

    public function getLocaisPrimeTicket($objSqlAdmin){
        $sqlBuscaLocal = "SELECT cl.* FROM cliente cl INNER JOIN fornecedor f ON (f.id = cl.fornecedor)
        INNER JOIN fornecedor_interesse fi ON (fi.fornecedor = f.id) 
        WHERE cl.ativo = '1' AND cl.bloqueado = 'N' AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdLogin != '' AND cl.bdSenha != ''
        AND fi.fornecedor_tipo NOT IN(1, 3)
        GROUP BY cl.id";
        $arrDados = $objSqlAdmin->executaQuery($sqlBuscaLocal);
        return $arrDados;
    }

    public function getLocaisDominioTerceiro($objSqlAdmin, $idLocalSistema){
        $resultLocal   = $objSqlAdmin->executaQuery("SELECT * FROM cliente cl 
        WHERE cl.id = " . intval($idLocalSistema) . " AND cl.ativo = '1' AND cl.bloqueado IN('N')");
        $arrDadosFinal = array();
        $strFornecedores = '';

        if(isset($resultLocal[0]['bdLogin']) && $resultLocal[0]['bdLogin'] != '' && isset($resultLocal[0]['bdLocal']) && $resultLocal[0]['bdLocal'] != '' && isset($resultLocal[0]['bdBase']) && $resultLocal[0]['bdBase'] != '' && isset($resultLocal[0]['bdSenha']) && $resultLocal[0]['bdSenha'] != ''){
            $objSqlCliente = new sql($resultLocal[0]['bdLogin'], $resultLocal[0]['bdBase'], $resultLocal[0]['bdLocal'], $resultLocal[0]['bdSenha']);
            $sqlBusca = "SELECT fs.* FROM fornecedor_servico fs 
            WHERE fs.tipo_do_fornecedor IN('L') AND fs.tem_loja IN('S')";
            $arrDadosTerceiro = $objSqlCliente->executaQuery($sqlBusca);
            if(isset($arrDadosTerceiro[0]['id']) && $arrDadosTerceiro[0]['id'] != ''){
                $sqlBuscaLocalCliente = "SELECT cl.* FROM cliente cl INNER JOIN fornecedor f ON (f.id = cl.fornecedor)
                LEFT JOIN fornecedor_interesse fi ON (fi.fornecedor = f.id) 
                WHERE cl.ativo = '1' AND cl.bloqueado IN('N') AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdLogin != '' AND cl.bdSenha != ''
                AND cl.id IN(".$idLocalSistema.")
                GROUP BY cl.id";
                $arrDadosCliente = $objSqlAdmin->executaQuery($sqlBuscaLocalCliente);

                $intCont = 0;
                if(isset($arrDadosTerceiro[0])){
                    foreach($arrDadosTerceiro as $arrForn){
                        $intCont++;
                        $strFornecedores .= (int)$intCont == 1 ? (int)$arrForn['fornecedorAdmin'] : "," . (int)$arrForn['fornecedorAdmin'];
                    }
                }
                $sqlBuscaLocal = "SELECT cl.* FROM cliente cl INNER JOIN fornecedor f ON (f.id = cl.fornecedor)
                LEFT JOIN fornecedor_interesse fi ON (fi.fornecedor = f.id) 
                WHERE cl.ativo = '1' AND cl.bloqueado IN('N') AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdLogin != '' AND cl.bdSenha != ''
                AND f.id IN(".$strFornecedores.")
                GROUP BY cl.id";
                $arrDados = $objSqlAdmin->executaQuery($sqlBuscaLocal);
                $arrDadosFinal = $arrDados;
                if(isset($arrDadosCliente[0]['id']) && $arrDadosCliente[0]['id'] > 0){
                    array_push($arrDadosFinal, $arrDadosCliente[0]);
                }
            } else {
                $sqlBuscaLocal = "SELECT cl.* FROM cliente cl INNER JOIN fornecedor f ON (f.id = cl.fornecedor)
                LEFT JOIN fornecedor_interesse fi ON (fi.fornecedor = f.id) 
                WHERE cl.ativo = '1' AND cl.bloqueado IN('N') AND cl.bdBase != '' AND cl.bdLocal != '' AND cl.bdLogin != '' AND cl.bdSenha != ''
                AND cl.id IN(".$idLocalSistema.")
                GROUP BY cl.id";
                $arrDados = $objSqlAdmin->executaQuery($sqlBuscaLocal);
                $arrDadosFinal = $arrDados;
            }
        } 
        return $arrDadosFinal;
    }

}