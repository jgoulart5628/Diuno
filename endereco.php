<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//  error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors', true);
$cfg = parse_ini_file("inc/imigra.ini",true);
// print_r($conver);
$programa = basename(__FILE__); 
include("inc/banco_pdo_class.php");
if (isset($_GET['banco'])) {
  $db = new banco_dados($_GET['banco']);
 }
$query = " alter session set current_schema=".$_GET['schema'];
$db->banco_query($query, 'sql');
// var_dump($db);
// echo $query;
// exit;
// $db = new banco_dados('ORACLE_gicof');
// $sy= new banco_dados('ODBC_semear');
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
    <!--    <meta charset="iso-8859-1" -->
 <title> Projeto Migração </title>
	<!--[if lt IE 9]>
		<script src="js/respond.min.js"></script>
	<![endif]-->
 <link rel="icon" type="image/ico" href="favicon.ico">
 <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
 <link rel="stylesheet" type="text/css" href="css/main.css">
  <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
   <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
 <script type="text/javascript" src="js/modernizr.js"></script>
 <link rel="stylesheet" type="text/css" href="css/nucleo.css">
</head>
<body>
  <form id="tela" name="tela" class="form" method="POST">
  <div class="container-fluid">
     <div class="text-muted centro topo"><img src="img/nucleo.gif">
          Projeto Diuno <small> Rotinas de Migração de Bancos de Dados. </small>
     </div>
     <hr>
    <div id="tab_menu" class="centro row fundo"></div>
    <div class="row fundo">
      <div class="col-md-12 marketing">
      <div id="saida" class=col-lg-12>
            <h4>Conversão Dados </h4>
            <p>Este programa converte os dados da tabela FORNEC parag gerar tabela PES_ENDERECO  na rotina de migração de dados do GICOF</p>
            <?php $err = endereco();  echo $err.'-'; ?>
       </div>
      </div>
    </div>
    <div class="footer fundo rodape">
         <span class="glyphicon glyphicon-thumbs-up"></span>&#174; Jgoulart Web
		  	      <button class="btn btn-sm btn-warning roda" onclick="Saida('empresa_banco_new.php');  return false;">  Parametros Conexão </button>
    </div>
   </div>
  </form>
 </body>
</html>
<?php
function endereco() {
     global $db;
     $query = " select * from fornec ";
     //a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b) ";
     $pes = $db->banco_query($query, 'array');
     $err = 0;
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
           $e = $db->banco_query($reg_ender, 'sql');  
           if ($e == 2) { $err++; } else  {  $x++; }
         }  
//           fwrite($flogra, $reg_logradouro);
        } else { echo ('Erro! : '.$reg_ender.' - '.$id_pes_municipio.' - '.$id_pes_logradouro.' - '.$id_pes_pessoa.$qp.'/'.$ql.'/'.$qm.'</p>'); // break;  
        }
      } 
     return $err;
}

function insere_endereco($id_pes_pessoa, $id_pes_municipio, $id_pes_logradouro) {
    $sql = " insert into pes_endereco  (id , id_pes_pessoa, id_pes_logradouro, nr_imovel, dt_ativo_de, tp_endereco, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_endereco a), $id_pes_pessoa, $id_pes_logradouro, 0, current_timestamp, 3, current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
}

/*
          /*
//          if (substr($TX_ENDERECO, 0, 7) == 'RODOVIA') {
//           $lista  = monta_endereco_rodovia($TX_ENDERECO);
//          } else { $lista  = monta_endereco($TX_ENDERECO); }
//           $rua    = $lista[0];
//           $numero = $lista[1];
 //          $bairro = $lista[2];
 //       $id_pes_bairro_distrito = Insere_Bairro_Distrito($bairro, $id_pes_municipio, $resp);
CREATE TABLE "GLOBALGOV_PESS_DEV"."PES_ENDERECO" 
   (  "ID" NUMBER(18,0) NOT NULL ENABLE, 
  "ID_PES_PESSOA" NUMBER(18,0) NOT NULL ENABLE, 
  "ID_PES_LOGRADOURO" NUMBER(18,0) NOT NULL ENABLE, 
  "NR_IMOVEL" NUMBER(10,0), 
  "DT_ATIVO_DE" current_TIMESTAMP, 
  "TP_ENDERECO" 3 
  "DT_CRIADO_EM" current_TIMESTAMP 
  "COD_USU_CRIADO" 1 
  */



/*

"ID" NUMBER(18,0) NOT NULL ENABLE, 
  "ID_PES_MUNICIPIO" NUMBER(18,0) NOT NULL ENABLE, 
  "ID_PES_TIPO_LOGRADOURO" NUMBER(18,0) NOT NULL ENABLE, 
  "ID_PES_BAIRRO_DISTRITO" NUMBER(18,0), 
  "NM_LOGRADOURO" VARCHAR2(250) NOT NULL ENABLE, 
  "NM_COMPLEMENTO" VARCHAR2(250), 
  "CEP_LOGRADOURO" CHAR(8), 
  "NR_VERSAO" NUMBER(10,0), 
  "DT_CRIADO_EM" TIMESTAMP (6) NOT NULL ENABLE, 
  "COD_USU_CRIADO" NUMBER(18,0) NOT NULL ENABLE, 
  "DT_ALTERADO_EM" TIMESTAMP (6), 
  "COD_USU_ALTERADO" NUMBER(18,0), 
   CONSTRAINT "PES_LOGR_PES_MUN_TLOGR_NM_UK" UNIQUE ("ID_PES_MUNICIPIO", "NM_LOGRADOURO", "NM_COMPLEMENTO", "CEP_LOGRADOURO")
 *          if ($y1 > 100) { fwrite($fpess, $com); $y1 = 0; 

select REGEXP_SUBSTR(tx_endereco,'[^ ]+',1,1) from fornec

select table_name  from  all_constraints
where   r_constraint_name in
    (select   constraint_name from  all_constraints
    where   table_name='PES_PESSOA')
PES_ENDERECO
PES_FORNECEDOR
PES_CONTATO
PES_PESSOA_FISICA
PES_PESSOA_JURIDICA
PES_PESSOA_JURIDICA
PES_PJ_SOCIO
*/

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
