<?php
   Header("Cache-control: private, no-cache");
   Header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
   ini_set('date.timezone', 'America/Sao_Paulo');
   ini_set("memory_limit",-1);
   ini_set('default_charset','UTF-8');
   ini_set('display_errors', true);
// $header = Header("Pragma: no-cache");
//   select prdbascodint, prdbastpcodint, prdbasnomcoml, prdregnomtec, prdregnroreg, prdbasrestrito from produto;
   error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
//   error_reporting(E_ALL & ~(E_NOTICE));
   require_once("xajax/xajax_core/xajax.inc.php");
   $xajax = new xajax();
   // $xajax->configure('debug',true);
   $xajax->configure( 'errorHandler', true );
   $xajax->configure( 'logFile', 'xajax_error_log.log' );  
   $xajax->register(XAJAX_FUNCTION,"Tela");
   $xajax->register(XAJAX_FUNCTION,"mostra_erro");
   $xajax->register(XAJAX_FUNCTION,"mostra_tran");
   $xajax->register(XAJAX_FUNCTION,"Exclui");
   $xajax->processRequest();
   $xajax->configure('javascript URI','xajax/');
?>
<html>
<head>
    <title>Visualizar Arquivos de Log</title>
    <style type="text/css">
     html { font: small/1.4 "Lucida Grande", Tahoma, sans-serif;    }
    </style>
    <link href="css/style_menu.css" rel="stylesheet" type="text/css" />
    <link href="css/main.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <!-- Our Custom CSS -->
    <link href="css/style_menu.css" rel="stylesheet" type="text/css" />
    <link href="css/main.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="css/nucleo.css">
    <script src="https://use.fontawesome.com/b2f0b70c9f.js"></script>
    <script  type="text/javaScript" src="js/jquery.min.js"></script>
    <script type="text/javaScript"  src="js/bootstrap.min.js"></script>
 <?php $xajax->printJavascript('xajax'); ?>
</head>
<body>
<body>
    <form id="tela" name="tela" method="POST">
       <div class="container-fluid">
          <div class="col-sm-12"> 
           <div class="row">
             <div class="col-sm-6">
               <div id="tela_erro" class="fundo"></div>
             </div>
             <div class="col-sm-6">
               <div id="tela_trans" class="fundox"></div> 
             </div> 
          </div>
        </div>  
        <div class="col-sm-12"> 
          <div class="row">
             <div class="col-sm-6">
               <div id="log_erro" class="fundo"></div>
             </div>
             <div class="col-sm-6">
               <div id="log_trans" style="border: 2px inset;" class="fundox"></div> 
             </div> 
          </div>
      </div>
     </div> 
    </form>    
    <script type="text/javaScript">xajax_Tela();</script>
</body>
</html>

<?php
function Tela() {
    $resp = new xajaxResponse();
    // metadados tabelas
    $lista_erro   = array();
    $lista_tran   = array();
    foreach(new DirectoryIterator('./log_erros') as $file)  {
        if (substr($file,0,1) <> '.')  {
            $lista_erro[] .= $file; 
        }
    }
    foreach(new DirectoryIterator('./log_trans') as $file)  {
        if (substr($file,0,1) <> '.')  {
            $lista_tran[] .= $file; 
        }
    }
    $tela  = '<div><h5>Log Erros #'.count($lista_erro).'</h5>';
        for ($i = 0; $i < count($lista_erro); $i++) {
            $arq    =  $lista_erro[$i];
            $arqx  = realpath('log_erros/').'\\'.$arq;
            $arqx  = str_replace("\\", "//", $arqx);
//            $resp->alert($arqx.' aqui'); return $resp;
            $tela .= '<input type="button"  onclick="xajax_mostra_erro(\''.$arqx.'\'); return false;" value="'.$arq.'"><input type="image" onclick="xajax_Exclui(\''.$arqx.'\'); return false;" style="vertical-align: bottom;" height="24" width="24" src="img/lixeira.png"></p>';
        }
        $tela .= '</div>';

        $resp->assign("tela_erro","innerHTML", $tela);

        // Tela Json
        // sql 

        $telax = '<div><h5>Logs de Transação #'.count($lista_tran).'</h5><div id="Gerado"></div>';
        for ($i = 0; $i < count($lista_tran); $i++) {
             $arq  =  $lista_tran[$i];
            $dirx   = realpath('log_trans/');
            $arq1  =  '\\'.$arq;
            $arqx  = $dirx.$arq1;
            $arqx  = str_replace("\\", "//", $arqx);
             $telax .= '<input type="button"  onclick="xajax_mostra_tran(\''.$arqx.'\'); return false;" value="'.$arq.'"><input type="image"  onclick="xajax_Exclui(\''.$arqx.'\'); return false;" style="vertical-align: bottom;" height="24" width="24" src="img/lixeira.png"></p>';
        }
        $telax .= '</div>';
        $resp->assign("tela_trans","innerHTML", $telax);
        // inserts 
       return $resp;
}

function mostra_erro($arqx) {
    $resp = new xajaxResponse();
    if(is_file($arqx)) { $text = file($arqx); } else { $resp->alert('Arquivo '.$arqx.' Não encontrado.'); return $resp; }
    $tela = '<b>'.basename($arqx).' : </b>';
    foreach ($text as $lin) {
      $tela .= $lin.'</p>';
    }
    $resp->assign("log_erro", "innerHTML", $tela);
    return $resp;
}

function mostra_tran($arqx) {
    $resp = new xajaxResponse();
    if(is_file($arqx)) { $text = file($arqx); } else { $resp->alert('Arquivo '.$arqx.' Não encontrado.'); return $resp; }
    $tela = '<b>'.basename($arqx).' : </b>';
    foreach ($text as $lin) {
      $tela .= $lin.'</p>';
    }
//    $resp->alert('Aqui '.$arqx.'-'.count($text)); return $resp;
    $resp->assign("log_trans", "innerHTML", $tela);
    return $resp;
}

function Exclui($arqx) {
    $resp = new xajaxResponse();
    if(is_file($arqx)) {
       unlink($arqx);
    } else { 
      $resp->alert('Arquivo '.$arqx.' Não encontrado.'); return $resp; 
    }
    $resp->redirect($_SERVER['PHP_SELF']);
    return $resp;
 }   
