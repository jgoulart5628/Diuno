<?php
/*
regras de migração

C:\2-SMFRJ-GICOF\02-Solucao\09-Migracao\Anexos\SMFRJ_GICOF_DEPARAMatrizMigracaoGICOFCadastroFinanceiro.xlsx
*/
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
// $header = Header("Pragma: no-cache");
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//  error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors', true);
// carregando parametros e tabela conexões DB (GLOBAL empresas e conver)
$cfg = parse_ini_file("inc/imigra.ini",true);
foreach($cfg as $x=>$y) {
   $conver[] .= $x;
}
$par = 'inc/parametro.sqlite';
$sqlite = new PDO('sqlite:'.$par);
if (is_object($sqlite))  {
   $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $query = " SELECT distinct id, dbms  FROM empresa_banco  order by id ";
   try {  $sql = $sqlite->query($query); }
      catch (Exception $msg)  { $msg = $sqlite->errorInfo();  echo $msg; }
   $res  = $sql->fetchAll(PDO::FETCH_ASSOC);
   foreach($res as $ban) {
     $empresas[] = $ban['dbms'].'_'.$ban['id'];  
   }
} else { print_r($sqlite).' Erro na conexão ao '.$par; exit; }   
// print_r($empresas);
include("inc/banco_pdo_class.php");
// $db = new banco_dados('ORACLE_gicof');
// $sy= new banco_dados('ODBC_semear');
require_once("xajax/xajax_core/xajax.inc.php");
$xajax = new xajax();
//$xajax->setCharEncoding('UTF-8');
// $xajax->configure('debug',true);
$xajax->configure( 'errorHandler', true );
$xajax->configure( 'logFile', 'c:\xampp\htdocs\teste\error_log.log' );
$xajax->register(XAJAX_FUNCTION,"Tela");
$xajax->register(XAJAX_FUNCTION,"Validar");
$xajax->register(XAJAX_FUNCTION,"Executa_Conv");
$xajax->register(XAJAX_FUNCTION,"pes_fornecedor");
$xajax->register(XAJAX_FUNCTION,"pes_pessoa");
$xajax->register(XAJAX_FUNCTION,"pes_logradouro");
$xajax->register(XAJAX_FUNCTION,"pes_endereco");
$xajax->register(XAJAX_FUNCTION,"pes_bairro_distrito");
$xajax->register(XAJAX_FUNCTION,"Selec_Tipo_Conv");
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
 <?php $xajax->printJavascript('xajax'); ?>
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
 <script type="text/javaScript" src="js/jquery-1.11.1.min.js" ></script>
 <script type="text/javaScript" src="js/bootstrap.min.js" ></script>
 <script type="text/javascript">
        function Saida(url){
        var form = document.createElement("form");
        form.setAttribute("action",url);
        form.setAttribute("method","GET");
        form.setAttribute("target","_blank");
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

  function doClear(ind)  { 
        var divpro = 'divProg' + ind;
        document.getElementById(divpro).innerHTML = ""; 
    }
     
    function log_message(message,ind) {
         var divpro = 'divProg' + ind;
         document.getElementById(divpro).innerHTML += message + '<br />';
    }


//    function log_erro(erro) {  document.getElementById("mostra_erro").innerHTML += erro + '<br />';   }
    function ajax_stream(desti, banco, origem, saida, ind)
    {   
        if (!window.XMLHttpRequest)
        {
            log_message("Seu browser nao suporta o objeto nativo XMLHttpRequest.");
            return;
        }
         
        try
        {
            var xhr = new XMLHttpRequest(); 
            xhr.previous_text = '';
             
            //xhr.onload = function() { log_message("[XHR] Done. responseText: <i>" + xhr.responseText + "</i>"); };
            xhr.onerror = function() { log_message("[XHR] Erro Fatal.", ind); };
            xhr.onreadystatechange = function()
            {
                try
                {
                    if (xhr.readyState > 2)
                    {
                        var progbar = 'progressor_' + ind;
                        var new_response = xhr.responseText.substring(xhr.previous_text.length);
                        var result = JSON.parse( new_response );
                        log_message(result.message, ind); 
                        //update the progressbar
                        document.getElementById(progbar).style.width = result.progress + "%";
                        xhr.previous_text = xhr.responseText;
                    }  
                }
                catch (e)
                {
    //               log_message("<b>[XHR] Exception: " + e + "</b>");
                }
                 
                 
            };
     
            xhr.open("GET", "ajax_stream.php?desti=" + desti + "&banco=" + banco + "&origem=" + origem  + "&saida="  + saida + "&ind="  + ind, true);
            xhr.send("Solicitando dados...");     
        }
        catch (e)
        {
            log_message("<b>[XHR] Exception: " + e + "</b>");
        }
    }
 </script>
</head>
<body>
  <form id="tela" name="tela" class="form" method="POST">
  <div class="container-fluid">
     <div class="text-muted centro topo"><img src="img/nucleo.gif">
          Projeto Diuno <small> Rotinas de Migração de Bancos de Dados. </small>
     </div>
     <hr>
    <div id="tab_menu" class="centro row fundo"></div>
    <div class="row fundo">
      <div class="col-md-12 marketing">
      <div id="saida" class=col-lg-12>
            <h4>Conversão Dados </h4>
            <p>Este programa converte os dados de uma tabela escolhida para seus destinos
            na rotina de migração de dados do GICOF</p>
       </div>
      </div>
    </div>
    <div id="erros"></div>
    <div class="footer fundo rodape">
         <span class="glyphicon glyphicon-thumbs-up"></span>&#174; Jgoulart Web
		  	      <button class="btn btn-sm btn-success roda" onclick="Saida('inc/empresa_banco.php');  return false;">  Parametros Conexão </button>
              <button class="btn btn-sm btn-danger roda" onclick="Saida('acessa_logs.php');  return false;">  Logs Sistema </button>
    </div>
   </div>
  </form>
   <script type="text/javaScript">xajax_Tela() </script>
 </body>
</html>
<?php
 function Tela()   {
    $resp = new xajaxResponse();
    global $conver;
    global $empresas;
     $tela  = '<nav>
            <ul class="row center-xs middle-xs">
              <li class="col-xs-2">
                <label for="db" class="label-control">Conexão :</label>
                <select class="form-control" name="db" id="db" onchange="xajax_Selec_Tipo_Conv(xajax.getFormValues(\'tela\')); return false;">
              <option value ="" ></option> ';
         if (count($empresas) > 0 )  {
            foreach($empresas as $emp) {
           if ($emp)  {    $tela .= '<option value="'.$emp.'" > '.$emp.' </option> '; }
           }     
         }
//    var_dump($dados);
//    if ($dados['conv']) { $resp->alert($dados['conv']); return $resp; }
    $tela .= '</select>
             </li>
             <li class="col-xs-2">
                 <label for="opc" class="label-control">Origem:</label>
                  <select id="opc" class="form-control" name="opc">';
                    foreach ($conver as $opc) {
                      $tela .= '<option value ="'.$opc.'" class="f_texto" >'.$opc.'</option>';
                  }
                  $tela .= '</select>
             </li>
             <li class="col-xs-2">
                <label for="saida">Opção Saida</label>
                   <select id="opc" class="form-control" name="saida">
                     <option value ="0" class="form-control" >Insert direto</option>
                     <option value ="1" class="form-control" >Arquivo SQL</option>
                   </select>  
             </li>
             <li class="col-xs-2">
                 <button class="btn btn-primary padrao" style="margin-top: 25px;" onclick="xajax_Validar(xajax.getFormValues(\'tela\')); return false;");"> Validar Opção  </button>
             </li>
          </ul></nav>';
//	$tela .= ' <button class="btn btn-sm btn-danger padrao" onclick="Saida(\'erro_sql.php\');  return false;">  Erros SQL   </button>';
       $resp->assign("tab_menu","innerHTML", $tela);
     return $resp;
 }

  function Selec_Tipo_Conv($dados) {
     $resp     = new xajaxResponse();
     $empresa  = $dados['db'];
     $tela = '<input type="hidden" name="db" value="'.$empresa.'">';
     $resp->assign("banco", "innerHTML", $tela);
     return $resp; 
  }   


 function Validar($dados) {
     $resp     = new xajaxResponse();
     $banco    = $dados['db'];
     $opcao    = $dados['opc'];
     $saida    = $dados['saida'];
//     $resp->alert($schema.' aqui'.print_r($cfg,true)); return $resp;
     $db = new banco_dados($banco);
     if (!is_object($db)) {  $resp->alert(print_r($db,true));  return $resp; }
     global $cfg;
     $tipo    = $cfg[$opcao]['tipo'];
     $origem  = $cfg[$opcao]['origem'];
     $destino = $cfg[$opcao]['destino'];
     $schema  = $cfg[$opcao]['schema'];
     $loc =  explode(":", $banco);
     $local = explode('_',$loc[0]);
     if ($local[2] ==  'local') { $schema = 'jgoulart'; }
     $des = '';
     $query = " alter session set current_schema=$schema ";
//     $resp->alert('Aqui '.$opcao.'-'.$origem.'-'.$schema.' - '.$query); return $resp;
     $e = $db->banco_query($query, 'sql', $resp);
     if($e != 1) { /* $resp->alert(print_r($e,true));*/ $resp->assign("erros", "innerHTML", 'Schema:'.$schema.'-'.print_r($e,true)); return $resp; }
     $dest  = explode('|', $destino);
//     $resp->alert($banco.' - aqui'); return $resp;
     if ($tipo  == 'tabela') {
        $tabor = $origem; 
        $query = " select count(*) conta from $tabor ";
        $conta = $db->banco_query($query,'unico', $resp);
        if (is_numeric($conta)) {
            $tela = '<h3> Registros na tabela '.$tabor.' :'.$conta.'</h3></p>';
        }  else { $resp->alert('sem registros na tabela de origem, verifique.'); return $resp; }  
        $a = 0;  
        foreach ($dest as $dp) {
          $des = '';
          $xx  = explode('_', $dp);
          if ($xx[1] == 'PESSOA') {
            if(count($xx) > 2) { $des = 'disabled'; }
          } 
          $query = " select count(*) conta from $dp ";
          $ctdp  = $db->banco_query($query,'unico', $resp);
          if(!is_numeric($ctdp)) { $ctdp = 0; }
          $tela .= '<button type="submit" class="btn btn-primary" style="width: 200px;" '.$des.' onclick="xajax_Executa_Conv(\''.$dp.'\',\''.$banco.'\',\''.$origem.'\',\''.$saida.'\',\''.$a.'\'); return false;">'.$dp.'</button> - '.$ctdp.' Registros </p>
            <div id="erro_'.$a.'"></div>';
           $a++; 
        }
//        $resp->alert($tabor); return $resp;
     }
     if ($tipo  == 'arquivo') {
        if (!file_exists($origem)) { 
            $resp->alert('Arquivo '.$origem.' Não existe!'); 
            return $resp; }
        $linhas = 0;
        $fh = fopen($origem, "r");
        while(!feof($fh)){
  //        $line = fgets($fh);
          $linhas++;
        } 
        fclose($fh);
        $a = 0;  
        $origem .= ';arquivo';
        $origem = str_replace("\\", "//", $origem);
        $query = " select count(*) conta from $destino ";
        $conta = $db->banco_query($query,'unico', $resp);
        if ($conta >= $linhas)  { $des = 'disabled'; } else {$des = ''; }
        $tela = '<h3> Registros no Arquivo '.$origem.' :'.$linhas.'</h3></p>';
        $tela .= '<button type="submit" class="btn btn-primary" '.$des.' onclick="xajax_Executa_Conv(\''.$destino.'\',\''.$banco.'\',\''.$origem.'\',\''.$saida.'\',\''.$a.'\'); return false;">'.$destino.'</button> - '.$conta.' Registros <div id="erro_'.$a.'"></div>';
     }
//     $opcao($tipo, $origem, $destino, $resp);
//     $resp->alert($opcao);
//     $resp->script('xajax_Tela();');
     $resp->assign("saida", "innerHTML", $tela);
//     $resp->assign("$desti", "value", $tipo.' - '.$origem.' - '.$destino.'-'.$depend);
     return $resp;
 }

 function Executa_Conv($desti, $banco, $origem, $saida, $a)  {
     $resp  = new xajaxResponse();
     $destix =  strtolower($desti);     
     $db    = new banco_dados($banco);
     $or = explode(';', $origem);
     if (count($or) === 1) {
        $query = " select * from $origem where rownum < 2000 ";
        $resul = $db->banco_query($query,'array', $resp);
     }  else { $resul = file($or[0]); }  
     if (!is_array($resul)) { $resp->alert('Sem registros de origem : '.$origem); return $resp; }
//     $origem = ';arquivo';
//     $resp->alert(count($resul)); return $resp;
     if (!$desti) { $resp->alert('Opção Inválida : '.$desti); return $resp; }
     $tela = '<div style="border:1px solid #000; padding:10px; width:300px; height:100px; overflow:auto; background:#eee;" id="divProg'.$a.'"></div>
       <div style="border:1px solid #ccc; width:200;px; height:20px; overflow:auto; background:#eee;">
         <div id="progressor_'.$a.'" style="background:#07c; width:0%; height:100%;"></div>
    </div></p>'; 
//    global $programa;
//    $programa = $desti;
//    $resp->alert(print_r($GLOBALS,true)); return $resp;
//    $result = serialize($resul);
    $resp->assign("erro_$a", "innerHTML", '');
    $resp->assign("erro_$a", "innerHTML", $tela);
//    $script = "xajax_$destix('$banco', '$result','$saida');";
    $resp->script("ajax_stream('$destix','$banco','$origem','$saida', '$a');");  
//    $resp->script($script); 
    return $resp;
 }

function send_message($message, $ind=0) {
    $d = array('message' => $message , 'ind' => $ind);
     echo json_encode($d) . PHP_EOL;
  
    //PUSH THE data out by all FORCE POSSIBLE
    ob_flush();
    flush();
}

/*

//  pl para ativar e desativar constraints

 declare cursor xx is
  SELECT 'alter table ' || tabela || ' modify  constraint ' || fk || ' disable cascade ' constra
  from (
    select (select x.table_name from user_constraints x where x.constraint_name = aa.r_constraint_name) tabela
        ,aa.r_constraint_name as fk
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = 'PES_LOGRADOURO'
union all
select aa.table_name as tabela, aa.constraint_name fk
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = 'PES_LOGRADOURO'
);
 begin
    for xx_rec in xx
    loop
       execute immediate xx_rec.constra; 
       commit;
     end loop;
  end; 
  
  
   } 

   if ($op === 'E')  {
       $query = " declare cursor xx is
                 SELECT 'alter table ' || tabela || ' modify  constraint ' || fk || ' enable novalidate ' constra 
                  from (
        select (select x.table_name from user_constraints x where x.constraint_name = aa.r_constraint_name) tabela
               ,aa.r_constraint_name as fk 
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = '$tabela'
union all
select aa.table_name as tabela, aa.constraint_name fk
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = '$tabela'
);
          begin
             for xx_rec in xx
             loop
              execute immediate xx_rec.constra; 
              commit;
            end loop;
            end; ";  
   }

   document.getElementById("myBtn").disabled = true; *
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
