<?php
/**  
    Ajax Streaming without polling
*
*
    declare cursor cc is 
 select 'alter table ' || xx.tabela || ' enable constraint ' || zz.constraint_name || ';'    as const from
 (select table_name tabela from user_tables) xx, user_constraints zz
where zz.table_name = xx.tabela
 and zz.constraint_type in ('C', 'P')
 and zz.status  =  'DISABLED';
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end; 

// disable foreign keys de outras tabelas.

      select zz.table_name, zz.constraint_name fk from (
select  constraint_name  chave 
  from user_constraints where table_name = 'NOTAF_SAI_BASE' and constraint_type in ('P')  and status = 'ENABLED' )  xx, user_constraints zz
where  xx.chave = zz.r_constraint_name
 and  zz.constraint_type = 'R'
 and status = 'ENABLED' 
   
*/         
//type octet-stream. 
header('Content-Type: text/octet-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
ini_set("memory_limit", -1);
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
// ini_set('default_charset','ISO-8859-1');
ini_set('display_errors',true);
if (isset($_GET['banco']))  {
   $tabela = $_GET['banco'];
 } else {
   send_message('Sem banco escolhido!',0); 
   exit;
 }
$desti     = $_GET['desti'];
$banco     = $_GET['banco'];
$origem    = $_GET['origem'];
$saida     = $_GET['saida']; 
$ind       = $_GET['ind']; 
include("inc/banco_pdo_class.php");
$db = new banco_dados($banco);
$or = explode(';', $origem);
$origem = str_replace('////','//',$origem);
if (count($or) === 1) {
    $query = " select * from $origem where rownum < 2000 ";
    $pes = $db->banco_query($query,'array', $resp);
} else { $pes = file($or[0]); serialize($pes); }  
//    foreach($pes as $linha)  {
if (!is_array($pes)) { send_message('Sem registros de origem : '.$origem, 0, $ind); }
//$msg = $desti.'-'.$banco.'-'.$total.'-'.$saida.'-'.$origem;
// send_message("$msg", 0);
// echo($desti($banco, $pes, $saida, $ind));
// send_message('Dados '.$desti.$saida.$ind, 0, $ind);   exit;
echo($desti($banco, $pes, $saida, $ind));
//  ------------------------------------s---------------
send_message(date('G:i:s').' Concluido!', 0);
exit;

function pes_tipo_logradouro($banco, $pes, $saida, $ind) {
     global $db;
     global $desti;
//     unserialize($pes);
     $total = count($pes);
     send_message('Iniciando carga tabela '.$desti.': Total de registros: '.$total, 0, $ind); 
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_tplogra  = $dirx.'\\PES_TIPO_LOGRADOURO.sql';
        $pes_tplogra  = str_replace("\\", "//", $pes_tplogra);
//     $resp->alert($pes_pessoa.'-'.$pes_pj.'-'.$pes_pf); return $resp;
        $ftplogra     = fopen($pes_tplogra, "w+");
     }
     $z = 0;
     $p = 0;
     $com = 'commit;'.PHP_EOL;
     for ($a = 0; $a < count($pes); $a++)  {
         $linha = explode(';', $pes[$a]); 
         $NM_TIPO_LOGRADOURO = $linha[3];
         $ABREVIATURA        = trim($linha[2]);
         $query = "select id from pes_tipo_logradouro where abreviatura = '$ABREVIATURA' ";
         $idx = $db->banco_query($query,'unico');
         if (!$idx)  {
           $qq = "select pes_tipo_logradouro_seq.nextval from dual";
           $id = $db->banco_query($qq,'unico');
           $sql_tplogra = " insert into pes_tipo_logradouro (ID, NM_TIPO_LOGRADOURO, ABREVIATURA, nr_versao, DT_CRIADO_EM, COD_USU_CRIADO) values ($id, '$NM_TIPO_LOGRADOURO', '$ABREVIATURA', 0, current_timestamp, 1 )";
             if ($saida == '1')  {
                $sql_tplogra .= ';'.PHP_EOL; 
                   fwrite($ftplogra, $sql_tplogra);
                   $y++;
                   if ($y > 100) { fwrite($ftplogra, $com); $y = 0; }
                }  else {  $db->banco_query($sql_tplogra,'sql'); }   
         }
         $p = floor($a*100/$total);   //Progress
         if ($p > 0 && $p > $z) {  $z = $p; send_message($p . '% . Regs.: '.$a, $p, $ind); }
     }
     if ($saida == '1')  {
       fwrite($ftplogra, $com);
       fclose($ftplogra);
     }
     send_message(date('G:i:s').' Concluido!', 0);
     exit;
 }    
/*
  CREATE TABLE "JGOULART"."PES_TIPO_LOGRADOURO" 
   (  "ID" NUMBER(18,0) NOT NULL ENABLE, 
  "NM_TIPO_LOGRADOURO" VARCHAR2(500 BYTE) NOT NULL ENABLE, 
  "ABREVIATURA" VARCHAR2(5 BYTE) NOT NULL ENABLE, 
  "NR_VERSAO" NUMBER(10,0), 
  "DT_CRIADO_EM" TIMESTAMP (6) NOT NULL ENABLE, 
  "COD_USU_CRIADO" NUMBER(18,0) NOT NULL ENABLE, 
  "DT_ALTERADO_EM" TIMESTAMP (6), 
  "COD_USU_ALTERADO" NUMBER(18,0), 
   CONSTRAINT "PES_TIPO_LOGRADOURO_PK" PRIMARY KEY ("ID") DISABLE, 
   CONSTRAINT "PES_TIPO_LOGRAD_ABREVIATURA_UK" UNIQUE ("ABREVIATURA")
*/

function pes_pessoa($banco, $pes, $saida, $ind) {
     global $db;
     global $desti;
     $total = count($pes);
     send_message('Iniciando carga tabela '.$desti.': Total de registros: '.$total, 0, $ind); 
//     $serverTime = time();
//     send_message($serverTime.'-'.date('G:i:s').' Inserindo registros... ', 0, $ind);
//     exit;
     //     $resp->alert(print_r($pes,true)); return $resp;
     if ($saida == '1')  {
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
     }
     //a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b) ";
     // $pes = $db->banco_query($query, 'array');
     $z  = 0;
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
        $id1 = $db->banco_query($query, 'unico');           
        if(!$id1)  {
           if ($TP_FAVORECIDO_D == '2') { $tp_pessoa = 'J'; } else { $tp_pessoa = 'F'; }
//        $id = $db->banco_query(" select nvl((max(a.id) + 1),1) from pes_pessoa a ", 'unico', $resp);
           $id = $db->banco_query(" select pes_pessoa_seq.nextval from dual ", 'unico');
           if ($id) {
//        $tela .= $NR_CPF_CGC.'</p>';
              $sql_pes =  " insert into pes_pessoa (id, NM_PESSOA, TP_SITUACAO_PESSOA, TP_PESSOA, NR_VERSAO, DT_CRIADO_EM, COD_USU_CRIADO)  values ($id, '$NM_FAVORECIDO', 1, '$tp_pessoa', 0, current_timestamp, 1 ) ";
              if ($saida == '1')  {
                 $sql_pes .= ';'.PHP_EOL; 
                 fwrite($fpess, $sql_pes);
                 $y1++;
                 if ($y1 > 100) { fwrite($fpess, $com); $y1 = 0; }
              } else {
                $e = $db->banco_query($sql_pes,'sql');
             }   
             if ($tp_pessoa == 'J') {
                $cnpj = str_pad($NR_CPF_CGC,14, "0", STR_PAD_LEFT);
                $sql_pesj = " insert into pes_pessoa_juridica (id, cnpj)  values($id , '$cnpj') ";
                if ($saida == '1')  {
                   $sql_pesj .= ';'.PHP_EOL; 
                   fwrite($fpjur, $sql_pesj);
                   $y2++;
                   if ($y2 > 100) { fwrite($fpjur, $com); $y2 = 0; }
                }  else { $e = $db->banco_query($sql_pesj,'sql'); }   
              }
              if ($tp_pessoa == 'F') {
                  $cpf = str_pad($NR_CPF_CGC,11, "0", STR_PAD_LEFT);
                   $sql_pesf = " insert into pes_pessoa_fisica (id, CPF, IND_TRANSX,IND_DOADOR_SANGUE,IND_DOADOR_ORGAO) values($id , '$cpf', 0, 0, 0) ";
                  if ($saida == '1')  {
                     $sql_pesf .= ';'.PHP_EOL; 
                     fwrite($fpfis, $sql_pesf);
                     $y3++;
                    if ($y3 > 100) { fwrite($fpfis, $com); $y3 = 0; }
                  } else { $e = $db->banco_query($sql_pesf,'sql'); }   
             }
             $p = floor($a*100/$total);   //Progress
             if ($p > 0 && $p > $z) {  $z = $p; send_message($p . '% . Regs.: '.$a, $p, $ind); }
           } 
       }
    } 
    if ($saida == '1')  {
       fwrite($fpess, $com);
       fclose($fpess);
       fwrite($fpjur, $com);
       fclose($fpjur);
       fwrite($fpfis, $com);
       fclose($fpfis);
    }
     send_message('Encerrado! '.$total.' Registros.', 0,  $ind);
      exit;
}
 
function send_message($message, $progress, $ind=0) {
    $d = array('message' => $message , 'progress' => $progress, 'ind' => $ind);
    echo json_encode($d);
    //PUSH THE data out by all FORCE POSSIBLE
    ob_flush();
    flush();
}

function pes_bairro_distrito($banco, $pes, $saida, $ind) {
     global $db;
     global $desti;
     $total = count($pes);
     send_message('Iniciando carga tabela '.$desti.': Total de registros: '.$total, 0, $ind); 
     send_message('Encerrado! '.$total.' Registros.', 0,  $ind);
     exit;
}


function pes_logradouro($banco, $pes, $saida, $ind) {
     global $db;
     global $desti;
     $total = count($pes);
     send_message('Iniciando carga tabela '.$desti.': Total de registros: '.$total, 0, $ind); 
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_logra  = $dirx.'\\PES_LOGRADOURO.sql';
        $pes_logra  = str_replace("\\", "//", $pes_logra);
//     $resp->alert($pes_pessoa.'-'.$pes_pj.'-'.$pes_pf); return $resp;
        $flogra     = fopen($pes_logra, "w+");
     }
     $z = 0;
     $p = 0;
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
        $query = " select id from  pes_municipio where trim(upper(convert(nm_municipio, 'SF7ASCII'))) = '$TX_CIDADE' ";
        $id_pes_municipio  = $db->banco_query($query, 'unico');
        if ($id_pes_municipio) {
           $reg_logradouro     = Insere_Logradouro($TX_ENDERECO, $id_pes_municipio, '', $NR_CEP);
           if ($saida == '1')  {
              $reg_logradouro .= ';'.PHP_EOL; 
              fwrite($flogra, $reg_logradouro);
              $y++;
              if ($y > 100) { fwrite($flogra, $com); $y = 0; }
           } else { $e = $db->banco_query($reg_logradouro,'sql'); }   
        }  
        $p = floor($a*100/$total);   //Progress
        if ($p > 0 && $p > $z) {  $z = $p; send_message($p . '% . Regs.: '.$a, $p, $ind); }
     }
    if ($saida == '1')  {
       fwrite($flogra, $com);
       fclose($flogra);
    }   
     send_message('Encerrado! '.$total.' Registros.', 0,  $ind);
     exit;
}

function Insere_Logradouro($rua, $id_pes_municipio, $id_pes_bairro_distrito='', $nr_cep='')  {
    $id_pes_tipo_logradouro = '1'; //  TODO
    $sql = " insert into pes_logradouro  (id , id_pes_municipio, id_pes_tipo_logradouro, nm_logradouro, cep_logradouro, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_logradouro a), $id_pes_municipio, $id_pes_tipo_logradouro, '$rua', '$nr_cep', current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
}

function pes_endereco($banco, $pes, $saida, $ind) {
     global $db;
     global $desti;
     $total = count($pes);
     send_message('Iniciando carga tabela '.$desti.': Total de registros: '.$total, 0, $ind); 
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_endereco = $dirx.'\\PES_ENDERECO.sql';
        $pes_endereco = str_replace("\\", "//", $pes_endereco);
        $fpesen      = fopen($pes_endereco, "w+");
     }
     $com = 'commit;'.PHP_EOL;
     $z = 0; $p=0;
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
        if (!$NR_INSC_ESTADUAL) { $NR_INSC_ESTADUAL = ' ';}
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
        $qp = " SELECT ID  FROM PES_PESSOA_JURIDICA WHERE CNPJ = '$NR_CPF_CGC'
          UNION
                SELECT ID  FROM PES_PESSOA_FISICA WHERE CPF = '$NR_CPF_CGC' ";
        $id_pes_pessoa = $db->banco_query($qp, 'unico'); 
        $qm = " select id from  pes_municipio where trim(upper(convert(nm_municipio, 'SF7ASCII'))) = '$TX_CIDADE' ";
        $id_pes_municipio  = $db->banco_query($qm, 'unico');
        if ($id_pes_municipio) {
           $ql = " select id from  pes_logradouro a where id_pes_municipio = $id_pes_municipio and a.nm_logradouro like '$TX_ENDERECO' ";
          $id_pes_logradouro = $db->banco_query($ql, 'unico');
          $reg_ender = '';
          if ($id_pes_municipio && $id_pes_pessoa && $id_pes_logradouro) {
             $reg_ender    = insere_endereco($id_pes_pessoa, $id_pes_municipio, $id_pes_logradouro);
            if ($saida == '1')  {
               $reg_ender .= ';'.PHP_EOL; 
               fwrite($fpesen, $reg_ender);
              $y++;
              if ($y > 100) { fwrite($fpesen, $com); $y = 0; }
             } else { $e = $db->banco_query($reg_ender,'sql'); }   
           } //  else {  send_message('Erro! Registro eliminado: '.$reg_ender, 0,  $ind);     }
      } 
      $p = floor($a*100/$total);   //Progress
      if ($p > 0 && $p > $z) {  $z = $p; send_message($p . '% . Regs.: '.$a, $p, $ind); }
    }
      if ($saida == '1')  {
       fwrite($fpesen, $com);
       fclose($fpesen);
       }   
       send_message('Encerrado! '.$total.' Registros.', 0,  $ind);
     exit;
}

function insere_endereco($id_pes_pessoa, $id_pes_municipio, $id_pes_logradouro) {
    $sql = " insert into pes_endereco  (id , id_pes_pessoa, id_pes_logradouro, nr_imovel, dt_ativo_de, tp_endereco, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_endereco a), $id_pes_pessoa, $id_pes_logradouro, 0, current_timestamp, 3, current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
}


function pes_fornecedor($banco, $pes, $saida, $ind) {
     global $db;
     global $desti;
     $total = count($pes);
     send_message('Iniciando carga tabela '.$desti.': Total de registros: '.$total, 0, $ind); 
//     $serverTime = time();
//     send_message($serverTime, date('G:i:s').' Inserindo registros... ', 0, $ind);
//     exit;
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_fornecedor = $dirx.'\\PES_FORNECEDOR.sql';
        $pes_fornecedor = str_replace("\\", "//", $pes_forncecedor);
        $fpesf      = fopen($pes_fornecedor, "w+");
     }
  //   $query = " select * from fornec where rownum < 10 ";
     //a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b) ";
  //   $pes = $db->banco_query($query, 'array', $resp);
     $z = 0;
     $p = 0;
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
        $qp = " SELECT ID  FROM PES_PESSOA_JURIDICA WHERE CNPJ = '$NR_CPF_CGC'
          UNION
                SELECT ID  FROM PES_PESSOA_FISICA WHERE CPF = '$NR_CPF_CGC' ";
        $id_pes_pessoa = $db->banco_query($qp, 'unico'); 
        $qm = " select id from  pes_endereco where id_pes_pessoa = $id_pes_pessoa ";
        $id_pes_endereco  = $db->banco_query($qm, 'unico');
        $reg_fornec = '';
        if ($id_pes_pessoa && $id_pes_endereco) {
           $reg_fornec    = insere_fornecedor($id_pes_pessoa, $id_pes_endereco);
           if ($saida == '1')  {
              $reg_fornec .= ';'.PHP_EOL; 
              fwrite($fpesf, $reg_fornec);
              $y++;
              if ($y > 100) { fwrite($fpesf, $com); $y = 0; }
           } else { $e = $db->banco_query($reg_fornec,'sql');  $i++; }   
//           $resp->alert('Aqui '.$e.'-'.$reg_fornec); return $resp;
        } 
        $p = floor($a*100/$total);   //Progress
        if ($p > 0 && $p > $z) {  $z = $p; send_message($p . '% . Regs.: '.$a, $p, $ind); }
      } 
      if ($saida == '1')  {
         fwrite($fpesf, $com);
         fclose($fpesf);
      }
      send_message('Encerrado! '.$total.' Registros.', 0,  $ind);
      exit;
}

function insere_fornecedor($id_pes_pessoa, $id_pes_endereco) {
    $sql = " insert into pes_fornecedor  (id , id_pes_pessoa, id_pes_endereco, TXT_OBSERVACAO, nr_versao, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_fornecedor a), $id_pes_pessoa, $id_pes_endereco, null, 0, current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
}

 function monta_endereco($end)  {
    $num    = numero($end);
    $end1   = substr($end,0, ($num -1));
    $resto  = substr($end, $num);
    $fn     = strpos($resto, ' ');
    $end2   = substr($resto, 0, $fn);
    $bairro = substr($resto, (strlen($end2)));
 //   $resp->alert($pes['ID'].' Rua:  '.$end1.'  Numero:  '.$end2.' -  Bairro:  '.$bairro.'    -    '.$pes['ENDER'].'</p>');
     return array($end1, $end2, $bairro);
 }

 function monta_endereco_rodovia($end)  {
    $num    = numero($end);
    $end1   = substr($end,0, ($num -1));
    $resto  = substr($end, $num);
    $fn     = strpos($resto, ' ');
    $end2   = substr($resto, 0, $fn);
    $bairro = substr($resto, (strlen($end2)));
 //   $resp->alert($pes['ID'].' Rua:  '.$end1.'  Numero:  '.$end2.' -  Bairro:  '.$bairro.'    -    '.$pes['ENDER'].'</p>');
     return array($end1, $end2, $bairro);
 }


function numero($string) {
  $count = strlen($string);
  $i = 0;
  while( $i < $count ) {
     if( ctype_digit($string[$i]) ) {
        return $i;
     }
     $i++;
  }
  return $i;
}
