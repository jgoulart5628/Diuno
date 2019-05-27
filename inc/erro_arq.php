 <?php
    $arq = realpath('Scripts_Oracle/').'\\erro_sql.txt';
    error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
    $fs = @fopen($arq, 'w+');
    //parametros do erro
    $tipo   = urldecode($_GET['tipo']);
    $msg    = urldecode($_GET['msg']);
    $sql    = urldecode($_GET['sql']); 
    //usuario
    //dados da sessao
    $ori   = $_SERVER["HTTP_REFERER"];
//    $brw   = $_SERVER["HTTP_USER_AGENT"];
    $ip    = $_SERVER["REMOTE_ADDR"];
    // montando o texto
    $txt    = $ori.' - '.$ip.' - '.$tipo.'-'.$msg.' - '.$sql.PHP_EOL;
    frwite($fs, $txt);
    fclose($fs);
