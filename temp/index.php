<?php
/*
 * 
 * SELECT PLAN_TABLE_OUTPUT 
  FROM TABLE(DBMS_XPLAN.DISPLAY());

explain plan for
//
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
ini_set('display_errors',true);
// include("inc/banco_pdo_class.php");
include("tabs_conv_class.php");
// $my = new banco_dados('MYSQL_nucleo');
// $sy= new banco_dados('ODBC_semear');
require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
//$xajax->setCharEncoding('UTF-8');
// $xajax->configure('debug',true);
$xajax->register(XAJAX_FUNCTION,"Tela");
$xajax->register(XAJAX_FUNCTION,"Converter");
$xajax->register(XAJAX_FUNCTION,"Selec_Tipo_Conv");
$xajax->register(XAJAX_FUNCTION,"Atualiza");
$xajax->register(XAJAX_FUNCTION,"Refresh");
$xajax->register(XAJAX_FUNCTION,"Refresh_Geral");
$xajax->register(XAJAX_FUNCTION,"cria_lista_tabs");
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
            <h4>Conversão Dados </h4>
            <p>Este programa converte os dados para um DB já criado no Destino.</p>
            <h4>Origem:</h4>
            <p>As bases de origem podem ser Sybase(ASA) e Firebird.</p>
            <h4>Destino</h4>
            <p>As bases de Destino podem ser ORACLE, ou SQL Server.</p>
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
   $tela  = '<div class="col-md-1">Cliente : <select class="entra" style="width: 110px;"  name="empresa" id="empresa" onchange="xajax_Selec_Tipo_Conv(xajax.getFormValues(\'tela\')); return true;"> <option value ="" class="f_texto" ></option> ';
   if (count($empresas) > 0 )  {
        foreach($empresas as $emp) {
       if ($emp)  {    $tela .= '<option value="'.$emp.'"   class="f_texto"> '.$emp.' </option> '; }
      }     
    }
    $tela .= '</select></div> &nbsp; &nbsp;&nbsp; <div id="opc" class="col-md-2"></div>';
    $tela .=  '<div class="col-md-9">
                 <input type="radio" name="tipo_carga" value="1" checked="checked">&nbsp;Carga Direta
                 <input type="radio" name="tipo_carga" value="2">&nbsp;Arq CSV  
                 <input type="radio" name="tipo_carga" value="3">&nbsp;SQL insert&nbsp;        
                 <input type="radio" name="tipo_carga" value="5">&nbsp;Ajuste Tabelas&nbsp;          
                 <button class="btn btn-sm btn-success padrao" onclick="xajax_Converter(xajax.getFormValues(\'tela\')); return false;");">  Converter   </button>
                 <input type="checkbox"  name="todas" value="1">&nbsp;Ver Todas</div>';       
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
     if ((substr($entra,0,4) === 'SYBA') && (substr($saida,0,4) === 'ORAC'))  { $conv = '1'; $origem = 'Sybase'; $destino  = 'Oracle'; }
     if ((substr($entra,0,4) === 'SYBA') && (substr($saida,0,4) === 'SQLS'))  { $conv = '2'; $origem = 'Sybase'; $destino  = 'SQLServer';}
     if ((substr($entra,0,4) === 'FIRE') && (substr($saida,0,4) === 'ORAC'))  { $conv = '3'; $origem = 'Firebird'; $destino = 'Oracle';}
     if ((substr($entra,0,4) === 'FIRE') && (substr($saida,0,4) === 'SQLS'))  { $conv = '4'; $origem = 'Firebird'; $destino = 'Sqlserver';}
     $tela  =  '<h4>'.$origem.' para '.$destino.'</h4> 
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
	   $cliente  = $dados['empresa'];
	   $conv     = $dados['conv'];
	   $tipo     = $dados['tipo_carga'];
     $entra    = $dados['entra'];
     $saida    = $dados['saida'];
     $origem   = $dados['origem'];
     $destino  = $dados['destino'];
	   if (isset($dados['todas']))  { $todas    = $dados['todas']; }
	   if ($tipo === '2'  && ($conv === '3' || $conv === '4') ) {   $resp->alert("Arquivo CSV ainda não é suportado para Firebird. Corrija.");  return $resp; }
//     $resp->alert($cliente.'-'.$conv.'-'.$tipo.'-'.$todas.'-'.$saida.'-'.$entra);  return $resp; 
  	 if (!$cliente ||  !$conv)  {   $resp->alert("Escolha a Empresa ");  return $resp; }
     /*
     switch ($conv)  {
	    case "1":   $origem = 'Sybase';
           	      $destino = 'Oracle';
                  $entra  =   'SYBASE_'.$cliente;						  
                  $saida  =  'ORACLE_'.$cliente;						  
                  break;
	    case "2":   $origem = 'Sybase'; 
		              $destino = 'SQL Server';
                  $entra  =   'SYBASE_'.$cliente;						  
                  $saida  =  'ODBC_'.$cliente;						  
   						   break;
	    case "3":   $origem = 'Firebird';
               	  $destino = 'Oracle'; 
                  $entra  =   'FIREBIRD_'.$cliente;						  
                  $saida  =  'ORACLE_'.$cliente;
    						  break;
	    case "4":   $origem = 'Firebird'; 
		              $destino = 'SQL Server';
                  $entra  =   'FIREBIRD_'.$cliente;						  
                  $saida  =  'ODBC_'.$cliente;						  
     						  break;
     }
     */	
     $tabs_conv = 'MYSQL_nucleo';
	 // testar as base de dados
       // global $en;	 
     $en  = new InOut_model($entra);
	   if ($en->error)  {  $resp->alert('Entrada: '.$en->error);  return $resp;  } 
	   $sai   = new InOut_model($saida);
	   if ($sai->error)  {  $resp->alert('Saida: '.$sai->error);   return $resp;  } 
     $db = new Converte_model($tabs_conv);
     if ($db->error)  {  $resp->alert('Falta Registro MYSQL: '.$db->error);   return $resp; } 
     $lista = $db->ler_tab_conv($cliente);
     $pr = 0;
   	 $tabs = '';
     $ids  = '';
 	   $z = 0;
//     $par = json_encode(get_object_vars($db));
     if (count($lista) > 0)  {
 	     foreach($lista as $file)  {
        	if ($z  > 0)  { $tabs .= '|'; $ids .= '|'; }
    	    $tabs  .= trim($file['tabela']) ;
          $ids   .= $file['id'];
		     if($file['difer'] > 0)  { $pr++; }
			     $z++;
        }
    } 
	  else { 
       $script = "xajax_cria_lista_tabs('$cliente', '$entra', '$saida', '$tabs_conv','$conv')";
       $resp->script($script);
       return $resp;
    }	
//    $resp->alert($cliente.'-'.$entra.'0'.$saida.'-'.$conv); return $resp; 
    $a = 0;
   $tela .= '<table id="clicli" data-toggle="table" class="table table-striped table-bordered"  data-sort-name="Tabela" data-sort-order="desc">
	               <caption>Total tabelas com dados : '.count($lista).'   Tabelas com diferenças: '.$pr.'
    					   <input type="button" style="float: right;" value="Refresh Geral" onclick="xajax_Refresh_Geral(\''.$cliente.'\',\''.$ids.'\',\''.$tabs.'\',\''.$a.'\',\''.$entra.'\',\''.$saida.'\',\''.$tabs_conv.'\'); return false;"></caption>
                   <thead>                  
                    <tr>
       			         <th data-field="seq"      data-sortable="true">Seq.</th>
        						 <th data-field="tabela"   data-sortable="true">Tabela</th>
                     <th data-field="origem"   data-sortable="true"> Regs.'.$origem.'</th>
                     <th data-field="destino"  data-sortable="true"> Regs.'.$destino.'</th>
				            </tr>
                  </thead>';
// 	var_dump($tabs);
    if (count($lista) > 0)  {
      foreach($lista as $file)  {
		   	if ($a == 0 || fmod($a, 2) == 0) { $classe =  'class="t_line1"'; } else { $classe =  'class="t_line2"'; } 
        $id      = $file['id'];
        $arq     = trim($file['tabela']);
        $difer   = $file['difer'];
        $rorigem  = $file['rows_origem'];
        $rdestino = $file['rows_destino'];
        $col_dif   = 0; 				
        $col_dif1   = 0; 				
        $col_dif2   = 0; 				
        $col_long1 = '';
			  $col_long2 = '';
        if ($arq === 'NOTAF_ENT_ELETRONICA')  {
            $res =  $db->ler_coluna_long($id);
//            $resp->alert(print_r($res,true).'-'.$); return $resp; 
            $col_long1 = $res[0]['coluna'];
            $col_dif1  = $res[0]['difer'];
            if ($col_dif1 > 0)  { $col_dif++; }
//            $resp->alert($col_long1.'-'.$col_dif1.' aqui '.$col_dif.'-'.$arq); return $resp; 
        }    
        if ($arq === 'NOTAF_SAI_ELETRONICA')  {
            $res =  $db->ler_coluna_long($id);
            $col_long1 = $res[0]['coluna'];
            $col_dif1  = $res[0]['difer'];
            $col_long2 = $res[1]['coluna'];
            $col_dif2  = $res[1]['difer'];
            if ($col_dif1 > 0 || $col_dif2 > 0)  { $col_dif++; }
        }    
 			$cc = '';
			if (($todas === '1') || (!$todas && ($difer >  0 || $col_dif > 0) ) ) {   
               if ($difer > 0 || $col_dif  > 0)  { $cc = 'class="contar"'; } else { $cc = 'class="verde"';  } 
               $tela .= '<tr '.$classe.'>'
                    . '<td data-field="seq" data-sortable="true">'.($a + 1).'</td>'
                    . '<td data-field="tabela" data-sortable="true">'.$arq.'<input type="button" '.$cc.' id="botao_carrega_'.$a.'" style="float: right;" value="Carregar" onclick="xajax_Barra(\''.$id.'\',\''.$arq.'\',\''.$a.'\',\''.$conv.'\' ,\''.$cliente.'\',\''.$tipo.'\'); return true;">
                        <input type="button" style="float: right;" value="Refresh" onclick="xajax_Refresh(\''.$cliente.'\',\''.$id.'\',\''.$arq.'\',\''.$a.'\',\''.$entra.'\',\''.$saida.'\',\''.$tabs_conv.'\'); return false;">';
				if($col_dif > 0) {
				   $tela .= '</br>';
                   if($col_dif1 <> 0) { $tela  .= $col_long1. ' -  dif: '.$col_dif1;  }
                   if($col_long2 && $col_dif2 > 0)  { $tela  .=  $col_long2.'-  dif.: '.$col_dif2;  }
                   if($col_dif > 0)  { $tela .=  '<input type="button" '.$cc.' id="botao_carrega_'.$a.'"  value="Atualizar" onclick="xajax_Barra(\''.$id.'\',\''.$arq.'\',\''.$a.'\',\''.$conv.'\' ,\''.$cliente.'\',\'4\'); return true;">'; }
				}
                $tela .= '<div id="erro_'.$a.'" class="contar"></div></td>'
                    . '<td data-field="origem"  data-sortable="true" align="right"><input type="txt" class="linha" id="lin_arq_'.$a.'" name="lin_arq_'.$a.'" value="'.number_format($rorigem,0,'.','.').'"></td>'
                    . '<td data-field="destino" data-sortable="true"align="right"><input type="txt" class="linha" id="lin_db_'.$a.'" name="lin_db_'.$a.'" value="'.number_format($rdestino,0,'.','.').'"></td>'
                    . '</tr>';             
                $a++;
			}	
         }
     }
     $tela .= '</table>';
     $resp->assign("saida","innerHTML", $tela);
     $resp->script('tabela()');
 	return $resp;
 }


 function Barra($id, $arq,$a, $conv, $cliente,$tipo)   {
     $resp = new xajaxResponse();
     $tela = '<div style="border:1px solid #000; padding:10px; width:300px; height:100px; overflow:auto; background:#eee;" id="divProgress"></div>
    <br />
	<div style="border:1px solid #ccc; width:300px; height:20px; overflow:auto; background:#eee;">
         <div id="progressor" style="background:#07c; width:0%; height:100%;"></div>
         <div id="mostra_erros"></div>
    </div>';
    $resp->assign("erro_$a","innerHTML",$tela);
    $resp->script("ajax_stream('$arq','$cliente','$conv','$tipo');");  
    return $resp;
   }
   
   
   function cria_lista_tabs($cliente, $entra, $saida, $tabs_conv, $conv)   {
     $resp = new xajaxResponse();
     $en   =   new InOut_model($entra);
     if ($en->error) { $e =  "Erro banco Entrada! ".$entra;   return $e; }
     $sai  =   new InOut_model($saida);
     if ($sai->error) { $e = "Erro banco Entrada! ".$saida;   return $e; }
     $db   =   new Converte_model($tabs_conv);
     if ($db->error) { $e = "Erro banco Converte! ".$tabs_conv;   return $e; }
     $e = '';
     $result = $en->Lista_Tabelas_Orig($conv);
     $resp->alert(print_r($result, true)); return $resp;
     if (!is_array($result))  { $resp->alert("Sem registros na leitura da tabela de entrada ");  return $resp; }
     foreach($result as $tb)  {
        if ($conv === '1' || $conv === '2') {
           $tabela = trim($tb['name']);
        } else { $tabela = trim($tb['TABELA']); }   
        $conta =  $en->Conta_Regs($tabela);
        if ($conta > 0)  {
            $tab = strtoupper($tabela);
            $e = $db->insertRow($tab, $conta);
        }
     }  
     $tab  =  'NOTAF_ENT_ELETRONICA';
     $col  =  'arq_nfe';
     $e    =  colunas_long($cliente, $db, $en, $sai, $tab, $col);
     $tab  =  'NOTAF_SAI_ELETRONICA';
     $e    =  colunas_long($cliente, $db, $en, $sai, $tab, $col);
     $col  =  'arq_cancelamento';
     $e    =  colunas_long($cliente, $db, $en, $sai, $tab, $col);
  	 $resp->alert('Lista do Cliente '.$cliente.' criada! Acesse novamente');
     $resp->call('xajax_Tela()');	 
	   return $resp;   
   } 


   function colunas_long($empresa, $db, $en, $sai, $tabela, $coluna, $resp='')  {
     $tabela_id = $db->Ultimo_ID($empresa, $tabela); 
     $orig_conta = $en->ContaRegs($tabela, $coluna);
     $dest_conta = $sai->ContaRegs($tabela, $coluna);
     $e = $db->insertRowLong($tabela_id, $coluna, $orig_conta, $dest_conta);
//     $resp->alert($tabela_id.'-'.$orig_conta.'-'.$dest_conta.'-'.$e.'-'.$coluna); return $resp;
  }

  function Refresh_Geral($cliente, $id, $tabela, $a, $entra, $saida, $tabs_conv)   {
     $resp = new xajaxResponse();
     $en  = new InOut_model($entra);
     if ($en->error)  {  $resp->alert('Entrada: '.$en->error);  return $resp;  } 
     $sai   = new InOut_model($saida);
     if ($sai->error)  {  $resp->alert('Saida: '.$sai->error);   return $resp; } 
     $db = new Converte_model($tabs_conv);
     if ($db->error)  {  $resp->alert('Falta Registro SQLITE: '.$db->error);   return $resp; } 
     $tabs  = explode('|', $tabela);
     $idx  = explode('|', $id);

     for ($x = 0; $x < count($idx); $x++)  {
         $id = $idx[$x];
         $tb = $tabs[$x];
         $tab = strtolower($tb); 
         $conta = $en->ContaRegs($tab);
         $regs  = $sai->ContaRegs($tab);
         if(!$regs)  { $regs = 0; }
         $e = $db->updateRow($id, $regs, $conta);
//  	 $resp->alert($tabela.'-'.$id.'-'.$cliente.'-'.$entra.'-'.$saida); return $resp;
         if ($tb === 'NOTAF_ENT_ELETRONICA')   {
            $col  =  'arq_nfe';
            $e    =  colunas_long($cliente, $db, $en, $sai, $tb, $col,$resp);
         }
         if ($tb ===  'NOTAF_SAI_ELETRONICA')   {
             $col   =  'arq_nfe';
             $e     =  colunas_long($cliente, $db, $en, $sai, $tb, $col, $resp);
             $col1  =  'arq_cancelamento';
             $e     =  colunas_long($cliente, $db, $en, $sai, $tb, $col1, $resp);
         }
     }  
     $resp->assign("saida","innerHTML", ' ');
     $resp->call('xajax_Tela()');   
     return $resp;
   } 


  function Refresh($cliente, $id, $tabela, $a, $entra, $saida, $tabs_conv)   {
     $resp = new xajaxResponse();
     $en  = new InOut_model($entra);
     if ($en->error)  {  $resp->alert('Entrada: '.$en->error);  return $resp;  } 
     $sai   = new InOut_model($saida);
     if ($sai->error)  {  $resp->alert('Saida: '.$sai->error);   return $resp; } 
     $db = new Converte_model($tabs_conv);
     if ($db->error)  {  $resp->alert('Falta Registro Mysql: '.$db->error);   return $resp; } 
      $tab = strtolower($tabela); 
      $conta = $en->ContaRegs($tab);
      $regs  = $sai->ContaRegs($tab);
      if(!$regs)  { $regs = 0; }
      $e = $db->updateRow($id, $regs, $conta);
//     $resp->alert($tabela.'-'.$id.'-'.$cliente.'-'.$entra.'-'.$saida); return $resp;
      if ($tabela === 'NOTAF_ENT_ELETRONICA')   {
          $col  =  'arq_nfe';
          $e    =  colunas_long($cliente, $db, $en, $sai, $tabela, $col,$resp);
         }
      if ($tabela ===  'NOTAF_SAI_ELETRONICA')   {
             $col   =  'arq_nfe';
             $e     =  colunas_long($cliente, $db, $en, $sai, $tabela, $col, $resp);
             $col1  =  'arq_cancelamento';
             $e     =  colunas_long($cliente, $db, $en, $sai, $tabela, $col1, $resp);
         }
       $resp->assign("lin_db_$a","value", number_format($regs,0,'.','.')); 
       $resp->assign("lin_arq_$a","value", number_format($conta,0,'.','.')); 
       $resp->assign("erro_$a","innerHTML", "");
       if ($regs === $conta) { 
          $resp->assign("botao_carrega_$a","className", "verde"); 
       }   
      return $resp;
   } 
  