<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//  error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
// ini_set('default_charset','iso-8859-1');
ini_set('display_errors', true);
$programa = basename(__FILE__); 
include("inc/banco_pdo_class.php");
//if (isset($_GET['banco'])) {
//  $db = new banco_dados($_GET['banco']);
// }
$db = new banco_dados('ORACLE_gicof_desenv');
$arq = realpath('Scripts_Oracle/').'\\Municipios.csv';
$arqsai = realpath('Scripts_Oracle/').'\\Municipios.sql';
if (!file_exists($arq)) { echo('Arquivo '.$arq.' Não existe!'); exit; }
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <!--meta charset=utf-8-->
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--meta charset="iso-8859-1" -->
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
    $fs = fopen($arqsai, "w+");
    $fh = fopen($arq, "r");
    $x = 0;
     while (!feof($fh))  {
        $mun = fgets($fh, 80);
//        $mun = mb_convert_encoding($mun, "Windows-1252", "UTF-8");
        $sai = explode(';', $mun);
        $uf  = $sai[0];
        $coduf = $sai[1];
        $codmun = $sai[2];
        $nome   = str_replace("'"," ", $sai[3]); 
        $nome   = str_replace("*","", $nome); 
        $codibge = $coduf.$codmun;
        $sql = " update pes_municipio set NM_MUNICIPIO = '$nome' where cod_ibge = $codibge; ".PHP_EOL;
 //       $e = $db->banco_query($sql,'sql');
        echo $sql.'</p>';
        fwrite($fs, $sql);
        /*
        if ($codmun) {
          $query = " select id from pes_municipio where cod_ibge = $codibge ";
          $res = $db->banco_query($query, 'unico');
          if (!$res) {
             $x++;
             $id_es = $db->banco_query(" SELECT ID FROM PES_ESTADO WHERE SG_ESTADO = '$uf' ",'unico');
             $id = $db->banco_query("select nvl((max(a.id) + 1),1) from pes_municipio a ", 'unico');
             $sql = " insert into pes_municipio (ID, ID_PES_ESTADO, NM_MUNICIPIO, COD_IBGE, NR_VERSAO, DT_CRIADO_EM, COD_USU_CRIADO) values ($id, $id_es, '$nome', $codibge, 0, current_timestamp, 1) ;".PHP_EOL;
//             $e = $db->banco_query($sql,'sql');
             fwrite($fs, $sql);
             echo($x.' - '.$sql.'-'.$coduf.'-'.$codmun.'-'.$nome.'</p>');
//             if ($x > 5) { break; }
          }
        } 
        */    
     }
     $sql = " commit ";
     $e = $db->banco_query($sql,'sql');
    fclose($fh);
    fclose($fs);
    echo '<h1>Encerrado!</h1>'; 
/*
SELECT ID FROM PES_ESTADO WHERE SG_ESTADO = 'RS'

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
