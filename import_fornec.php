<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//  error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('mbstring.substitute_character', "none");
// ini_set('default_charset','iso-8859-1');
ini_set('display_errors', true);
$programa = basename(__FILE__); 
include("inc/banco_pdo_class.php");
$db = new banco_dados('ORACLE_gicof_local');
$arq = realpath('migra/fornec/').'\\FORNECEDORES.txt';
$arq = str_replace('\\','//', $arq);
$arqsai = realpath('Scripts_Oracle/').'\\fornec.sql';
//$arqsai = str_replace('\\','//', $arqsai);
if (!file_exists($arq)) { echo('Arquivo '.$arq.' Não existe!'); exit; }
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <!--! meta charset="iso-8859-1"-->
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
 <style type="text/css">
   th { text-align: center; }
   .bbc { width: 20px; }
   .esquerda  {  float: left;}
   .boxa  { font-size: 1.1em; font-weight: bold; color: red; }
   .barra { background-color: #78cbd1; border: 2px outset;}
   nav { margin-top: 4px; margin-bottom: 4px; }
   .rodape { height: 50px;}
 </style>
</head>
<body>
  <form id="tela" name="tela" class="form" method="POST">
  <div class="container-fluid">
     <div class="text-muted centro topo"><img src="img/nucleo.gif">
          Projeto Diuno <small> Rotinas de Migração de Bancos de Dados. </small>
     </div>    
     <hr>
     <?php
//    $fs = fopen($arqsai, "w+");
    $fh = fopen($arq, "r");
    $x = 0;
//    $z = 0;
//    $y = 0;
    $tit = array("NR_CPF_CGC","TP_FAVORECIDO_D","NM_FAVORECIDO","TX_ENDERECO","NR_TELEFONE",
    "TX_CIDADE", "NR_BANCO", "NR_AGENCIA", "SG_UF", "NR_CTA_CORRENTE", "NR_CEP", 
    "NR_INSCR_MUNICIPAL","NR_INSCR_ESTADUAL","TP_RAMO_ATIV_D", "IN_SUSPENSAO_D" , 
    "UG_CD_UG_SUSPEN","IN_COLETIVO_D","IN_EVENTUAL_D","ST_ATIVO", "DT_DESATIVACAO", 
    "NR_AGENCIA_DIR","NR_BANCO_DIR", "NR_CTA_CORRENTE_DIR", "CD_ORIGEM", 
    "TP_NAT_JURIDICA","FL_ZONAFRANCASOCIAL");
    $tot_cols = count($tit);
    $sqx = " insert into fornec values(";
    while (!feof($fh))  {
        $sql = '';
        $col = '';
        $mat  = fgets($fh, 2048);
        $mat  = str_replace("'"," ", $mat); 
        $mat  = str_replace('"'," ", $mat); 
        $mat  = str_replace(","," ", $mat); 
        $mt   = str_replace("&","e", $mat); 
        $mat  = preg_replace('/[[:^print:]]/', "", $mt);
        $sai = explode(';', $mat);
        $tt = count($sai);
        if ($x > 0)  { 
           $a = 1;
          if ($sai[0]) { 
           foreach ($sai as $dado)  {
             if ($a < $tt)  {
                if ($a == 1) {
                    $col .= " '$dado' "; 
               } else { 
                  if ($a  == 20) { $col .= ', TO_DATE(\''.$dado.'\', \'dd/mm/yyyy hh24:mi:ss\')'; }
                  else { $col .= ", '$dado' "; }
               }
               $a++;
               }
           }
//              $conta = $db->banco_query($query, 'unico');
           $sql = $sqx.$col.')'; // ;'.PHP_EOL;
           $db->banco_query($sql,'sql');
  //         fwrite($fs, $sql);
  //         $y++;
  //         $z++;
         //  if ($y > 100) { $com = 'commit;'.PHP_EOL; fwrite($fs, $com); $y = 0; }
           echo($x.'-');  // exit;
//           if ($x > 20) { exit; }
        }
       }
        $x++;
     }
    fclose($fh);
  //  fclose($fs);
  //  echo '<h1>Encerrado! '.$z.' Registros a incluir.</h1>'; 
/*

/*           $sqx = " insert into fornec (";
         
//        $mun = mb_convert_encoding($mun, "Windows-1252", "UTF-8");
           $a = 0;
           foreach ($tit as $coluna) {
           $coluna = str_replace('"', '', $coluna);
              $sqx .=  $coluna;
              if ($a === (count($tit) - 1)) { $sqx .= ''; } else { $sqx .= ', '; }
              $a++;
           }
           $sqx .= ') values(';

SELECT ID FROM PES_ESTADO WHERE SG_ESTADO = 'RS'
TO_DATE('2003/05/03 21:02:44', 'yyyy/mm/dd hh24:mi:ss')); 
SELECT PES_MUNICIPIO_SEQ.NEXTVAL FROM DUAL

    CREATE TABLE "JGOULART"."PES_MUNICIPIO" 
   (  "ID" NUMBER(18,0) NOT NULL ENABLE, 
  "ID_PES_ESTADO" NUMBER(18,0) NOT NULL ENABLE, 
  "NM_MUNICIPIO" VARCHAR2(250 BYTE) NOT NULL ENABLE, 
  "COD_IBGE" VARCHAR2(7 BYTE), 
  "NR_VERSAO" NUMBER(10,0), 
  "DT_CRIADO_EM" TIMESTAMP (6) NOT NULL ENABLE, 
  "COD_USU_CRIADO" NUMBER(18,0) NOT NULL ENABLE, 
  "DT_ALTERADO_EM" TIMESTAMP (6), 
  "COD_USU_ALTERADO" NUMBER(18,0), 
  */
     ?>
    </div> 
  </form>
 </body>
</html>
