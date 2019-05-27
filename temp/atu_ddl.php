<?php
/*
declare cursor ana is
select 'analyze table ' || table_name || ' compute statistics '  analisa from user_tables;
begin
   for ana_rec in ana
  loop
    execute immediate ana_rec.analisa;  
  end loop;
end;  
 * Prgrama principal de navegação dos dados
 */
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
// error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors',TRUE);
$dir = realpath('ALTERA_DDL/');
define("DIR", str_replace('\\','/',$dir));
define("PASTA", 'ALTERA_DDL/');
$dirx = realpath('SQL_INSERT/');
define("DIRX", str_replace('\\','/',$dirx));
define("PASTAX", 'SQL_INSERT/');
include("inc/banco_pdo_class.php");
// $sy= new banco_dados('ODBC_semear');
require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
//$xajax->setCharEncoding('UTF-8');
$xajax->register(XAJAX_FUNCTION,"Tela");
$xajax->register(XAJAX_FUNCTION,"Mostra_SQL");
$xajax->register(XAJAX_FUNCTION,"Selec_Cliente");
$xajax->register(XAJAX_FUNCTION,"Limpa_Tela");
$xajax->register(XAJAX_FUNCTION,"Executar");
$xajax->register(XAJAX_FUNCTION,"TESTAR");
$xajax->register(XAJAX_FUNCTION,"Carga");
$xajax->register(XAJAX_FUNCTION,"Excluir");
//$xajax->configure('decodeUTF8Input',true);
// $xajax->configure('debug',true);
$xajax->processRequest();
$xajax->configure('javascript URI','xajax/');
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--    <meta charset="iso-8859-1" -->
 <title> Atualiza DDL  </title>
	<!--[if lt IE 9]>
		<script src="js/respond.min.js"></script>
	<![endif]-->
 <link rel="shortcut icon" href=dist/6df2b309.favicon.ico>
 <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
 <link rel=stylesheet href=dist/styles/444ed944.main.css>
 <script src="dist/scripts/vendor/fbe20327.modernizr.js"></script>
   
  <?php $xajax->printJavascript('xajax05'); ?>

 <script type="text/javascript" src="js/nucleo.js"></script>

 <link rel="stylesheet" type="text/css" href="css/nucleo.css">
 <style type="text/css">
 #sidebar a:hover, a:focus {  text-decoration: none;  }
 #sidebar.nav > li > a:hover,
 #sidebar.nav > li > a:focus {
    background-color: #e4f5fc;
    color: #999;  }
 .esquerda { float: left; }   
 .botao_excluir { background-color: white; }   
</style>
</head>
<body>
  <form id="tela" name="tela" class="form" method="POST">
  <script type="text/javaScript">xajax_Tela('') </script>
  <div class="container-fluid fundo">
    <div class="page-header">
        <h3 class="text-muted centro">Núcleo Sistemas <small> Rotinas de Atualização DDL. </small></h3>
    </div>
    <div class="row">    
      <div id="DBMS" class="col-md-2"></div>
      <div id="CLIENTE" class="col-md-2"></div>
      <div class="col-md-2"><input type="button" class="btn-default" value="Testar Conexão" onclick="xajax_TESTAR(xajax.getFormValues('tela')); return true;"></div>
    <hr>
    </div>
    <div class="row">    
           <div id="tab_conv" class="col-md-5"></div>
    </div>
    <div class="row">    
           <div id="tab_conver" class="col-md-5"></div>
           <div id="texto_sql" class="col-md-7"></div> 
    </div>
    <div class=footer>
         <span class="glyphicon glyphicon-thumbs-up"></span>&#174; Jgoulart Web
 	      <button class="btn btn-sm btn-warning roda" onclick="Saida('inc/empresa_banco.php');  return false;">  Parametros Conexão </button>
 	      <button class="btn btn-sm btn-danger roda" onclick="Saida('erro_sql.php');  return false;">  Erros SQL   </button>
    </div>
   </div>    
  </form>
  <script src="js/jquery.js"></script>
  <script src="js/bootstrap.js"></script>
 </body>
</html>
<?php
 function Tela()   {
    $resp = new xajaxResponse();
    $tela  = combo_dbms();
    $telax =  '<div><h4>Scripts da pasta </h4>
                 <div class="col-md-4"><input type="radio" name="pasta" value="'.PASTA.'" checked="checked" onclick="xajax_Limpa_Tela();">&nbsp;<b>ALTERA_DDL</b></div>        
                 &nbsp;&nbsp;&nbsp;<div class="col-md-4">&nbsp;<input type="radio" name="pasta" value="'.PASTAX.'" onclick="xajax_Limpa_Tela();">&nbsp;<b>SQL_INSERT</b></div>
                 <div class="col-md-2"><input type="button" class="btn-default" value="Carregar Scripts" onclick="xajax_Carga(xajax.getFormValues(\'tela\')); return true;"></div>
               </div>';           
//    $telax = busca_dados(PASTA);     
    $resp->assign("DBMS","innerHTML", $tela);
    $resp->assign("tab_conver","innerHTML", '');
    $resp->assign("tab_conv","innerHTML", $telax);
    return $resp;
 }
 
 function Limpa_Tela()   {
    $resp = new xajaxResponse();
    $resp->assign("tab_conver","innerHTML", '');
    $resp->assign("texto_sql","innerHTML", '');
    return $resp;
 }

 function combo_dbms($dbms='')  {
     $dbs = array('ORACLE', 'SYBASE','SQLSERVER','FIREBIRD','MYSQL','ODBC','SQLITE','POSTGRESQL');
     $tela  = 'DBMS : <select class="entra" style="width: 110px;"  name="dbms" id="dbms" onchange="xajax_Selec_Cliente(xajax.getFormValues(\'tela\')); return true;">
              <option value ="" class="f_texto" ></option> ';
     foreach($dbs as $db) {
        if ($db === $dbms)  {  $sel = ' selected '; } else { $sel = ''; }
        $tela .= '<option value="'.$db.'"  '.$sel.' class="f_texto" > '.$db.' </option> '; 
     }     
     $tela .= '</select> &nbsp; &nbsp;&nbsp; '; 
     return $tela;
   }  

 function Selec_Cliente($dados)   {
//       <div id="saida"><iframe src="#" name="conteudo" style="width:99%; height:99%;"></iframe></div> 
    $resp = new xajaxResponse();
    $dbms = $dados['dbms'];
    $db = conecta('inc/parametro.sqlite');
    if (!is_object($db))  { $resp->alert("Não conectou banco de dados"); return $resp; }
    $query = "SELECT distinct id FROM empresa_banco  where dbms = '$dbms' order by id "; 
    $sql = executa($db,$query);
    $res = $sql->fetchAll(PDO::FETCH_ASSOC);
    $tela  = 'Cliente : <select class="entra" style="width: 110px;"  name="cliente" id="cliente">
              <option value ="" class="f_texto" ></option> ';
     foreach($res as $cl) {
        $cli = $cl['id'];
        $tela .= '<option value="'.$cli.'"  class="f_texto" > '.$cli.' </option> '; 
     }     
    $tela .= '</select> &nbsp; &nbsp;&nbsp; '; 
//    $resp->alert($dbms); 
    $resp->assign("CLIENTE","innerHTML", $tela);
    return $resp;
 }   
 
 function Carga($dados) { 
   $resp = new xajaxResponse();
   $dbms     = $dados['dbms'];
   $cliente  = $dados['cliente'];
   $pastax   = $dados['pasta'];
   if (!$dbms || !$cliente)  { $resp->alert("Escolha um DBMS e um cliente!"); return $resp; }
   if (substr($pastax,0,3) === 'SQL') {  $pasta = $pastax.$cliente.'/'; } else { $pasta = $pastax; }
   $tela = '<div id="arq_sql" class="row">';
   foreach (new DirectoryIterator($pasta) as $fileInfo) {
     if($fileInfo->isDot()) continue;
     if($fileInfo->isDir()) { $pasta1 = $pasta.$fileInfo->getFilename(); continue; }
     $file[]  = $fileInfo->getFilename();
   }
   $tela .= '<div id="pendente"><h4>Scripts da pasta '.$pasta.'</h4>
               <ul id="sidebar" class="nav nav-pills nav-stacked" style="max-width: 320px; margin-left: 5px;">';
   if(count($file) > 0)  {
     foreach($file as $arq)  {
       $tela .= '<li class="active"><a href="#" style="text-align:left;" onmouseover="xajax_Mostra_SQL(\''.$pasta.'/'.$arq.'\'); return true;">'.$arq.'</a>
                 </li>';
     }
   } 
//    $resp->alert(print_r($file, true)); return $resp;
    $tela .= '</ul></div>';
 //   $file1 = array();
 //   foreach (new DirectoryIterator($pasta) as $fileInfo) {
 //     if($fileInfo->isDot()) continue;
 //     $file1[]  = $fileInfo->getFilename();
 //   }
//   $resp->alert(print_r($file1, true)); return $resp;
//   $tela .= '<div id="pronto">';
//   if(count($file1) > 0)  {
//     foreach($file1 as $arq)  {
//       $tela .= '<input type="button" name="sql_pronto" value="'.$arq.'"></br>';
//     }
//   }  
//   $tela .= '</div>';
   
   $tela .= '</div>';
   $resp->assign("tab_conver","innerHTML", $tela);
   return $resp;
}

 function Mostra_SQL($arq) {
  $resp     = new xajaxResponse();
  $file =  realpath($arq);
  if(!is_file($file)) { $resp->alert("Arquivo invalido, verifique: ".$arq); return $resp; }
  $tela = '<div class="form-group"> 
            <h3>SQL</h3></br>';
  $ll   = file($file);
  foreach ($ll as $linha)  { 
     $sql .= $linha;
  }   
//  $sql = 'Teste de SQL';
  $tela .= '<label for="textarea">'.$arq.'</label>
              </br><textarea name="sql" class="field span7" id="textarea" rows="6">'.$sql.'</textarea>
              </br><button class="btn btn-sm btn-success padrao" onclick="xajax_Executar(xajax.getFormValues(\'tela\')); return false;");"> Executar </button>
              <button class="btn btn-primary" onclick="xajax_Excluir(\''.$file.'\'); return false;">Excluir<span class="glyphicon glyphicon-trash"></span></div>';
//  $resp->alert($file.'-'.$lin);
  // $resp->alert($sql);
  $resp->assign("texto_sql","innerHTML",$tela);
  return  $resp; 
 } 

 function Excluir($file)  {
    $resp     = new xajaxResponse();
    if (is_file($file)) { unlink($file);  }  
    $telax = busca_dados(PASTA);     
    $resp->assign("tab_conver","innerHTML", $telax);
    return $resp;
 }

 function Executar($dados) {
  $resp     = new xajaxResponse();
  $sql      =   $dados['sql'];
  $ddl      = array('CREATE','ALTER', 'DROP','create','alter','drop');
  $op = 'array';
  foreach($ddl as $dd) {
    if (strpos($sql ,$dd) !== false) {
       $op  = 'sql'; //say NO!
    }
  }  
  $id            = $dados['cliente'];
  $dbms          = $dados['dbms'];
  $banco         = $dbms.'_'.$id;
  if (!$id || !$dbms)  { $resp->alert("Para executar escolha um DBMS!"); return $resp; }
  $con = new banco_dados($banco);
  if ($con->erro)  { $resp->alert($con->erro); } 
  $e   = $con->banco_query($sql, $op, '1'); 
  if ($con->erro)  {  $resp->alert($con->erro);  } 
  if ($op === 'array') {  $resp->alert(print_r($e,true)); }
  return $resp;
 }

 function conecta($banco) {
    $db = new PDO('sqlite:'.$banco);
    return $db;
 }

 function executa($db,$query,$resp='')  {
      try {  $sql = $db->query($query); }
      catch (Exception $msg)  { $msg = $db->errorInfo();  $resp->alert($msg); return $resp; }
      return $sql;
 }

function TESTAR($dados) {
     $resp = new xajaxResponse();
     $id            = $dados['cliente'];
     $dbms          = $dados['dbms'];
     $banco         = $dbms.'_'.$id;
     $con = new banco_dados($banco);
     if ($con->erro)  { $resp->alert($con->erro); } else { $resp->alert($banco.' Conectou com sucesso!'); }
     return $resp; 
 }
