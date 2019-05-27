<?php
include('inc/adodb5/adodb.inc.php');
include('inc/adodb5/adodb-exceptions.inc.php');
session_start(); # session variables required for monitoring
$link     = NewADOConnection("oci8");
$link->LogSQL(true);
$dbhost   = 'nucleo';
$dbuser = 'system';
$dbpasswd = 'nucleo';
$link->Connect($dbhost, $dbuser, $dbpasswd) or die("<h1><font color='red'>Não conectou banco de dados, chamar o Joao.</font></h1>");
$perf =& NewPerfMonitor($link);
$perf->UI($pollsecs=5);
?>
