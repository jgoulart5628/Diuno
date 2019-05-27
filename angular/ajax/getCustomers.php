<?php
include('../../inc/banco_pdo_class.php');

$db = new dataBase('ORACLE_semear');
$query="select  razao_social, nome_fantasia, endereco, bairro, cep_cliente, e_mail, codigo_barra  from  clientes where rownum  < 100 ";
$resul =  $db->banco_query($query,'array');
# JSON-encode the response
$json_response = json_encode($resul);

// # Return the response
echo $json_response;
?>
