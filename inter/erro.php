<?php
?>
<html>
<head>
    <title> Erro </title>
</head>
<body>
<?php

    //parametros do erro
    $tipo   = urldecode($_GET['tipo']);
    $msg    = urldecode($_GET['msg']);
    $sql    = urldecode($_GET['sql']);
    $ori   = $_SERVER["HTTP_REFERER"];
    $brw   = $_SERVER["HTTP_USER_AGENT"];
    $ip    = $_SERVER["REMOTE_ADDR"];

    $txt    = $sql;

      $from     = "Erro <goulart.joao@gmail.com>";
      $to       = "Erro <goulart.joao@gmail.com>";
      $subj     = "Controle de Erros - Web";

      $body     = '<html><head></head><body>
                   <p>Na Máquina: '.$ip.'
                   <p>Usando o Browser: '.$brw.'
                   <p>Página: '.$ori.'
                   <p>Erro: '.$msg.'
                   <p><b>'.$sql.'</b></body></hmtl>';

      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $headers .= "From: $from";
       mail( $to, $subj, $body, $headers);
?>
<div style="width:100%; height:50px; background:#FFFF99; vertical-align:top; line-height:60px;" >
    &nbsp;
    <img src="img/alerta_erro.gif" width="40px" height="40px"/>
    &nbsp;
    Atenção! <?php echo($tipo);?>
</div>
  <p> <?php echo($msg); ?> </p>
  <p> &nbsp;<?php echo($txt);?> </p>
  <br />
  &nbsp;Developer Avisado.
  &nbsp;
  <a href="javascript: window.close()" target="_self" >Fechar Tela</a>
</body>
</html>
