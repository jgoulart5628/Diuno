<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
// error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors',true);
// include("inc/banco_pdo_class.php");
include("parametro_class.php");
include("tabs_conv_class.php");
include("manut_ddl_postgre.php");
include("manut_ddl_oracle.php");
include("manut_ddl_sqlserver.php");

// $my = new banco_dados('MYSQL_nucleo');
// $sy= new banco_dados('ODBC_semear');

require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
//$xajax->setCharEncoding('UTF-8');
// $xajax->configure('debug',true);
$xajax->configure( 'errorHandler', true );
$xajax->configure( 'logFile', 'xajax_error_log.log' );  
$xajax->register(XAJAX_FUNCTION,"Tela");
$xajax->register(XAJAX_FUNCTION,"Converter");
$xajax->register(XAJAX_FUNCTION,"Selec_Tipo_Conv");
$xajax->register(XAJAX_FUNCTION,"CRIA_TABELAS_ORACLE");
$xajax->register(XAJAX_FUNCTION,"CRIA_TABELAS_SQLSERVER");
$xajax->register(XAJAX_FUNCTION,"CRIA_TABELAS_POSTGRESQL");
$xajax->register(XAJAX_FUNCTION,"Barra");
//$xajax->configure('decodeUTF8Input',true);
$xajax->processRequest();
$xajax->configure('javascript URI','xajax/');
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
    <!--    <meta charset="iso-8859-1" -->
 <title> Projeto Nucleo </title>
	<!--[if lt IE 9]>
		<script src="js/respond.min.js"></script>
	<![endif]-->
 <link rel="shortcut icon" href="icon/favicon.ico">
 <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
 <link rel="stylesheet" type="text/css" href="css/main.css">
  <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
   <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
 <link rel="shortcut icon" href="icon/favicon.ico">
 <script type="text/javascript" src="js/modernizr.js"></script>
 <?php $xajax->printJavascript('xajax'); ?>
 <link rel="stylesheet" type="text/css" href="css/nucleo.css">
 <style type="text/css">
   th { text-align: center; }
   .bbc { width: 20px; }
   .esquerda  {  float: left;}
   .boxa  { border: 2px inset;}
 </style>
 <script type="text/javascript">
  function tabela() {  
     $(document).ready(function() {
       $('#clicli').dataTable();
     } );
    }
    $(function() { tabela();});  
 </script> 
</head>
<body>
  <form id="tela" name="tela" class="form" method="POST">
 <div class="container-fluid fundo">
    <div class="page-header">
        <h3 class="text-muted centro">Núcleo Sistemas <small> Rotinas de Migração de Bancos de Dados. </small></h3>
		<hr>
         <div id="tab_conver" class="centro row"></div>
    </div>
	<hr>
    <div class="row">
      <div class="col-md-12 marketing">
      <div id="saida" class=col-lg-12>
            <h4>Comparar Bases </h4>
            <p>Este programa compara existencia de tabelas no Destino.</p>
            <h4>Origem:</h4>
            <p>As bases de origem podem ser Mysql Sybase(ASA) e Firebird.</p>
            <h4>Destino</h4>
            <p>As bases de Destino podem ser ORACLE, PostgreSQL ou SQLServer.</p>
       </div>
      </div> 
     </div>
    <div class=footer>
         <span class="glyphicon glyphicon-thumbs-up"></span>&#174; Jgoulart Web
		  	      <button class="btn btn-sm btn-warning roda" onclick="Saida('inc/empresa_banco.php');  return false;">  Parametros Conexão </button>
		  	      <button class="btn btn-sm btn-danger roda" onclick="Saida('mostra_erros_sql.php');  return false;">  Erros SQL   </button>
    </div>
   </div>    
  </form>
   <script type="text/javascript" src="js/nucleo.js"></script>
   <script type="text/javaScript" src="js/jquery-1.11.1.min.js" ></script>
   <script type="text/javaScript" src="js/jquery.dataTables.min.js" ></script>
   <script type="text/javaScript" src="js/bootstrap.min.js" ></script> 
   <script type="text/javaScript" src="js/dataTables.bootstrap.js" ></script> 
   <script type="text/javaScript">xajax_Tela('') </script>
 </body>
</html>
<?php
 function Tela()   {
//       <div id="saida"><iframe src="#" name="conteudo" style="width:99%; height:99%;"></iframe></div> 
    $resp = new xajaxResponse();
    $sqlite = new Parametro_model();
    $empresas  = $sqlite->Lista_empresas();
    $tela = combo_empresas($empresas);
//	$tela .= ' <button class="btn btn-sm btn-danger padrao" onclick="Saida(\'erro_sql.php\');  return false;">  Erros SQL   </button>';
    $resp->assign("tab_conver","innerHTML", $tela);
     return $resp;
 }
 
 function combo_empresas($empresas)  {
//    $resp->alert(print_r($sqlite,true)); return $resp;
//    $arq = 'erro_sql.php?arq='.$saida;
   $tela  = '<div class="col-md-2">Cliente : <select class="entra" style="width: 110px;"  name="empresa" id="empresa" onchange="xajax_Selec_Tipo_Conv(xajax.getFormValues(\'tela\')); return false;"> <option value ="" class="f_texto" ></option> ';
   if (count($empresas) > 0 )  {
        foreach($empresas as $emp) {
       if ($emp)  {    $tela .= '<option value="'.$emp.'"   class="f_texto"> '.$emp.' </option> '; }
      }     
    }
    $tela .= '</select></div> &nbsp; &nbsp; <div id="opc" class="col-md-2 boxa"></div>';
    $tela .=  '<div class="col-md-8">
                 <button class="btn btn-sm btn-success padrao" onclick="xajax_Converter(xajax.getFormValues(\'tela\')); return false;");">  Comparar Bases  </button>
               </div>';       
    return $tela; 
 }


 function Selec_Tipo_Conv($dados) {
     $resp     = new xajaxResponse();
     $empresa  = $dados['empresa'];
     $sqlite = new Parametro_model();
     $dbms   = $sqlite->Lista_DBMS($empresa);
     $entra  = '';
     $saida  = '';
     $conv   = ''; 
     for ($a = 0; $a < count($dbms); $a++)  {
         $db = $dbms[$a]['dbms'].'_'.$empresa; 
         $tp = $dbms[$a]['es']; 
         if ($tp === 'E')  { $entra = $db; } 
         if ($tp === 'S')  { $saida = $db; } 
     }
     if ((substr($entra,0,4) === 'ODBC') && (substr($saida,0,4) === 'ORAC'))  { $conv = '1'; $origem = 'Sybase'; $destino  = 'Oracle'; }
     if ((substr($entra,0,4) === 'ODBC') && (substr($saida,0,4) === 'SQLS'))  { $conv = '2'; $origem = 'Sybase'; $destino  = 'SQLServer';}
     if ((substr($entra,0,4) === 'FIRE') && (substr($saida,0,4) === 'ORAC'))  { $conv = '3'; $origem = 'Firebird'; $destino = 'Oracle';}
     if ((substr($entra,0,4) === 'FIRE') && (substr($saida,0,4) === 'SQLS'))  { $conv = '4'; $origem = 'Firebird'; $destino = 'Sqlserver';}
     if ((substr($entra,0,4) === 'FIRE') && (substr($saida,0,4) === 'POST'))  { $conv = '5'; $origem = 'Firebird'; $destino = 'PostgreSQL';}
     if ((substr($entra,0,4) === 'MYSQ') && (substr($saida,0,4) === 'POST'))  { $conv = '6'; $origem = 'Mysql'; $destino = 'PostgreSQL';}
     $tela  =  '<h4> '.$origem.' para '.$destino.' </h4> 
                  <input type="hidden" name="conv" value="'.$conv.'">
                  <input type="hidden" name="entra" value="'.$entra.'">
                  <input type="hidden" name="saida" value="'.$saida.'">
                  <input type="hidden" name="origem" value="'.$origem.'">
                  <input type="hidden" name="destino" value="'.$destino.'">';
//     $resp->alert($cliente.'-'.$entra.'-'.$saida.'-'.$orig_destino); 
     $resp->assign("opc","innerHTML", $tela);              
     return $resp; 
 }


 function Converter($dados) {
 	   $resp     = new xajaxResponse();
     $conv     = $dados['conv'];
	   $cliente  = $dados['empresa'];
     $entra    = $dados['entra'];
     $saida    = $dados['saida'];
     $origem   = $dados['origem'];
     $destino  = $dados['destino'];

  	 if (!$cliente)  {   $resp->alert("Escolha a Empresa ");  return $resp; }
	 // testar as base de dados
       // global $en;	 
     $en  = new InOut_model($entra);
	   if ($en->error)  {  $resp->alert('Entrada: '.$en->error);  return $resp;  } 
	   $sai   = new InOut_model($saida);
	   if ($sai->error)  {  $resp->alert('Saida: '.$sai->error);   return $resp;  } 

//     $db = new Converte_model($tabs_conv);
//     if ($db->error)  {  $resp->alert('Falta Registro MYSQL: '.$db->error);   return $resp; }
     $result = $en->Lista_Tabelas_Orig($conv,$resp); 
     if (empty($result))  { $resp->alert("Sem registros na leitura da tabela de entrada ");  return $resp; }
     foreach($result as $tb)  {
        $tabela = trim($tb['name']);
        if (!$tabela) {
           $tabela = trim($tb['NAME']); 
        }   
        $tabx = $tabela;
        $existe = $sai->Verifica_Destino($tabx, $conv,$resp);
        if (!$existe)  { $lista[] = $tabela; }
     }  

//     $resp->alert(print_r($lista,true).'-'.$entra.' - '.$saida.'-'.$conv); return $resp; 
    $a = 0;
   $tela .= '<table id="clicli" data-toggle="table" class="table table-striped table-bordered"  data-sort-name="Tabela" data-sort-order="desc">
	               <caption>Total tabelas faltantes : '.count($lista).'</caption>
                   <thead>                  
                    <tr>
       			         <th data-field="seq"      data-sortable="true">Seq.</th>
        						 <th data-field="tabela"   data-sortable="true">Tabela</th>
				            </tr>
                  </thead>';
// 	var_dump($tabs);
    if (count($lista) > 0)  {
      foreach($lista as $arq)  {
		   	if ($a == 0 || fmod($a, 2) == 0) { $classe =  'class="t_line1"'; } else { $classe =  'class="t_line2"'; } 
   			$cc = '';
	      $tela .= '<tr '.$classe.'>'
                    . '<td data-field="seq" data-sortable="true">'.($a + 1).'</td>'
                    . '<td data-field="tabela" data-sortable="true">'.$arq.'<input type="button" '.$cc.' id="botao_carrega_'.$a.'" style="float: right;" value="Criar" onclick="xajax_Barra(\''.$arq.'\',\''.$a.'\',\''.$conv.'\' ,\''.$entra.'\',\''.$saida.'\'); return true;">'
                    . '<div id="erro_'.$a.'" class="contar">';
         $a++;
	    }
     }    	
     $tela .= '</table>';
     $resp->assign("saida","innerHTML", $tela);
     $resp->script('tabela()');
 	return $resp;
 }

function Barra($tabela, $a, $conv, $entra, $saida)   {
     $resp = new xajaxResponse();
     if ($conv === '1' || $conv === '3')  {
        $conver = "xajax_CRIA_TABELAS_ORACLE('$tabela','$conv', '$entra', '$saida', '$a')";
     }
     if ($conv === '2' || $conv === '4')  {
        $conver = "xajax_CRIA_TABELAS_SQLSERVER('$tabela','$conv', '$entra', '$saida', '$a')";
     }
     if ($conv === '6' )  {
        $conver = "xajax_CRIA_TABELAS_POSTGRESQL('$tabela','$conv', '$entra', '$saida', '$a')";
     }
     $tela = '
         <div id="progressor_'.$a.'" style="background:#07c; width:0%; height:100%;"></div>
         <div id="mostra_erros"></div>';
    $resp->assign("erro_$a","innerHTML",$tela);
    $resp->script($conver);  
    return $resp;
   }


function send_message($message, $ind=0) {
    $d = array('message' => $message , 'ind' => $ind);
     echo json_encode($d) . PHP_EOL;
    
    //PUSH THE data out by all FORCE POSSIBLE
    ob_flush();
    flush();
}
