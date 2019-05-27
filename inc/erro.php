<html>
<header>
    <title> Erro </title>
</header>
<body>
<?php
    error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
    //parametros do erro
    $tipo   = urldecode($_GET['tipo']);
    $msg    = urldecode($_GET['msg']);
    $sql    = urldecode($_GET['sql']); 
    //usuario
    if (isset($_SESSION['Deal_usuario'])) {
       $usu    = strtoupper($_SESSION['Deal_usuario']);
    } else { $usu = 'Anônimo'; }   
    //dados da sessao
    $ori   = $_SERVER["HTTP_REFERER"];
    $brw   = $_SERVER["HTTP_USER_AGENT"];
    $ip    = $_SERVER["REMOTE_ADDR"];

    // montando o texto
    $txt    = $sql;

    $from     = "Erro <joao_goulart@jgoulart.eti.br>";
    $to       = "Erro <suporte@dealsw.biz";
    $subj     = "Controle de Erros - DealSW";

    $body     = '<html><head></head><body>
                   <p>O usuário: '.$usu.'
                   <p>Na Máquina: '.$ip.'
                   <p>Usando o Browser: '.$brw.'
                   <p>Página: '.$ori.'
                   <p>Erro: '.$msg.'
                   <p><b>'.$sql.'</b></body></hmtl>';

      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
      $headers .= "From: $from";
       mail( $to, $subj, $body, $headers);
?>
<div style="width:100%; height:50px; background:#FFFF99; vertical-align:top; line-height:60px;" >
    &nbsp;
    <img src="/diuno/img/alerta_erro.gif" width="40px" height="40px"/>
    &nbsp;
    Atenção! <?php echo($tipo);?>
</div>
  <p> <?php echo($msg); ?> </p>
  <p> &nbsp;<?php echo($txt);?> </p>
  <br />
  &nbsp;Suporte Avisado.
  &nbsp;
  <a href="javascript: window.close()" target="_self" >Fechar Tela</a>
</body>
</html>
