<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//  error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','iso-8859-1');
ini_set('display_errors', true);
$programa = basename(__FILE__); 
include("inc/banco_pdo_class.php");
$db = new banco_dados('ORACLE_gicof');
$arq = realpath('migra/mater/').'\\MATERIAIS.txt';
$arqsai = realpath('Scripts_Oracle/').'\\materiais.sql';
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
    $z = 0;
    $y = 0;
     while (!feof($fh))  {
        $mat  = fgets($fh, 1024);
        $mat  = str_replace("'"," ", $mat); 
        $mat  = str_replace('"'," ", $mat); 
        $mat  = str_replace(","," ", $mat); 
        $mat  = str_replace("&","e", $mat); 
         
//        $mun = mb_convert_encoding($mun, "Windows-1252", "UTF-8");
        // cria colunas
        $sai = explode(';', $mat);
//        echo $mat;
        if ($x == 0) { 
           $sqx = " insert into mater (";
           $a = 0;
           foreach ($sai as $col) {
              $col = str_replace('"', '', $col);
              $sqx .=  $col;
              if ($a === (count($sai) - 1)) { $sqx .= ''; } else { $sqx .= ', '; }
              $a++;
           }
           $sqx .= ') values(';
//            echo ($sqx); 
        } 

        if ($x > 0)  { 
           $CD_MATERIAL       = $sai[0];
           $DS_MATERIAL_PADR  = $sai[1];
           $DS_MATERIAL_COMPL = $sai[2];
           $IN_UNIDADE        = $sai[3];
           $VL_MATERIAL       = $sai[4];
           $ST_MATERIAL       = $sai[5];
           $TP_ITEM           = $sai[6];
           if ($CD_MATERIAL) {
              $query = "select count(*) conta from mater where cd_material = $CD_MATERIAL ";
              $conta = $db->banco_query($query, 'unico');
              if ($conta == 0)  { 
                  $sql = $sqx." $CD_MATERIAL , '$DS_MATERIAL_PADR', '$DS_MATERIAL_COMPL', '$IN_UNIDADE', '$VL_MATERIAL', $ST_MATERIAL, $TP_ITEM) ;".PHP_EOL;
                 echo($sql.'</p>'); 
                 fwrite($fs, $sql);
                 $y++;
                 $z++;
              if ($y > 100) { $com = 'commit;'.PHP_EOL; fwrite($fs, $com); $y = 0; }
           } 
        }
      }
      $x++;
    }
//   }
    fclose($fh);
    fclose($fs);
    echo '<h1>Encerrado! '.$z.' Registros a incluir.</h1>'; 
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
