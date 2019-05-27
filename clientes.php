<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
// error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors',TRUE);
$db = conecta('dados.db');
require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
// $xajax->configure("debug", true);
$xajax->register(XAJAX_FUNCTION, "Tela");
$xajax->register(XAJAX_FUNCTION, "CRUD");
$xajax->processRequest();
$xajax->configure('javascript URI','xajax/');

?>
<!DOCTYPE html>
<html class=no-js>
<head>
 <meta charset=utf-8>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
 <link rel="stylesheet" type="text/css" href="../css/dataTables.bootstrap.css">
  <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
   <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <title>Manutenção de Clientes</title>
  <?php $xajax->printJavascript('xajax'); ?>
 <style type="text/css">
   th { text-align: center; }
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
	 <div class="container">  
       <form name="tela" id="tela" class="form" method="post">
           <div id="tela_alt"></div> 
           <div id="tela_lista"></div>
      </form>
    </div>
 </body>
   <script src="../js/jquery-1.11.1.min.js" ></script>
   <script src="../js/jquery.dataTables.min.js" ></script>
   <script src="../js/bootstrap.min.js" ></script> 
   <script src="../js/dataTables.bootstrap.js" ></script> 
   <script type="text/javaScript">xajax_Tela() </script>
</html>

 <?php
  function Tela($id='',$nome='')   {
     $resp = new xajaxResponse();
     global $db;
     $tela = '<div class="form-group">
                <label for="lid" class="col-xs-2">Id : </label>
                <input type="text" class="form-control" id="lid" name="id" size="5" value="'.$id.'" readonly placeholder="Chave do Registro">
              </div>
              <div class="form-group">
                <label for="lnome" class="col-xs-2">Nome : </label>
                <input type="text" class="form-control" id="lnome" name="nome" size="40" value="'.$nome.'" placeholder="Nome do Cliente">
              </div>
               </p>
              <div class="form-group">             
               <input type="button" class="btn-success" value="Incluir" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'I\'); return true;">
	           <input type="button" class="btn-primary" value="Alterar" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'A\',\''.$id.'\'); return true;">
	           <input type="button" class="btn-warning" value="Excluir" onclick="xajax_CRUD(xajax.getFormValues(\'tela\'),\'E\',\''.$id.'\'); return true;">
	         </div>';
     $tela1 = consulta_clientes($db);
     $resp->assign("tela_alt","innerHTML",$tela);   
     $resp->assign("tela_lista","innerHTML",$tela1);   
     $resp->script('tabela()');
     return $resp;  
  }
  
  function conecta($banco) {
    $db = new PDO('sqlite:'.$banco);
    return $db;
  }


  function cria_tabela($db, $tabela) {
	$query = 'CREATE TABLE '.$tabela.' (id integer primary key, nome text)';
	$sql = $db->query($query);
	return $sql;
  }

  
  function CRUD($dados,$oper,$id='') {
     $resp = new xajaxResponse();
     global $db;
     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     $nome = $dados['nome'];
//     $resp->alert($id.'-'.$nome.'-'.$oper);
     if(!$nome) { $resp->alert('Preencha o nome!'); return $resp; }
     if($oper === 'I')  {
	$query = "INSERT INTO clientes(id, nome) VALUES(null,'$nome')";
        executa($db,$query);
     }
     if($oper === 'A')  {
	$query = "update clientes set nome = '$nome' where id = $id ";
        executa($db,$query);
     }
     if($oper === 'E')  {
	$query = "delete from  clientes  where id = $id ";
        executa($db,$query);
     }
    $resp->script('xajax_Tela()');      
    return $resp;
  }
  
  function executa($db,$query,$resp='')  {
      try {  $sql = $db->query($query); }
      catch (Exception $msg)  { $msg = $db->errorInfo();  $resp->alert($msg); return $resp; }
      return $sql;
  }
	
 function consulta_clientes($db) {
	$query = 'SELECT * FROM clientes';
//	if ($cliente) {
//		$query .= " WHERE id = $cliente";
//	}
	$sql = executa($db,$query);
	$res = $sql->fetchAll(PDO::FETCH_ASSOC);
	if (count($res) > 0) {
	    $tela = '<table id="clicli" data-toggle="table" class="table table-striped table-bordered"  data-sort-name="nome" data-sort-order="desc">
       	          <thead>	
       	        	<tr>
		   <th data-field="id"   data-sortable="true">Chave</th>
		   <th data-field="nome" data-sortable="true">Nome</th>
		</tr> </thead><tbody>';
	  foreach ($res as $cli) {
	     $id = $cli['id'];
	     $nome = $cli['nome'];
	     $tela .= '<tr>
                  	<td data-field="id" align="center" data-sortable="true"><a href="#"  size="5"  onclick="xajax_Tela(\''.$id.'\',\''.$nome.'\'); return false;">'.$id.'</a></td>				
		 	<td data-field="nome" data-sortable="true">'.$nome.'</td>
                     </tr>';
	  }
  	  $tela .= "</tbody></table>";
	}
	return $tela;
  }
