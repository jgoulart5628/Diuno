<?php
/*
--
-- Table structure for table `ERRO_SQL`
--
DROP TABLE IF EXISTS `ERRO_SQL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
/*
CREATE TABLE `ERRO_SQL` (
  `chave` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cliente` varchar(30) COLLATE utf8_bin NOT NULL,
  `data_exe` datetime DEFAULT NULL,
  `erro` int(10) NOT NULL DEFAULT '0',
  `msg` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `sql_exe` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`chave`),
  KEY `banco_cliente` (`banco_cliente`,`data_exe`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
*/
// cadastro e manutenção de Pessoas
//error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
error_reporting(E_ALL & ~(E_NOTICE));
ini_set('log_errors', 1);
error_log("php_errors.log");
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set('default_charset','UTF-8');

//;; error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
// error_reporting(E_ALL);
ini_set('display_errors', true);
// ini_set('default_charset', 'iso-8859-1');
include("tabs_conv_class.php");
include("parametro_class.php");
$db = new Converte_model('MYSQL_nucleo');
//include("inc/banco_pdo_class.php");
//$db = new banco_dados('MYSQL_nucleo');
// var_dump($db);
if($db->erro)  { echo $db->erro.'OPA';  }
require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
$xajax->configure( 'errorHandler', true );
$xajax->configure( 'logFile', 'xajax_error_log.log' );
$xajax->register(XAJAX_FUNCTION,"Tela");
$xajax->register(XAJAX_FUNCTION,"Atualiza");
$xajax->register(XAJAX_FUNCTION,"Refresh");
$xajax->register(XAJAX_FUNCTION,"LIMPA");
$xajax->register(XAJAX_FUNCTION,"Exclui");
// $xajax->configure('debug',true);
$xajax->processRequest();
$xajax->configure('javascript URI','xajax/');
?>
<!DOCTYPE html>
<html class="no-js" lang="pt-BR">
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
 <title> Erros SQL </title>
    <script src="js/jquery-1.8.1.js"></script>
	<!--[if lt IE 9]>
		<script src="js/respond.min.js"></script>
	<![endif]-->
       <script type="text/javascript" src="../js/bootstrap.js"></script>
  <?php $xajax->printJavascript('xajax'); ?>
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
   <script type="text/javaScript">xajax_Tela() </script>
     <div id="local" class="row"></div> 
 </form> 
</body>
</html>
<?php
 function Tela()   {
   $resp = new xajaxResponse();
   global $db;
   $query = "select chave, banco_cliente , data_exe , erro,  msg, sql_exe 
              from  erro_sql  order by  data_exe desc,  chave   limit 200 ";
   $res  = $db->banco_query($query,'array',$resp);
   $tela = '<button  onclick="xajax_Refresh(); return false">Refresh</button>
                <button  onclick="xajax_LIMPA(); return false">Limpar Tabela Erros</button>
               <table border="1" cellpadding="0"  width="99%">
                <thead>
                  <tr>
                   <th>Data</th>
                   <th>Erro</th>
                   <th>Msg</th>
                   <th>SQL</th>
                  </tr>
                 </thead>
              <tbody>';   
//   $resp->alert($query.'-'.count($res)); return $resp;           
   if (!empty($res))  {
    for($a = 0; $a < count($res); $a++)   {
       $chave    = $res[$a]['data_exe'];	
       $erro     = $res[$a]['erro'];
       $msg      = $res[$a]['msg'];
       $sql_exe  = $res[$a]['sql_exe'];
       $key      = $res[$a]['chave'];	
       if ($a == 0 || fmod($a, 2) == 0) { $classe =  'class="t_line1"'; } else { $classe =  'class="t_line2"'; } 
       $tela .= '<tr '.$classe.'><td>'.$chave.'</td>     
                  <td>'.$erro.'<input type="image" src="inc/lixeira.png" width="32" heigth="32" onclick="xajax_Exclui(\''.$key.'\'); return false;"></td>
                  <td>'.$msg.'</td>
                  <td>'.$sql_exe.'</td>
                </tr>';
     }   		  
   } 
   $tela .= '</tbody></table>';
   $resp->assign("local","innerHTML",$tela);
   return $resp;
}

function Exclui($key) {
   $resp = new xajaxResponse();
   global $db;
   $query = " delete from erro_sql where chave = '$key' ";
   $db->banco_query($query,'sql',$resp);
   $resp->script("xajax_Tela();");
   return $resp;
}

function LIMPA() {
   $resp = new xajaxResponse();
   global $db;
   $e = $db->banco_query(" delete from erro_sql  ",'sql',$resp);
   $resp->script("xajax_Tela();");
   return $resp;
}

function Refresh() {
   $resp = new xajaxResponse();
   $resp->script("xajax_Tela();");
   return $resp;
}
