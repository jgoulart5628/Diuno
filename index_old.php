<?php
/*
regras de migração

C:\2-SMFRJ-GICOF\02-Solucao\09-Migracao\Anexos\SMFRJ_GICOF_DEPARAMatrizMigracaoGICOFCadastroFinanceiro.xlsx
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
          $line = fgets($fh);
          $linhas++;
        } 
        fclose($fh);
        $a = 0;  
        $query = " select count(*) conta from $destino ";
        $conta = $db->banco_query($query,'unico', $resp);
        if ($conta >= $linhas)  { $des = 'disabled'; } else {$des = ''; }
        $tela = '<h3> Registros no Arquivo '.$origem.' :'.$linhas.'</h3></p>';
        $tela .= '<button type="submit" class="btn btn-primary" '.$des.' onclick="xajax_Executa_Conv(\''.$desti.'\',\''.$banco.'\',\''.$origem.'\',\''.$saida.'\',\''.$a.'\'); return false;">'.$destino.'</button> - '.$conta.' Registros <div id="erro_'.$a.'"></div>';
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
     $query = " select * from $origem where rownum < 2000 ";
     $resul = $db->banco_query($query,'array', $resp);
     if (!is_array($resul)) { $resp->alert('Sem registros de origem : '.$origem); return $resp; }
//     $resp->alert(count($resul)); return $resp;
     if (!$desti) { $resp->alert('Opção Inválida : '.$destino); return $resp; }
     $tela = '<div style="border:1px solid #000; padding:10px; width:300px; height:100px; overflow:auto; background:#eee;" id="divProg'.$a.'"></div>
       <div style="border:1px solid #ccc; width:200;px; height:20px; overflow:auto; background:#eee;">
         <div id="progressor_'.$a.'" style="background:#07c; width:0%; height:100%;"></div>
    </div></p>'; 
//    global $programa;
//    $programa = $desti;
//    $resp->alert(print_r($GLOBALS,true)); return $resp;
    $result = serialize($resul);
    $resp->assign("erro_$a", "innerHTML", '');
    $resp->assign("erro_$a", "innerHTML", $tela);
//    $script = "xajax_$destix('$banco', '$result','$saida');";
    $resp->script("ajax_stream('$destix','$banco','$origem','$saida', '$a');");  

    $resp->script($script); 
    return $resp;
 }

function pes_pessoa($banco, $resul, $saida) {
     $resp = new xajaxResponse();
     $pes  = unserialize($resul);
     $db = new banco_dados($banco);
//     $resp->alert(print_r($pes,true)); return $resp;S
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_pessoa = $dirx.'\\PES_PESSOA.sql';
        $pes_pj     = $dirx.'\\PES_PESSOA_JURIDICA.sql';
        $pes_pf     = $dirx.'\\PES_PESSOA_FISICA.sql';
        $pes_pessoa = str_replace("\\", "//", $pes_pessoa);
        $pes_pj     = str_replace("\\", "//", $pes_pj);
        $pes_pf     = str_replace("\\", "//", $pes_pf);
//     $resp->alert($pes_pessoa.'-'.$pes_pj.'-'.$pes_pf); return $resp;
        $fpess      = fopen($pes_pessoa, "w+");
        $fpjur      = fopen($pes_pj, "w+");
        $fpfis      = fopen($pes_pf, "w+");
     }
     //a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b) ";
     // $pes = $db->banco_query($query, 'array');
     $tela = '<b>CNPJ/CPF '.count($pes).'</b></p>';
     $y1 = 0;
     $y2 = 0;
     $y3 = 0;
     $com = 'commit;'.PHP_EOL;
     for ($a = 0; $a < count($pes); $a++)   {
        $NR_CPF_CGC      = $pes[$a]['NR_CPF_CGC'];
        $TP_FAVORECIDO_D = $pes[$a]['TP_FAVORECIDO_D'];
        $NM_FAVORECIDO   = str_replace("&","e", $pes[$a]['NM_FAVORECIDO']);
        $TX_ENDERECO     = $pes[$a]['TX_ENDERECO'];
        $NR_TELEFONE     = $pes[$a]['NR_TELEFONE'];
        $TX_CIDADE       = $pes[$a]['TX_CIDADE'];
        $NR_BANCO        = $pes[$a]['NR_BANCO'];
        $NR_AGENCIA      = $pes[$a]['NR_AGENCIA'];
        $SG_UF           = $pes[$a]['SG_UF'];
        $NR_CTA_CORRENTE = $pes[$a]['NR_CTA_CORRENTE'];
        $NR_CEP          = $pes[$a]['NR_CEP'];
        $NR_INSCR_MUNICIPAL = $pes[$a]['NR_INSCR_MUNICIPAL'];
        $NR_INSC_ESTADUAL  = $pes[$a]['NR_INSC_ESTADUAL'];
        $TP_RAMO_ATIV_D     = $pes[$a]['TP_RAMO_ATIV_D'];
        $IN_SUSPENSAO_D     = $pes[$a]['IN_SUSPENSAO_D'];
        $UG_CD_UG_SUSPEN    = $pes[$a]['UG_CD_UG_SUSPEN'];
        $IN_COLETIVO_D      = $pes[$a]['IN_COLETIVO_D'];
        $IN_EVENTUAL_D      = $pes[$a]['IN_EVENTUAL_D'];
        $ST_ATIVO           = $pes[$a]['ST_ATIVO'];
        $DT_DESATIVACAO     = $pes[$a]['DT_DESATIVACAO'];
        $NR_AGENCIA_DIR     = $pes[$a]['NR_AGENCIA_DIR'];
        $NR_BANCO_DIR       = $pes[$a]['NR_BANCO_DIR'];
        $NR_CTA_CORRENTE_DIR = $pes[$a]['NR_CTA_CORRENTE_DIR'];
        $CD_ORIGEM         = $pes[$a]['CD_ORIGEM'];
        $TP_NAT_JURIDICA   = $pes[$a]['TP_NAT_JURIDICA'];
        $FL_ZONAFRANCASOCIAL = $pes[$a]['FL_ZONAFRANCASOCIAL'];
        $sql_pesj = ''; 
        $sql_pesf = ''; 
        $sql_pes  = ''; 
        $query =  " SELECT id FROM pes_pessoa_fisica WHERE cpf = '$NR_CPF_CGC'
                        union
                   SELECT id FROM pes_pessoa_juridica WHERE CNPJ = '$NR_CPF_CGC' ";
        $id = $db->banco_query($query, 'unico');           

        if(!$id)  {

        if ($TP_FAVORECIDO_D == '2') { $tp_pessoa = 'J'; } else { $tp_pessoa = 'F'; }
//        $id = $db->banco_query(" select nvl((max(a.id) + 1),1) from pes_pessoa a ", 'unico', $resp);
        $id = $db->banco_query(" select pes_pessoa_seq.nextval from dual ", 'unico');
        if ($id) {
//        $tela .= $NR_CPF_CGC.'</p>';
          $sql_pes =  " insert into pes_pessoa (id, NM_PESSOA, TP_SITUACAO_PESSOA, TP_PESSOA, NR_VERSAO, DT_CRIADO_EM, COD_USU_CRIADO)
   values ($id, '$NM_FAVORECIDO', 1, '$tp_pessoa', 0, current_timestamp, 1 ) ";
           if ($saida == '1')  {
              $sql_pes .= ';'.PHP_EOL; 
              fwrite($fpess, $sql_pes);
              $y1++;
              if ($y1 > 100) { fwrite($fpess, $com); $y1 = 0; }
           } else {
             $e = $db->banco_query($sql_pes,'sql', $resp);
           }   
          if ($tp_pessoa == 'J') {
            $cnpj = str_pad($NR_CPF_CGC,14, "0", STR_PAD_LEFT);
           $sql_pesj = " insert into pes_pessoa_juridica (id, cnpj)
                         values($id , '$cnpj') ";
//           
           if ($saida == '1')  {
              $sql_pesj .= ';'.PHP_EOL; 
              fwrite($fpjur, $sql_pesj);
              $y2++;
              if ($y2 > 100) { fwrite($fpjur, $com); $y2 = 0; }
           } else { $e = $db->banco_query($sql_pesj,'sql', $resp); }   
         }
         if ($tp_pessoa == 'F') {
            $cpf = str_pad($NR_CPF_CGC,11, "0", STR_PAD_LEFT);
            $sql_pesf = " insert into pes_pessoa_fisica (id, CPF, IND_TRANSX,IND_DOADOR_SANGUE,IND_DOADOR_ORGAO)
                         values($id , '$cpf', 0, 0, 0) ";
//          $e = $db->banco_query($sql_pesf,'sql', $resp); 
           if ($saida == '1')  {
              $sql_pesf .= ';'.PHP_EOL; 
              fwrite($fpfis, $sql_pesf);
              $y3++;
             if ($y3 > 100) { fwrite($fpfis, $com); $y3 = 0; }
           } else { $e = $db->banco_query($sql_pesf,'sql', $resp); }   
        }
        $tela .= $NR_CPF_CGC.'</p>';
       } 
     }
    } 
    if ($saida == '1')  {
       fwrite($fpess, $com);
       fclose($fpess);
       fwrite($fpjur, $com);
       fclose($fpjur);
       fwrite($fpfis, $com);
       fclose($fpfis);
    }
     $resp->assign("saida", "innerHTML", $tela); 
     return $resp;
}

function pes_logradouro($banco ,$resul, $saida) {
     $resp = new xajaxResponse();
     $pes  = unserialize($resul);
     $db = new banco_dados($banco);
//     $resp->alert($banco.'-'.$saida);
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_logra  = $dirx.'\\PES_LOGRADOURO.sql';
        $pes_logra  = str_replace("\\", "//", $pes_logra);
//     $resp->alert($pes_pessoa.'-'.$pes_pj.'-'.$pes_pf); return $resp;
        $flogra     = fopen($pes_logra, "w+");
     }
     $x = 0;
     $err = 0;
     $com = 'commit;'.PHP_EOL;
     for ($a = 0; $a < count($pes); $a++)   {
        $NR_CPF_CGC      = $pes[$a]['NR_CPF_CGC'];
        $TP_FAVORECIDO_D = $pes[$a]['TP_FAVORECIDO_D'];
        $NM_FAVORECIDO   = str_replace("&","e", $pes[$a]['NM_FAVORECIDO']);
        $TX_ENDERECO     = $pes[$a]['TX_ENDERECO'];
        $NR_TELEFONE     = $pes[$a]['NR_TELEFONE'];
        $TX_CIDADE       = $pes[$a]['TX_CIDADE'];
        $NR_BANCO        = $pes[$a]['NR_BANCO'];
        $NR_AGENCIA      = $pes[$a]['NR_AGENCIA'];
        $SG_UF           = $pes[$a]['SG_UF'];
        $NR_CTA_CORRENTE = $pes[$a]['NR_CTA_CORRENTE'];
        $NR_CEP          = $pes[$a]['NR_CEP'];
        $NR_INSCR_MUNICIPAL = $pes[$a]['NR_INSCR_MUNICIPAL'];
        $NR_INSC_ESTADUAL  = $pes[$a]['NR_INSC_ESTADUAL'];
        $TP_RAMO_ATIV_D     = $pes[$a]['TP_RAMO_ATIV_D'];
        $IN_SUSPENSAO_D     = $pes[$a]['IN_SUSPENSAO_D'];
        $UG_CD_UG_SUSPEN    = $pes[$a]['UG_CD_UG_SUSPEN'];
        $IN_COLETIVO_D      = $pes[$a]['IN_COLETIVO_D'];
        $IN_EVENTUAL_D      = $pes[$a]['IN_EVENTUAL_D'];
        $ST_ATIVO           = $pes[$a]['ST_ATIVO'];
        $DT_DESATIVACAO     = $pes[$a]['DT_DESATIVACAO'];
        $NR_AGENCIA_DIR     = $pes[$a]['NR_AGENCIA_DIR'];
        $NR_BANCO_DIR       = $pes[$a]['NR_BANCO_DIR'];
        $NR_CTA_CORRENTE_DIR = $pes[$a]['NR_CTA_CORRENTE_DIR'];
        $CD_ORIGEM         = $pes[$a]['CD_ORIGEM'];
        $TP_NAT_JURIDICA   = $pes[$a]['TP_NAT_JURIDICA'];
        $FL_ZONAFRANCASOCIAL = $pes[$a]['FL_ZONAFRANCASOCIAL'];
        $query = " select id from  pes_municipio where trim(upper(convert(nm_municipio, 'SF7ASCII'))) = '$TX_CIDADE' ";
        $id_pes_municipio  = $db->banco_query($query, 'unico');
        if ($id_pes_municipio) {
           $reg_logradouro     = Insere_Logradouro($TX_ENDERECO, $id_pes_municipio, '', $NR_CEP);
           if ($saida == '1')  {
              $reg_logradouro .= ';'.PHP_EOL; 
              fwrite($flogra, $reg_logradouro);
              $y++;
              if ($y > 100) { fwrite($flogra, $com); $y = 0; }
           } else { $e = $db->banco_query($reg_logradouro,'sql', $resp); }   
        }  
     }
    if ($saida == '1')  {
       fwrite($flogra, $com);
       fclose($flogra);
    }   
    return $resp;
}

function Insere_Logradouro($rua, $id_pes_municipio, $id_pes_bairro_distrito='', $nr_cep='')  {
    $id_pes_tipo_logradouro = '1'; //  TODO
    $sql = " insert into pes_logradouro  (id , id_pes_municipio, id_pes_tipo_logradouro, nm_logradouro, cep_logradouro, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_logradouro a), $id_pes_municipio, $id_pes_tipo_logradouro, '$rua', '$nr_cep', current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
}


 function monta_endereco($end)  {
    $num    = numero($end);
    $end1   = substr($end,0, ($num -1));
    $resto  = substr($end, $num);
    $fn     = strpos($resto, ' ');
    $end2   = substr($resto, 0, $fn);
    $bairro = substr($resto, (strlen($end2)));
 //   $resp->alert($pes['ID'].' Rua:  '.$end1.'  Numero:  '.$end2.' -  Bairro:  '.$bairro.'    -    '.$pes['ENDER'].'</p>');
     return array($end1, $end2, $bairro);
 }

 function monta_endereco_rodovia($end)  {
    $num    = numero($end);
    $end1   = substr($end,0, ($num -1));
    $resto  = substr($end, $num);
    $fn     = strpos($resto, ' ');
    $end2   = substr($resto, 0, $fn);
    $bairro = substr($resto, (strlen($end2)));
 //   $resp->alert($pes['ID'].' Rua:  '.$end1.'  Numero:  '.$end2.' -  Bairro:  '.$bairro.'    -    '.$pes['ENDER'].'</p>');
     return array($end1, $end2, $bairro);
 }


function numero($string) {
$count = strlen($string);
$i = 0;
while( $i < $count ) {
if( ctype_digit($string[$i]) ) {
  return $i;
}
$i++;
}
return $i;
}



function pes_bairro_distrito($banco, $resul, $saida) {
     $resp = new xajaxResponse();
     $result = unserialize($resul);
     $resp->alert($banco.' - Falta implementar');
     return $resp;
}

function pes_endereco($banco, $resul, $saida) {
     $resp = new xajaxResponse();
     $pes  = unserialize($resul);
     $db = new banco_dados($banco);
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_endereco = $dirx.'\\PES_ENDERECO.sql';
        $pes_endereco = str_replace("\\", "//", $pes_endereco);
        $fpesen      = fopen($pes_endereco, "w+");
     }
     $err = 0;
     $com = 'commit;'.PHP_EOL;
     for ($a = 0; $a < count($pes); $a++)   {
        $NR_CPF_CGC      = $pes[$a]['NR_CPF_CGC'];
        $TP_FAVORECIDO_D = $pes[$a]['TP_FAVORECIDO_D'];
        $NM_FAVORECIDO   = str_replace("&","e", $pes[$a]['NM_FAVORECIDO']);
        $TX_ENDERECO     = $pes[$a]['TX_ENDERECO'];
        $NR_TELEFONE     = $pes[$a]['NR_TELEFONE'];
        $TX_CIDADE       = $pes[$a]['TX_CIDADE'];
        $NR_BANCO        = $pes[$a]['NR_BANCO'];
        $NR_AGENCIA      = $pes[$a]['NR_AGENCIA'];
        $SG_UF           = $pes[$a]['SG_UF'];
        $NR_CTA_CORRENTE = $pes[$a]['NR_CTA_CORRENTE'];
        $NR_CEP          = $pes[$a]['NR_CEP'];
        $NR_INSCR_MUNICIPAL = $pes[$a]['NR_INSCR_MUNICIPAL'];
        $NR_INSC_ESTADUAL  = $pes[$a]['NR_INSC_ESTADUAL'];
        if (!$NR_INSC_ESTADUAL) { $NR_INSC_ESTADUAL = ' ';}
        $TP_RAMO_ATIV_D     = $pes[$a]['TP_RAMO_ATIV_D'];
        $IN_SUSPENSAO_D     = $pes[$a]['IN_SUSPENSAO_D'];
        $UG_CD_UG_SUSPEN    = $pes[$a]['UG_CD_UG_SUSPEN'];
        $IN_COLETIVO_D      = $pes[$a]['IN_COLETIVO_D'];
        $IN_EVENTUAL_D      = $pes[$a]['IN_EVENTUAL_D'];
        $ST_ATIVO           = $pes[$a]['ST_ATIVO'];
        $DT_DESATIVACAO     = $pes[$a]['DT_DESATIVACAO'];
        $NR_AGENCIA_DIR     = $pes[$a]['NR_AGENCIA_DIR'];
        $NR_BANCO_DIR       = $pes[$a]['NR_BANCO_DIR'];
        $NR_CTA_CORRENTE_DIR = $pes[$a]['NR_CTA_CORRENTE_DIR'];
        $CD_ORIGEM         = $pes[$a]['CD_ORIGEM'];
        $TP_NAT_JURIDICA   = $pes[$a]['TP_NAT_JURIDICA'];
        $FL_ZONAFRANCASOCIAL = $pes[$a]['FL_ZONAFRANCASOCIAL'];
        $qp = " SELECT ID  FROM PES_PESSOA_JURIDICA WHERE CNPJ = '$NR_CPF_CGC'
          UNION
                SELECT ID  FROM PES_PESSOA_FISICA WHERE CPF = '$NR_CPF_CGC' ";
        $id_pes_pessoa = $db->banco_query($qp, 'unico'); 
        $qm = " select id from  pes_municipio where trim(upper(convert(nm_municipio, 'SF7ASCII'))) = '$TX_CIDADE' ";
        $id_pes_municipio  = $db->banco_query($qm, 'unico');
        if ($id_pes_municipio) {
           $ql = " select id from  pes_logradouro a where id_pes_municipio = $id_pes_municipio and a.nm_logradouro like '$TX_ENDERECO' ";
          $id_pes_logradouro = $db->banco_query($ql, 'unico');
          $reg_ender = '';
          if ($id_pes_municipio && $id_pes_pessoa && $id_pes_logradouro) {
             $reg_ender    = insere_endereco($id_pes_pessoa, $id_pes_municipio, $id_pes_logradouro);
            if ($saida == '1')  {
               $reg_ender .= ';'.PHP_EOL; 
               fwrite($fpesen, $reg_ender);
              $y++;
              if ($y > 100) { fwrite($fpesen, $com); $y = 0; }
             } else { $e = $db->banco_query($reg_ender,'sql', $resp); }   
 
       } else { echo ('Erro! : '.$reg_ender.' - '.$id_pes_municipio.' - '.$id_pes_logradouro.' - '.$id_pes_pessoa.$qp.'/'.$ql.'/'.$qm.'</p>'); // break;  
        }
      } 
     if ($saida == '1')  {
       fwrite($fpesen, $com);
       fclose($fpesen);
     }   
     return $err;
}

function insere_endereco($id_pes_pessoa, $id_pes_municipio, $id_pes_logradouro) {
    $sql = " insert into pes_endereco  (id , id_pes_pessoa, id_pes_logradouro, nr_imovel, dt_ativo_de, tp_endereco, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_endereco a), $id_pes_pessoa, $id_pes_logradouro, 0, current_timestamp, 3, current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
}

     return $resp;
}

function pes_fornecedor($banco, $resul, $saida) {
     $resp = new xajaxResponse();
     $pes  = unserialize($resul);
     $db = new banco_dados($banco);
     if ($saida == '1')  {
        $dirx       = realpath('Scripts_Oracle/');
        $pes_fornecedor = $dirx.'\\PES_FORNECEDOR.sql';
        $pes_fornecedor = str_replace("\\", "//", $pes_forncecedor);
        $fpesf      = fopen($pes_fornecedor, "w+");
     }
  //   $query = " select * from fornec where rownum < 10 ";
     //a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b) ";
  //   $pes = $db->banco_query($query, 'array', $resp);
     $total = count($pes);
     $err = 0;
     $i = 0; 
     $z = 0;
     $com = 'commit;'.PHP_EOL;
     for ($a = 0; $a < count($pes); $a++)   {
        $NR_CPF_CGC      = $pes[$a]['NR_CPF_CGC'];
        $TP_FAVORECIDO_D = $pes[$a]['TP_FAVORECIDO_D'];
        $NM_FAVORECIDO   = str_replace("&","e", $pes[$a]['NM_FAVORECIDO']);
        $TX_ENDERECO     = $pes[$a]['TX_ENDERECO'];
        $NR_TELEFONE     = $pes[$a]['NR_TELEFONE'];
        $TX_CIDADE       = $pes[$a]['TX_CIDADE'];
        $NR_BANCO        = $pes[$a]['NR_BANCO'];
        $NR_AGENCIA      = $pes[$a]['NR_AGENCIA'];
        $SG_UF           = $pes[$a]['SG_UF'];
        $NR_CTA_CORRENTE = $pes[$a]['NR_CTA_CORRENTE'];
        $NR_CEP          = $pes[$a]['NR_CEP'];
        $NR_INSCR_MUNICIPAL = $pes[$a]['NR_INSCR_MUNICIPAL'];
        $NR_INSC_ESTADUAL  = $pes[$a]['NR_INSC_ESTADUAL'];
        $TP_RAMO_ATIV_D     = $pes[$a]['TP_RAMO_ATIV_D'];
        $IN_SUSPENSAO_D     = $pes[$a]['IN_SUSPENSAO_D'];
        $UG_CD_UG_SUSPEN    = $pes[$a]['UG_CD_UG_SUSPEN'];
        $IN_COLETIVO_D      = $pes[$a]['IN_COLETIVO_D'];
        $IN_EVENTUAL_D      = $pes[$a]['IN_EVENTUAL_D'];
        $ST_ATIVO           = $pes[$a]['ST_ATIVO'];
        $DT_DESATIVACAO     = $pes[$a]['DT_DESATIVACAO'];
        $NR_AGENCIA_DIR     = $pes[$a]['NR_AGENCIA_DIR'];
        $NR_BANCO_DIR       = $pes[$a]['NR_BANCO_DIR'];
        $NR_CTA_CORRENTE_DIR = $pes[$a]['NR_CTA_CORRENTE_DIR'];
        $CD_ORIGEM         = $pes[$a]['CD_ORIGEM'];
        $TP_NAT_JURIDICA   = $pes[$a]['TP_NAT_JURIDICA'];
        $FL_ZONAFRANCASOCIAL = $pes[$a]['FL_ZONAFRANCASOCIAL'];
        $qp = " SELECT ID  FROM PES_PESSOA_JURIDICA WHERE CNPJ = '$NR_CPF_CGC'
          UNION
                SELECT ID  FROM PES_PESSOA_FISICA WHERE CPF = '$NR_CPF_CGC' ";
        $id_pes_pessoa = $db->banco_query($qp, 'unico', $resp); 
        $qm = " select id from  pes_endereco where id_pes_pessoa = $id_pes_pessoa ";
        $id_pes_endereco  = $db->banco_query($qm, 'unico', $resp);
        $reg_fornec = '';
        if ($id_pes_pessoa && $id_pes_endereco) {
           $reg_fornec    = insere_fornecedor($id_pes_pessoa, $id_pes_endereco);
           if ($saida == '1')  {
              $reg_fornec .= ';'.PHP_EOL; 
              fwrite($fpesf, $reg_fornec);
              $y++;
              if ($y > 100) { fwrite($fpesf, $com); $y = 0; }
           } else { $e = $db->banco_query($reg_fornec,'sql', $resp); }   
//           $resp->alert('Aqui '.$e.'-'.$reg_fornec); return $resp;
           if ($e == 1) { $i++; } else  {  
               $err++; 
//               $resp->assign("erros", "innerHTML", print_r($e,true).'-'.$err);
               send_message(0, $e.'-'.$err,0);
//               return $resp;
           }
           $p = floor($i*100/$total);   //Progress
           if ($p > 0 && $p > $z) { 
               $z = $p;
               $resp->assign("progressor", "innerHTML", $serverTime, $p . '% . Regs.: ' . $i, $p); 
           }
        } 
      } 
      if ($saida == '1')  {
         fwrite($fpesf, $com);
         fclose($fpesf);
      }
      $resp->alert('Terminado. Erros: '.$err);
      return $resp;
}

function send_message($message, $ind=0) {
    $d = array('message' => $message , 'ind' => $ind);
     echo json_encode($d) . PHP_EOL;
  
    //PUSH THE data out by all FORCE POSSIBLE
    ob_flush();
    flush();
}

function insere_fornecedor($id_pes_pessoa, $id_pes_endereco) {
    $sql = " insert into pes_fornecedor  (id , id_pes_pessoa, id_pes_endereco, TXT_OBSERVACAO, nr_versao, dt_criado_em, cod_usu_criado)  
       values((select nvl((max(a.id) + 1),1) from pes_fornecedor a), $id_pes_pessoa, $id_pes_endereco, null, 0, current_timestamp, 1) ";
//       $e = $db->banco_query($sql, 'sql', $resp);
   return $sql;
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
*/