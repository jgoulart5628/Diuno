<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
/*
 * banco :parametro.sqlite
CREATE TABLE "empresa_banco" ("id" VARCHAR PRIMARY KEY  NOT NULL ,"nome_empresa" VARCHAR NOT NULL  DEFAULT (null)
 *  ,"dbms" VARCHAR NOT NULL ,"dsn" VARCHAR,"host" VARCHAR,"dbname" VARCHAR,"user" VARCHAR,"pwd" VARCHAR, driver VARCHAR)
* 
*/
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
// error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors',TRUE);
// require 'kint.phar';
$db = conecta('parametro.sqlite');
$query =  "alter table empresa_banco add column log_erros bool default 1 ";
$e = executa($query);
//d($e);
$query =  "alter table empresa_banco add column log_trans bool default 0 ";
$e = executa($query);
//d($e);
if (!is_object($db)) { var_dump($db); Echo('Não conectou parametro.sqlite'); exit; }
require_once("../xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
// $xajax->configure("debug", true);
$xajax->configure( 'errorHandler', true );
$xajax->configure( 'logFile', 'xajax_error.log' );
$xajax->register(XAJAX_FUNCTION, "Tela");
$xajax->register(XAJAX_FUNCTION, "INC_ALT_EXC");
$xajax->register(XAJAX_FUNCTION, "CRUD");
$xajax->register(XAJAX_FUNCTION, "TESTAR");
$xajax->processRequest();
$xajax->configure('javascript URI','../xajax/');
// <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
//Kint::trace(); // Debug backtrace
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" type="text/css" href="../css/dataTables.bootstrap.css">
 <link rel="stylesheet" type="text/css" href="../css/nucleo.css">
 <link rel="stylesheet" type="text/css" href="../css/main.css">
  <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
   <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="shortcut icon" href="../icon/favicon.ico">
  <script type="text/javascript" src="../js/modernizr.js"></script>
  <title>Parametros Empresas/Bancos de Dados</title>
  <?php $xajax->printJavascript('../xajax'); ?>
 <style type="text/css">
   th { text-align: center; }
   .bbc { width: 20px; }
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
    <form name="tela" id="tela" class="form" method="post">
     <div class="container-fluid fundo" style="width: 90%;" >  
          <div class="page-header  fundo">
             <h3 class="text-muted centro">JGWeb Sistemas <small> Parametros de Conexão a  Bancos de Dados. </small></h3>
          </div>
          <div id="tela_alt"></div> 
          <div id="tela_lista"></div>
      <div class="footer fundo">
         <span class="glyphicon glyphicon-thumbs-up"></span>&#174; Jgoulart Web
      </div>
    </div>
   </form>
 </body>
   <script type="text/javaScript" src="../js/jquery-1.11.1.min.js" ></script>
   <script type="text/javaScript" src="../js/jquery.dataTables.min.js" ></script>
   <script type="text/javaScript" src="../js/bootstrap.min.js" ></script> 
   <script type="text/javaScript" src="../js/dataTables.bootstrap.js" ></script> 
   <script type="text/javaScript">xajax_Tela() </script>
</html>

 <?php
  function Tela()   {
    $resp = new xajaxResponse();
    $res =  leitura_tabela('','', $resp);
//    $resp->alert('Aqui'.d($res,true)); return $resp;
    $id = '';
    $dbms = '';
    $tela = '<input type="button" class="btn-success" value="Incluir" onclick="xajax_INC_ALT_EXC(\''.$id.'\',\''.$dbms.'\',\'I\'); return false;">
               <table id="clicli" data-toggle="table" class="table table-striped table-bordered"  data-sort-name="empresa" data-sort-order="desc">
                <thead> 
                 <tr>
       <th data-field="id"   data-sortable="true">ID</th>
       <th data-field="nome_empresa" data-sortable="true">Nome Empresa</th>
       <th data-field="dbms" data-sortable="true">DBMS</th>
       <th data-field="dsn" data-sortable="true">DSN</th>
       <th data-field="host" data-sortable="true">HostName</th>
       <th data-field="dbname" data-sortable="true">Nome DB</th>
       <th data-field="dbuser" data-sortable="true">User DB</th>
       <th data-field="dbpasswd" data-sortable="true">Pwd DB</th>
       <th data-field="driver" data-sortable="true">Driver DB</th>
       <th data-field="log_erros" data-sortable="true">Log Erros</th>
       <th data-field="log_trans" data-sortable="true">Log Trans</th>
          </tr>
              </thead>
              <tbody>';
    if (count($res) > 0) {
       foreach ($res as $cli) {
       $id            = $cli['id'];
       $nome_empresa  = $cli['nome_empresa'];
       $dbms          = $cli['dbms'];
       $dsn           = $cli['dsn'];
       $host          = $cli['host'];
       $dbname        = $cli['dbname'];
       $user          = $cli['user'];
       $pwd           = $cli['pwd'];
       $driver        = $cli['driver'];
       if ($cli['log_erros']) { $log_erros  = 'Sim'; } else { $log_erros = 'Não'; }
       if ($cli['log_trans']) { $log_trans  = 'Sim'; } else { $log_trans = 'Não'; }
       $tela .= '<tr>
            <td data-field="id" align="center" data-sortable="true"><input type="button" style="width: 80px;" onclick="xajax_INC_ALT_EXC(\''.$id.'\',\''.$dbms.'\',\'A\'); return false;" value="'.$id.'"></td>
      <td data-field="nome_empresa" data-sortable="true">'.$nome_empresa.'</td>
      <td data-field="dbms" data-sortable="true">'.$dbms.'</td>
      <td data-field="dsn" data-sortable="true">'.$dsn.'</td>
      <td data-field="host" data-sortable="true">'.$host.'</td>
      <td data-field="dbname" data-sortable="true">'.$dbname.'</td>
      <td data-field="dbuser" data-sortable="true">'.$user.'</td>
      <td data-field="dbpasswd" data-sortable="true">'.$pwd.'</td>
      <td data-field="driver" data-sortable="true">'.$driver.'</td>
      <td data-field="log_erros" data-sortable="true">'.$log_erros.'</td>
      <td data-field="driver" data-sortable="true">'.$log_trans.'</td>
           </tr>';
    }
      $tela .= "</tbody></table>";
    }
     $resp->assign("tela_alt","innerHTML",'');   
     $resp->assign("tela_lista","innerHTML",$tela);   
     $resp->script('tabela()');
     return $resp;  
  }
  
  function conecta($banco) {
    $db = new PDO('sqlite:'.$banco);
    return $db;
  }


  function cria_tabela($db) {
    $query =  " CREATE TABLE empresa_banco (id VARCHAR NOT NULL, nome_empresa VARCHAR NOT NULL ,
                 dbms VARCHAR NOT NULL ,dsn VARCHAR, host VARCHAR, dbname VARCHAR, user VARCHAR,pwd VARCHAR, driver varchar, log_erros BOOL, log_trans bool
                 PRIMARY KEY (id, dbms))";
	$sql = $db->query($query);
	return $sql;
  }

  function INC_ALT_EXC($id,$dbms,$oper) {
     $resp = new xajaxResponse();
     global $db;
     if($oper === 'A' || $oper === 'E') {
      if ($id && $dbms)  {
         $res =  leitura_tabela($id,$dbms, $resp);
         $nome_empresa = $res[0]['nome_empresa'];		 
         $dsn          = $res[0]['dsn'];		 
         $host         = $res[0]['host'];		 
         $dbname       = $res[0]['dbname'];		 
         $user         = $res[0]['user'];		 
         $pwd          = $res[0]['pwd'];		 
         $driver       = $res[0]['driver'];		 
         $log_erros    = $res[0]['log_erros'];    
         $log_trans    = $res[0]['log_trans'];    
      } 	 
     }  else {
	 $id            = '';
         $nome_empresa  = '';
         $dbms          = '';
         $dsn           = '';
         $host          = '';
         $dbname        = '';
         $user          = '';
         $pwd           = '';
         $driver        = '';
         $log_erros     =  1;
         $log_trans     =  0;
	}	 
  $logErroS = ''; $logErroN = ''; 
  $logTranS = ''; $logTranN = ''; 
  if ($log_erros) { $logErroS = "checked"; } else { $logErroN = "checked";}
  if ($log_trans) { $logTranS = "checked"; } else { $logTranN = "checked";}
  $tela = '<div class="row">
               <div class="col-sm-12">
                 <div class="form-group">             
                  <input type="button" class="btn btn-success" value="Incluir" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'I\'); return true;">
	                <input type="button" class="btn btn-primary" value="Alterar" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'A\',\''.$id.'\',\''.$dbms.'\'); return true;">
                  <input type="button" class="btn btn-warning" value="Excluir" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'E\',\''.$id.'\',\''.$dbms.'\'); return true;">
         	        <input type="button" class="btn btn-danger"  value="Retornar" onclick="xajax_Tela(\'\'); return true;">
                  <input type="button" class="btn btn-default" value="Testar Conexão" onclick="xajax_TESTAR(xajax.getFormValues(\'tela\')); return false;">
        	      </div>
            </div>  
          </div>  
          <hr>
          <div class="row">
                 <div class="form-group col-sm-2">
                  <label for="lid">ID : </label><input type="text" class="form-control" id="lid" name="id" value="'.$id.'"  placeholder="Nome reduzido para acesso e chave">
                 </div>   
                 <div class="form-group col-sm-3">
                   <label for="lnome_empresa">Nome : </label><input type="text" class="form-control" id="lnome_empresa" name="nome_empresa" value="'.$nome_empresa.'" placeholder="Nome da Empresa">
                 </div>  
                 <div class="form-group col-sm-3">
                 '.combo_dbms($dbms).'
                 </div>     
                <div class="form-group col-sm-2">
                    <label for="dsn">Dsn: </label><input type="text" id="dsn" name="dsn" class="form-control" value="'.$dsn.'" placeholder="DSN da Conexão ">
                </div>    
         </div>  
         <div class="row">
            <div class="form-group col-sm-2">
               <label for="host">Host: </label><input type="text" class="form-control" id="host" name="host" value="'.$host.'" placeholder="Host Name do Servidor ">
            </div>   
            <div class="form-group col-sm-2">
                <label for="dbname">DB: </label><input type="text" class="form-control"  id="dbname" name="dbname" value="'.$dbname.'" placeholder="Nome do Banco ">
            </div>    
            <div class="form-group col-sm-2">
                <label for="driver">Driver: </label><input type="text" class="form-control" id="driver" name="driver" value="'.$driver.'" placeholder="Driver da Conexão ">
            </div>
        </div>
        <div class="row">    
            <div class="form-group col-sm-2">
               <label for="user">Usuário: </label><input type="text" class="form-control"  id="user" name="user" value="'.$user.'" placeholder="Usuario do Banco ">
            </div>   
            <div class="form-group  col-sm-2">
                <label for="pwd">Password: </label><input type="text" class="form-control" id="pwd" name="pwd"  value="'.$pwd.'" placeholder="Senha do Usuario do Banco ">
            </div>
            <div class="form-group col-sm-2">
                <label for="log_erros">Log erros? </label>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" class="custom-control-input" id="log_erros" name="log_erros" value="1" '.$logErroS.'>Sim  &nbsp;&nbsp;<input type="radio" class="custom-control-input" name="log_erros" '.$logErroN.' value="0">Nao
                </div>
            </div>       
            <div class="form-group col-sm-2">
                <label for="log_trans">Log Transação? </label>
                <div class="custom-control custom-radio custom-control-inline">
                     <input type="radio" class="custom-control-input" id="log_trans" name="log_trans" '.$logTranS.' value="1">Sim    &nbsp;&nbsp;<input type="radio" class="custom-control-input" name="log_trans" '.$logTranN.' value="0">Nao
              </div>       
            </div>      
        </div>';
     $resp->assign("tela_lista","innerHTML",'');   
     $resp->assign("tela_alt","innerHTML",$tela);   
     return $resp;
  }  
  
  function combo_dbms($dbms='')  {
     $dbs = array('ORACLE','SQLSERVER','SYBASE','FIREBIRD','MYSQL','ODBC','SQLITE','POSTGRESQL');
     $tela  = '<label for=dbms">DBMS : </label><select  class="form-control" name="dbms" id="dbms"> <option value ="" class="form-control" ></option> ';
     foreach($dbs as $db) {
        if ($db === $dbms)  {  $sel = ' selected '; } else { $sel = ''; }
        $tela .= '<option value="'.$db.'"  '.$sel.' class="form-control" > '.$db.' </option> '; 
     }     
     $tela .= '</select>'; 
     return $tela;
   }  


  function TESTAR($dados) {
     $resp = new xajaxResponse();
     require_once("banco_pdo_class.php");
     $id            = $dados['id'];
     $dbms          = $dados['dbms'];
     $nome_empresa  = $dados['nome_empresa'];
     $dsn           = $dados['dsn'];
     $host          = $dados['host'];
     $dbname        = $dados['dbname'];
     $user          = $dados['user'];
     $pwd           = $dados['pwd'];
     $driver        = $dados['driver'];
     $banco         = $dbms.'_'.$id;
     $con = new banco_dados($banco);
     if ($con->erro)  { $resp->alert("Não Conectou!".$con->erro); } else { $resp->alert($banco.' Conectou com sucesso!'); }
     $resp->alert(print_r($dados,true));
     return $resp; 
  }

  function CRUD($dados,$oper,$id='',$dbms='') {
     $resp = new xajaxResponse();
     global $db;
     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     if($oper !== 'E')  {
       if($oper === 'I')  {
          $id            = trim($dados['id']);
          $dbms          = $dados['dbms'];
       }   
       $nome_empresa  = $dados['nome_empresa'];
       $dsn           = $dados['dsn'];
       $host          = $dados['host'];
       $dbname        = $dados['dbname'];
       $user          = $dados['user'];
       $pwd           = $dados['pwd'];
       $driver        = $dados['driver'];
       $log_erros     = $dados['log_erros'];
       $log_trans     = $dados['log_trans'];
     }  
//     $resp->alert($id.'-'.$nome.'-'.$oper);
     if(!$id) { $resp->alert('Preencha a Empresa!'); return $resp; }
     if(!$dbms) { $resp->alert('Escolha o banco!'); return $resp; }
     if($oper === 'I')  {
    	$query = "INSERT INTO empresa_banco(id, nome_empresa, dbms, dsn, host, dbname, user,pwd,driver, log_erros, log_trans)
    	 VALUES('$id','$nome_empresa','$dbms','$dsn','$host','$dbname','$user','$pwd','$driver',$log_erros, $log_trans)";
        executa($query,$resp);
     }
     if($oper === 'A')  {
        $query = "update empresa_banco set nome_empresa = '$nome_empresa', dsn = '$dsn', host = '$host',
        dbname = '$dbname', user = '$user',pwd = '$pwd', driver = '$driver', log_erros = $log_erros, log_trans = $log_trans
                   where id = '$id' and dbms = '$dbms' ";
        executa($query,$resp);
     }
     if($oper === 'E')  {
	    $query = "delete from  empresa_banco  where id = '$id' and dbms = '$dbms' ";
        executa($query,$resp);
     }
    $resp->script('xajax_Tela()');      
    return $resp;
  }
  
  function executa($query, $resp='')  {
     global $db;
     if ($resp) {
      try {  $sql = $db->query($query); }
      catch (Exception $msg)  { $msg = $db->errorInfo();  $resp->alert(print_r($msg,true).' Erro!!'); return $resp; }
     }
     else {
      try {  $sql = $db->query($query); }
      catch (Exception $msg)  { $msg = $db->errorInfo();  return $msg; }     
     } 
      return $sql;
  }
	

function leitura_tabela($id='',$dbms='', $resp)  {
  global $db;
//	$query = " SELECT * FROM empresa_banco order by id ";
  $query = " SELECT * FROM empresa_banco ";
	if($id)  { $query .=  " where id =  '$id' and dbms = '$dbms' "; }
 	$sql = executa($query, $resp);
 	$res = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $res;
}
