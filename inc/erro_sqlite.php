<?php
/*  Tabela de erros SQL
// CREATE TABLE "ERRO_SQL" 
        ("key" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL ,
		 "data" DATETIME,
 		 "erro" INTEGER, 
		 "msg" VARCHAR,
		 "sql" VARCHAR);
*/
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
ini_set('display_errors', true);
ini_set('default_charset', 'iso-8859-1');
//include("banco_pdo_class.php");
//$db = new dataBase('LOGS');
$cf    = parse_ini_file("banco.ini",true); 
$db   = $cf['LOGS']['db'];
$conn = new PDO("sqlite:".$db) or ("<h1><font color='red'>Não conectou banco de dados, LOFS_SQL Help. '</font></h1>");
// var_dump($res);
require_once("../xajax05/xajax_core/xajax.inc.php");
$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
// $xajax->setCharEncoding('UTF-8');
$xajax->configure('javascript URI','xajax05/');
$xajax->registerFunction("Tela");
$xajax->registerFunction("Atualiza");
$xajax->registerFunction("Refresh");
$xajax->registerFunction("LIMPA");
$xajax->registerFunction("Exclui");
$xajax->configure('decodeUTF8Input',true);
// $xajax->configure('debug',true);
$xajax->processRequest();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <head>
       <meta charset="utf-8">
       <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--    <meta charset="iso-8859-1" -->
        <title> Erros SQL </title>
         
        <script src="../js/jquery-1.8.1.js"></script>
	<!--[if lt IE 9]>
		<script src="js/respond.min.js"></script>
	<![endif]-->
       <script type="text/javascript" src="../js/bootstrap.js"></script>
<?php $xajax->printJavascript('../xajax05'); ?>
  <style>
   table       { margin: 0 auto;  border-collapse: collapse; font-family: Verdana, Times, serif;  } 
  .t_line1      {  background-color: #FFFFFF;  }
  .t_line2      {  background-color: #CCCCCC;  }
  .linha  { text-align: right;  }
  .contar { background-color: red; border: 2px outset; }
  .verde  { background-color: green; }
</style>
</head>
<body>
 <form name="tela" action="post"> 
   <script type="text/javaScript">xajax_Tela('') </script>
     <div id="local" class="row"></div> 
 </form> 
</body>
</html>
<?php
 function Tela()   {
   $resp = new xajaxResponse();
   global $conn;
   $query = "select key , data, erro, msg, sql  from ERRO_SQL order by key, data desc ";
   $sq = Executa($query,$conn);
   $res  = $sq->fetchAll();
   $tela = '<button  onclick="xajax_Refresh(); return false">Refresh</button>
                <button  onclick="xajax_LIMPA(); return false">Limpar Tabela Erros</button>
               <table border="1" cellpadding="0"  width="99%">
                <thead><tr><th>Data</th><th>Erro</th><th>Msg</th><th>SQL</th></tr></thead>
              <tbody>';   
   if (count($res) > 0)  {
   	  $a = 0;
       foreach($res as $list) {
       $chave = $list['data'];	
	    $key = $list['key'];	
       if ($a == 0 || fmod($a, 2) == 0) { $classe =  'class="t_line1"'; } else { $classe =  'class="t_line2"'; } 
        $tela .= '<tr '.$classe.'><td>'.$chave.'</td>     
                  <td>'.$list['erro'].'<input type="image" src="lixeira.png" width="32" heigth="32" onclick="xajax_Exclui(\''.$key.'\'); return false;"></td>
                  <td>'.$list['msg'].'</td>
                  <td>'.$list['sql'].'</td>
           </tr>';
         $a++;              
     }   		  
   } 
   $tela .= '</tbody></table>';
   $resp->assign("local","innerHTML",$tela);
   return $resp;
}

function Executa($query,$conn) {
   if ($conn) {
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sq = $conn->prepare($query);
      $sq->execute();
   }
   return $sq; 
}   

function Exclui($chave) {
   $resp = new xajaxResponse();
   global $conn;
   $query = " delete from ERRO_SQL where rowid = '$chave' ";
   $sq = Executa($query,$conn);
   $resp->script("xajax_Tela();");
   return $resp;
}

function LIMPA() {
   $resp = new xajaxResponse();
   global $conn;
   $query = " delete from ERRO_SQL  ";
   $sq = Executa($query,$conn);
   $resp->script("xajax_Tela();");
   return $resp;
}

function Refresh() {
   $resp = new xajaxResponse();
   $resp->script("xajax_Tela();");
   return $resp;
}
