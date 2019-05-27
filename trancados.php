<?php
Header("Cache-control: private, no-cache");
Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
error_reporting(E_ALL);
ini_set('date.timezone', 'America/Sao_Paulo');
ini_set("memory_limit",-1);
ini_set('default_charset','UTF-8');
ini_set('display_errors',TRUE);
session_start();
require_once("inc/banco_pdo_class.php");
$db = new banco_dados('ORACLE_system');
require_once("xajax/xajax_core/xajax.inc.php");

$xajax = new xajax();
$xajax->register(XAJAX_FUNCTION, "KILL");
$xajax->register(XAJAX_FUNCTION, "Tela");
$xajax->processRequest();
$xajax->configure('javascript URI','xajax/');
?>
<html>
<head>
<title> Trancados (Lock) </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<? $xajax->printJavascript('xajax'); ?>
</HEAD>
<body>
   <div id="tela_lista"></div>
   <script type="text/javaScript">xajax_Tela() </script>
</body>
<?php
function Tela()   {
   global $db;
   $resp= new xajaxResponse();
   $tela = '<TABLE  BORDER=1 width=100% CELLSPACING=0 CELLPADING=0>
                <TR ALIGN="center" bgcolor="#FFFF81">
                 <TH COLSPAN="9" NOWRAP><STRONG><BIG>Transações em Lock</BIG></STRONG></TH>
                </TR>
                <TR ALIGN="center" bgcolor="#FFFF81">
                   <TH> Terminal </TH>
                   <TH> Usuário </TH>
                   <TH> Processo</TH>
                   <TH> Tabela </TH>
                   <TH> Programa</TH>
                   <TH> Situação</TH>
                   <TH> Tipo de Lock </TH>
                   <TH> Sessão</TH>
                 </TR>';
    $query = "Select OS_USER_NAME
		       ,ORACLE_USERNAME
		       ,SPID
		       ,b.OBJECT_NAME
		       ,C.PROGRAM
                       ,NVL(LOCKWAIT,'ACTIVE') as SITUACAO
		       ,DECODE (LOCKED_MODE, 2,'ROW SHARE', 3,'ROW EXCLUSIVE',4,'SHARE',5,'SHARE ROW EXCLUSIVE',6,'EXCLUSIVE', 'UNKNOWN') as TIPO
		       ,(C.SID||','||C.SERIAL#) as SESSAO 
        	  from   SYS.V_\$LOCKED_OBJECT A
                        ,sys.dba_objects b
	                ,SYS.V_\$SESSION C
	                ,SYS.V_\$PROCESS D
         	where  A.object_id = b.object_id
                  and  c.sid       = a.session_id
                  and  c.paddr     = d.addr
                 order by 2, 5";

 $query2 = " select s1.username || '@' || s1.machine ||' Instancia '|| s1.inst_id 
             || ' ( SID=' || s1.sid || ' )  is blocking '
             || s2.username || '@' || s2.machine || ' ( SID=' || s2.sid || ' ) Instancia '||s2.inst_id AS blocking_status
             from gv\$lock l1, gv\$session s1, gv\$lock l2, gv\$session s2
               where s1.sid=l1.sid 
                 and s2.sid=l2.sid
                 and l1.BLOCK=1 and l2.request > 0
                 and l1.id1 = l2.id1
                 and l2.id2 = l2.id2 ";

  $resul = $db->banco_query($query,'array');
  for ($i = 0; $i < count($resul); $i++ ) {
     $terminal  = $resul[$i]["OS_USER_NAME"];
     $usuario   = $resul[$i]["ORACLE_USERNAME"];
     $processo  = $resul[$i]["SPID"];
     $tabela    = $resul[$i]["OBJECT_NAME"];
     $programa  = $resul[$i]["PROGRAM"];
     $situacao  = $resul[$i]["SITUACAO"];
     $tipo      = $resul[$i]["TIPO"];
     $sessao    = $resul[$i]["SESSAO"];
     $tela .= '<TR ALIGN="center",NULL,"center","NULL","NULL","NULL","NULL","NULL","NULL">
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$terminal.'</b></font></TD>
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$usuario.'</b></font></TD>
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$processo.'</b></font></TD>
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$tabela.'</b></font></TD>
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$programa.'</b></font></TD>
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$situacao.'</b></font></TD>
                 <TD ALIGN="center" COLSPAN="1" NOWRAP>'.$tipo.'</b></font></TD>
                 <TD COLSPAN=1 ALIGN="center" NOWRAP><B><FONT face="Verdana" SIZE=2  NOWRAP><form id="dados_encerra_'.$i.'">
                 <a href="#" class="bluelink" style="text-decoration:none;" onclick="xajax_KILL(\''.$sessao.'\'); return false;";>'.$sessao.' </TD>
	         </TR>';
   }
   $resp->assign("tela_lista","innerHTML",$tela);
   return $resp;     
 }

 function param()  {
    $query = " select ((seq / scat) * 100) as param
                 from (
                   select (select average_wait from v\$system_event where event = 'db file sequential read') as seq
                  ,(select average_wait from v\$system_event where event = 'db file scattered read') as scat
                   from dual
                 ) ";
  // which can be considered as a possible setting for OPTIMIZER_INDEX_COST_ADJ
 }

 function KILL($sessao_k) {
   global $db;
   $resposta = new xajaxResponse();
//   $resposta->addAlert($sessao);
   $query = " alter system kill session '$sessao_k' immediate "; 
   $sql   = $db->banco_query($query,'sql');
   if ($sql !== '2') { 
      $erro = 'Processo encerrado, tecle f5 para Refresh';
      $resposta->alert($erro);
   }   
   return $resposta;
}
