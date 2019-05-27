<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//  error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','ISO-8859-1');
ini_set('display_errors', true);
$cfg = parse_ini_file("inc/imigra.ini",true);
$programa = basename(__FILE__); 
// print_r($conver);
$banco = 'ORACLE_gicof_desenv';
include("inc/banco_pdo_class.php");
//if (isset($_GET['banco'])) {
$db = new banco_dados($banco);
$query = " alter session  set current_schema=globalgov_pess_dev";
$db->banco_query($query, 'sql');
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title> Projeto Migração </title>
</head>
<body>
   <?php 
     $dirx       = realpath('Scripts_Oracle/');
     $pes_pessoa = $dirx.'\\PES_PESSOA.sql';
     $pes_pj     = $dirx.'\\PES_PESSOA_JURIDICA.sql';
     $pes_pf     = $dirx.'\\PES_PESSOA_FISICA.sql';
     $pes_pessoa = str_replace("\\", "//", $pes_pessoa);
     $pes_pj     = str_replace("\\", "//", $pes_pj);
     $pes_pf     = str_replace("\\", "//", $pes_pf);
//     $resp->alert($pes_pessoa.'-'.$pes_pj.'-'.$pes_pf); return $resp;
     $fpess      = fopen($pes_pessoa, "w+");
     $fpjur      = fopen($pes_pj, "w+");
     $fpfis      = fopen($pes_pf, "w+");
     $query = " select * from fornec ";
     //a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b) ";
     $pes = $db->banco_query($query, 'array');
     $tela = 'CNPJ/CPF </p>';
     $y1 = 0;
     $y2 = 0;
     $y3 = 0;
     $com = 'commit;'.PHP_EOL;
     for ($a = 0; $a < count($pes); $a++)   {
        $NR_CPF_CGC      = $pes[$a]['NR_CPF_CGC'];
        $TP_FAVORECIDO_D = $pes[$a]['TP_FAVORECIDO_D'];
        $NM_FAVORECIDO   = str_replace("&","e", $pes[$a]['NM_FAVORECIDO']);
        $TX_ENDERECO     = $pes[$a]['TX_ENDERECO'];
        $NR_TELEFONE     = $pes[$a]['NR_TELEFONE'];
        $TX_CIDADE       = $pes[$a]['TX_CIDADE'];
        $NR_BANCO        = $pes[$a]['NR_BANCO'];
        $NR_AGENCIA      = $pes[$a]['NR_AGENCIA'];
        $SG_UF           = $pes[$a]['SG_UF'];
        $NR_CTA_CORRENTE = $pes[$a]['NR_CTA_CORRENTE'];
        $NR_CEP          = $pes[$a]['NR_CEP'];
        $NR_INSCR_MUNICIPAL = $pes[$a]['NR_INSCR_MUNICIPAL'];
        $NR_INSC_ESTADUAL  = $pes[$a]['NR_INSC_ESTADUAL'];
        $TP_RAMO_ATIV_D     = $pes[$a]['TP_RAMO_ATIV_D'];
        $IN_SUSPENSAO_D     = $pes[$a]['IN_SUSPENSAO_D'];
        $UG_CD_UG_SUSPEN    = $pes[$a]['UG_CD_UG_SUSPEN'];
        $IN_COLETIVO_D      = $pes[$a]['IN_COLETIVO_D'];
        $IN_EVENTUAL_D      = $pes[$a]['IN_EVENTUAL_D'];
        $ST_ATIVO           = $pes[$a]['ST_ATIVO'];
        $DT_DESATIVACAO     = $pes[$a]['DT_DESATIVACAO'];
        $NR_AGENCIA_DIR     = $pes[$a]['NR_AGENCIA_DIR'];
        $NR_BANCO_DIR       = $pes[$a]['NR_BANCO_DIR'];
        $NR_CTA_CORRENTE_DIR = $pes[$a]['NR_CTA_CORRENTE_DIR'];
        $CD_ORIGEM         = $pes[$a]['CD_ORIGEM'];
        $TP_NAT_JURIDICA   = $pes[$a]['TP_NAT_JURIDICA'];
        $FL_ZONAFRANCASOCIAL = $pes[$a]['FL_ZONAFRANCASOCIAL'];
        $sql_pesj = ''; 
        $sql_pesf = ''; 
        $sql_pes  = ''; 
        $query =  " SELECT id FROM pes_pessoa_fisica WHERE cpf = '$NR_CPF_CGC'
                        union
                   SELECT id FROM pes_pessoa_juridica WHERE CNPJ = '$NR_CPF_CGC' ";
        $id = $db->banco_query($query, 'unico');           

        if(!$id)  {

        if ($TP_FAVORECIDO_D == '2') { $tp_pessoa = 'J'; } else { $tp_pessoa = 'F'; }
//        $id = $db->banco_query(" select nvl((max(a.id) + 1),1) from pes_pessoa a ", 'unico', $resp);
        $id = $db->banco_query(" select pes_pessoa_seq.nextval from dual ", 'unico');
        if ($id) {
//        $tela .= $NR_CPF_CGC.'</p>';
          $sql_pes =  " insert into pes_pessoa (id, NM_PESSOA, TP_SITUACAO_PESSOA, TP_PESSOA, NR_VERSAO, DT_CRIADO_EM, COD_USU_CRIADO)
   values ($id, '$NM_FAVORECIDO', 1, '$tp_pessoa', 0, current_timestamp, 1 );".PHP_EOL;
           fwrite($fpess, $sql_pes);
           $y1++;
           if ($y1 > 100) { fwrite($fpess, $com); $y1 = 0; }

//        $e = $db->banco_query($sql_pes,'sql', $resp); 
          if ($tp_pessoa == 'J') {
            $cnpj = str_pad($NR_CPF_CGC,14, "0", STR_PAD_LEFT);
           $sql_pesj = " insert into pes_pessoa_juridica (id, cnpj)
                         values($id , '$cnpj'); ".PHP_EOL;
//          $e = $db->banco_query($sql_pesj,'sql', $resp); 
          fwrite($fpjur, $sql_pesj);
          $y2++;
          if ($y2 > 100) { fwrite($fpjur, $com); $y2 = 0; }
         }
         if ($tp_pessoa == 'F') {
            $cpf = str_pad($NR_CPF_CGC,11, "0", STR_PAD_LEFT);
            $sql_pesf = " insert into pes_pessoa_fisica (id, CPF, IND_TRANSX,IND_DOADOR_SANGUE,IND_DOADOR_ORGAO)
                         values($id , '$cpf', 0, 0, 0);".PHP_EOL;
//          $e = $db->banco_query($sql_pesf,'sql', $resp); 
         fwrite($fpfis, $sql_pesf);
         $y3++;
         if ($y3 > 100) { fwrite($fpfis, $com); $y3 = 0; }
        }
        $tela .= $NR_CPF_CGC.'</p>';
        echo $tela;
       } 
     }
    } 
   fwrite($fpess, $com);
   fclose($fpess);
   fwrite($fpjur, $com);
   fclose($fpjur);
   fwrite($fpfis, $com);
   fclose($fpfis);
    ?>
</body>
</html>

