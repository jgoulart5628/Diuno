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
//error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors',TRUE);
include("parametro_class.php");
$db = new Parametro_model();
require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
// $xajax->configure("debug", true);
$xajax->register(XAJAX_FUNCTION, "Tela");
$xajax->register(XAJAX_FUNCTION, "INC_ALT_EXC");
$xajax->register(XAJAX_FUNCTION, "CRUD");
$xajax->register(XAJAX_FUNCTION, "TESTAR");
$xajax->processRequest();
$xajax->configure('javascript URI','xajax/');
// <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
 <link rel="stylesheet" type="text/css" href="css/nucleo.css">
 <link rel="stylesheet" type="text/css" href="css/main.css">
  <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
   <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="shortcut icon" href="icon/favicon.ico">
  <script type="text/javascript" src="js/modernizr.js"></script>
  <title>Parametros Empresas/Bancos de Dados</title>
  <?php $xajax->printJavascript('xajax'); ?>
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
     <div class="container-fluid" style="width: 90%;" >  
          <div class="page-header  fundo">
             <h3 class="text-muted centro">Núcleo Sistemas <small> Parametros de Conexão a  Bancos de Dados. </small></h3>
          </div>
          <div id="tela_alt"></div> 
          <div id="tela_lista"></div>
      <div class="footer fundo">
         <span class="glyphicon glyphicon-thumbs-up"></span>&#174; Jgoulart Web
      </div>
    </div>
   </form>
 </body>
   <script type="text/javaScript" src="js/jquery-1.11.1.min.js" ></script>
   <script type="text/javaScript" src="js/jquery.dataTables.min.js" ></script>
   <script type="text/javaScript" src="js/bootstrap.min.js" ></script> 
   <script type="text/javaScript" src="js/dataTables.bootstrap.js" ></script> 
   <script type="text/javaScript">xajax_Tela() </script>
</html>

 <?php
  function Tela()   {
     $resp = new xajaxResponse();
     $tela1 = consulta_clientes();
     $resp->assign("tela_alt","innerHTML",'');   
     $resp->assign("tela_lista","innerHTML",$tela1);   
     $resp->script('tabela()');
     return $resp;  
  }
  

  function INC_ALT_EXC($id,$dbms,$oper) {
     $resp = new xajaxResponse();
     global $db;
     $script = '';
     if($oper === 'A' || $oper === 'E') {
      if ($id && $dbms)  {
         $res =  $db->leitura_tabela($id,$dbms);
         if (!is_array($res)) { $resp->alert($res); return $resp; }
         $nome_empresa = $res[0]['nome_empresa'];		 
         $dsn          = $res[0]['dsn'];		 
         $host         = $res[0]['host'];		 
         $dbname       = $res[0]['dbname'];		 
         $user         = $res[0]['user'];		 
         $pwd          = $res[0]['pwd'];		 
         $driver       = $res[0]['driver'];		 
         $script = 'document.getElementById("BT_INC").disabled = true;'; 
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
         $script = 'document.getElementById("BT_ALT").disabled = true; document.getElementById("BT_EXC").disabled = true;'; 
	}	 
     $tela = '<div class="form-group">
                ID : <input type="text" class="entra" id="lid" name="id" size="20" value="'.$id.'" placeholder="Nome reduzido para acesso e chave">
                &nbsp;&nbsp;&nbsp;Nome : <input type="text" class="entra" id="lnome_empresa" name="nome_empresa" size="40" value="'.$nome_empresa.'" placeholder="Nome da Empresa">
              </p>
              <fieldset  style="border: 2px inset; font-weight: bold;">
               <legend>Parâmetros Conexão</legend>
                <div class="form-group">
                 '.$db->combo_dbms($dbms).'
                </div>     
                </p> 
                <div class="form-group">
                  &nbsp;&nbsp;Dsn: <input type="text" id="dsn" name="dsn" size="30" value="'.$dsn.'" placeholder="DSN da Conexão ">
                  &nbsp;&nbsp;Host: <input type="text" id="host" name="host" size="20" value="'.$host.'" placeholder="Host Name do Servidor ">
                  &nbsp;&nbsp;DB: <input type="text"  id="dbname" name="dbname" size="20" value="'.$dbname.'" placeholder="Nome do Banco ">
                  </p>
                  &nbsp;&nbsp;Usuário: <input type="text"  id="user" name="user" size="15" value="'.$user.'" placeholder="Usuario do Banco ">
                  &nbsp;&nbsp;Password: <input type="text" id="pwd" name="pwd" size="15" value="'.$pwd.'" placeholder="Senha do Usuario do Banco ">
                  &nbsp;&nbsp;Driver: <input type="text" id="driver" name="driver" size="20" value="'.$driver.'" placeholder="Driver da Conexão ">
                </div>
               </fieldset>
             </div>
             <hr>
             <div class="form-group">'; 
                switch ($oper)  {          
                    case 'I':  $tela .= '<input type="button" class="btn-success" value="Incluir" id="BT_INC" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'I\'); return true;">'; break;
                    case 'A': 
                    case 'E':  $tela .= '<input type="button" class="btn-primary" value="Alterar" id="BT_ALT"onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'A\',\''.$id.'\',\''.$dbms.'\'); return true;">
                              <input type="button" class="btn-warning" value="Excluir" id="BT_EXC"onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'E\',\''.$id.'\',\''.$dbms.'\'); return true;">';
                              break;
                }
                $tela .= '<input type="button" class="btn-danger"  value="Retornar" onclick="xajax_Tela(\'\'); return true;">
                         <input type="button" class="btn-default" value="Testar Conexão" onclick="xajax_TESTAR(xajax.getFormValues(\'tela\')); return true;">
            </div>';
     $resp->assign("tela_lista","innerHTML",'');   
     $resp->assign("tela_alt","innerHTML",$tela);   
     $resp->script($script);
     return $resp;
  }  
  

  function TESTAR($dados) {
     $resp = new xajaxResponse();
     require_once("inc/banco_pdo_class.php");
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
     if ($con->erro)  { $resp->alert("Não Conectou!".$con->erro.'-'.$banco); } else { $resp->alert($banco.' Conectou com sucesso!'); }
//     $resp->alert(print_r($dados,true));
     return $resp; 
  }

  function CRUD($dados,$oper,$id='',$dbms='') {
     $resp = new xajaxResponse();
     global $db;
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
     }  
//     $resp->alert($id.'-'.$nome.'-'.$oper);
     if(!$id) { $resp->alert('Preencha a Empresa!'); return $resp; }
     if(!$dbms) { $resp->alert('Escolha o banco!'); return $resp; }
     if($oper === 'I')  {
    	$query = "INSERT INTO empresa_banco(id, nome_empresa, dbms, dsn, host, dbname, user,pwd,driver)
    	 VALUES('$id','$nome_empresa','$dbms','$dsn','$host','$dbname','$user','$pwd','$driver')";
       $msg = $db->Executa($query);
     }
     if($oper === 'A')  {
        $query = "update empresa_banco set nome_empresa = '$nome_empresa', dsn = '$dsn', host = '$host',
                           dbname = '$dbname', user = '$user',pwd = '$pwd', driver = '$driver'
                   where id = '$id' and dbms = '$dbms' ";
        $msg = $db->Executa($query);
     }
     if($oper === 'E')  {
	    $query = "delete from  empresa_banco  where id = '$id' and dbms = '$dbms' ";
      $msg = $db->Executa($query);
     }
     if (!is_object($msg)) { $resp->alert($msg);  } 
     $resp->script('xajax_Tela()');      
     return $resp;
  }
  
	
 function consulta_clientes() {
    global $db; 
    $res =  $db->leitura_tabela();
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
           </tr>';
	  }
  	  $tela .= "</tbody></table>";
    }
    return $tela;
 }

