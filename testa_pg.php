<?php
error_reporting(E_ALL);
// error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
ini_set('display_errors', true);
include('inc/banco_pdo_class.php');
$pg   = new banco_dados('POSTGRESQL_pnucleo');
//var_dump($pg);
if ($pg->erro) { var_dump($pg->erro); }
$query = " insert into converte (empresa,tabela, rows_origem, rows_destino) values('coop', 'CLIENTES', 1000, 0) ";
//$query1 = " select * from converte ";
$e = $pg->banco_query($query,'sql');
if ($pg->erro) { var_dump($pg->erro); }
//$res = $pg->banco_query($query1,'array');
var_dump($e);
