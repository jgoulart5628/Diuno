<?php
/**  
UNLOAD TABLE cb_mvto_contabil to 'g:\Bases_Nucleo\SCRIPTS_ORACLE\unload\cb_mvto_contabil.csv' DELIMITED BY ';' ESCAPES ON 

 unload de table no ASA
  dbunload -v -c "DSN=COOP;UID=DBA;PWD=***" -r "C:\Users\Joao\Documents\reload.sql" -d -xx -t "DBA.notaf_ent_impostos" "C:\Users\Joao\Documents\unload"
    Ajax Streaming without polling
*/
//type octet-stream. make sure apache does not gzip this type, else it would get buffered
//header('Content-Type: text/octet-stream');
// header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
ini_set("memory_limit", -1);
// ini_set('default_charset','ISO-8859-1');
ini_set('display_errors',true);
include("inc/banco_pdo_class.php");
$resp = '';
if(!isset($_GET['arq']))   {   echo "Escolha uma Tabela!"; exit;  }
$tabela = $_GET['arq']; 
// $tabela = 'CB_MVTO_CONTABIL'; 
 $db  = new banco_dados('ORACLE_coop');
$db1 = new banco_dados('ODBC_coop');
$db2 = new banco_dados('MYSQL_nucleo');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <head>
       <meta charset="utf-8">
       <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--    <meta charset="iso-8859-1" -->
        <title> DATA_LOAD (TABELAS) </title>
         
        <script src="js/jquery-1.8.1.js"></script>
	<!--[if lt IE 9]>
		<script src="js/respond.min.js"></script>
	<![endif]-->
       <script type="text/javascript" src="../js/bootstrap.js"></script>
  <style>
   table       { margin: 0 auto;  border-collapse: collapse; font-family: Verdana, Times, serif;  } 
  .t_line1      {  background-color: #FFFFFF;  }
  .t_line2      {  background-color: #CCCCCC;  }
  .linha  { text-align: right;  }
  .contar { background-color: red; border: 2px outset; }
  .verde  { background-color: green; }
</style>
</head>
<body>
 <form name="tela" action="post"> 
     <div id="local" class="row"></div> 
 </form> 
</body>
</html>
<?php
  $query = "select rows_origem from converte where tabela = '$tabela'  and empresa = 'coop' ";
  $total = $db2->banco_query($query,'unico');
  $tabx = strtolower($tabela); 
// if($tabx === 'cb_saldo_contabil') { $tabx = 'cb_teste'; }
//   leitura da DDlL no  banco de origem para obter as colunas  paara montar a primeira parte do INSERT
  $qq = " select  t.table_id ntab, c.column_name  campo ,  c.base_type_str tipo,  c.column_id, nulls  nulo  from systabcol c 
                      key join systab t on t.table_id=c.table_id 
                      where upper(t.table_name) =  '$tabela' 
					     order by column_id ";
 $tabs = $db1->banco_query($qq,'array');
$serverTime = time();
$cc =  monta_insert($tabs);
//  ----- 
 // verifica se existe a tabela no DB de destino, se não existir, executa CREATE TABLE 
 // se existe, verifica se as colunas nos DBs de origem e destino são iguais, caso haja diferença, insere aa colunaa faltantes
$query = " select table_name from user_tables where table_name = '$tabela' ";
$unico = $db->banco_query($query,'unico');
if (!$unico) { 
	echo  'Criando TABELA '.$tabela.'!</br>',
	$query = cria_tab($tabs,$tabela); $e = $db->banco_query($query,'sql'); 
	if ($e === 2) {  echo  'TABELA NAO EXISTE NO DESTINO!';   exit;  }
}
$cols_ora = $db->banco_query(" select column_name coluna, column_id  from user_tab_cols where table_name = '$tabela'  order by column_id ",'array');
if (count($tabs) > count($cols_ora))  {
	echo   'Ajustando Tabela '.$tabela.'! </br> ';
	$query = cria_cols($tabs,$cols_ora, $tabela); $e = $db->banco_query($query,'sql');
	if ($e === 2) {    echo  'TABELA NAO CORRIGIDA!  </br>'; exit;  }
	
}
		 
// --------------------------------------------------------------------------------
// Leitura da Tabela
// $dir = 'unload/';
// $qq = " select  table_id  from systab where   upper(table_name) =  '$tabela' ";
// $ntab = $db1->banco_query($qq,'unico');
$dir      = 'D://SCRIPTS_ORACLE//unload//'.strtolower($tabela).'.csv';
$dirsai = 'D://SCRIPTS_ORACLE//unload//'.strtolower($tabela).'.sql';
// $dirsai = 'D://SCRIPTS_ORACLE//unload//'.strtolower($tabela).'.sql';
// $dir = 'D://SCRIPTS_ORACLE//unload//'.strtolower($tabela).'.csv';
//if (!file_exists($dir))  { 
   $busca =  "UNLOAD TABLE $tabela to '$dir' DELIMITED BY '¨' ESCAPES ON ";
   $e = $db1->banco_query($busca,'sql');
//}
$fs = fopen($dirsai,'w+');
if (!$fh = fopen($dir,'r'))  {  echo  'ARQUIVO '.$dir.'  NAO ENCONTRADO!  </br>'; exit;              };
// $query = " select * from $tabx ";
// $res = $db1->banco_query($query, 'array');
//  if (count($res)  === 0)  { 	  send_message($serverTime, 'Sem dados na tabela escolhida!', $total); exit; }
//   $e = $db->banco_query("truncate table $tabela ",'sql');
//   llimpa a tabela desttno para importar os dados 
//  -------------------  Inicia a Carga ------------------------------------
  $linha =  "set define ~  \r\n"; 
  fwrite($fs,$linha);
// Desabilita as Chaves estrangeiras 
$qx = "declare cursor cc is 
                 select 'alter table ' || table_name || ' disable constraint ' || constraint_name  || ' cascade '  as const
                    from user_constraints where table_name = '$tabela' ;
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end; 
		 / 
		 \r\n "; 
  $linha = $qx .  "commit; \r\n"; 
  fwrite($fs,$linha);
  $linha = "delete from  $tabela ; \r\n"; 
  fwrite($fs,$linha);
  $linha = " commit;  \r\n"; 
  fwrite($fs,$linha);
 $i=0; 
 $z = 0;
 $tem_blob = 0; 	 
 while (!feof($fh))  { 
     $dad = fgets($fh, 4096); 
     $dad = str_replace("\r\n","",$dad);
	 $dad = str_replace("x0d", "",$dad);
	 $dad = str_replace("x0a", "",$dad);
	 $data = explode('¨',$dad);
     $z = count($data);	 
     if ($z  > 0)  { $ins = "insert into $tabela   values(";   } else { break;  }
     reset($data);
     $b = 0;
     while (key($data) !== null)   {
            if ($b > 0) { $ins .=  ', '; }
            $tipo = $tabs[$b]['tipo'];
            $nulo = $tabs[$b]['nulo'];
            $dado = current($data);
	         if(!$dado && $nulo = 'Y')  { $dado =  'NULL';   $ins .= $dado; } 
			  else {
            if ((substr($tipo,0,3)  === 'cha') || (substr($tipo,0,3)  === 'var')) {
		        $dado = str_replace('"',' ',$dado); 
		        if(!$dado)  { $dado =  '" "';  }
    	        $dado = str_replace('"' ,"'",$dado);
	            $ins .= $dado;
            }
            if ((substr($tipo,0,3) === 'num') || (substr($tipo,0,3) === 'int') || (substr($tipo,0,3) === 'sma') || (substr($tipo,0,3) === 'dec'))  {
		        $dado = str_replace('"','',$dado); 
		        $dado = str_replace("'",'',$dado);
     	        if (!$dado)   {  $dado = 0;  } 
     	        if (!is_numeric($dado))   {  $dado = 0;  } 
               $ins .= $dado;
            }
			
            if (substr($tipo,0,4) === 'date')  {
		         if(!$dado)  { $dado =  'NULL';   $ins .= $dado; } else {  $ins .= "TO_TIMESTAMP('$dado', 'YYYY-MM-DD HH24:MI')"; } 
            }
			
            if (substr($tipo,0,5) === 'times')  {
		         if(!$dado)  { $dado =  'NULL';   $ins .= $dado;  } else {    $ins .= "TO_TIMESTAMP('$dado', 'YYYY-MM-DD HH24:MI:SS:FF')";  }
            } else {
              if (substr($tipo,0,4) === 'time')  {
  		         if(!$dado)  { $dado =  'NULL';   $ins .= $dado;  } else {   $ins .= "TO_TIMESTAMP('$dado', 'HH24:MI:SS.FF')"; } 
              }
            }
			
            if (substr($tipo,0,4)  === 'long') {
             	if ($tem_blob === 0)  {
                    $ff = fopen('arq_tmp','w'); 
                    fwrite($ff, $dado);
                   fclose($ff);
                   $ins .= 'varq_nfe';
                }   
          	    if ($tem_blob === 1)  {
                   $ff1 = fopen('arq1_tmp','w'); 
                   fwrite($ff1, $dado);
                   fclose($ff1);
                   $ins .= 'varq_canc';
                }   
            $tem_blob = 1; 
           }
		 }  
         $b++;
          next($data); 
       }   
      $ins .= ') ; ';
      if ($tem_blob === 1)  {
	     $dd = '';
		 $dd1 = '';
         if(is_file('arq_tmp'))  { $dd  = file_get_contents('arq_tmp');  }
         if(is_file('arq1_tmp'))  { $dd1 = file_get_contents('arq1_tmp');  }
         $ins1 =  " DECLARE
                     varq_nfe varchar2(32767);
                     varq_canc varchar2(32767);
                    BEGIN
                       varq_nfe :=  '$dd';
                       varq_canc :=  '$dd1';
                    $ins   END; ";  
//          $e  = $db->banco_query($ins1,'sql');
          $linha = $ins1. "\r\n"; 
          fwrite($fs,$linha);
          $tem_blob = 0;
       } else {  
	     //    $e = $db->banco_query($ins,'sql'); 
          $linha = $ins. "\r\n"; 
          fwrite($fs,$linha);
		 } 
		$i++; 
		if ($i === 100)  {   $linha = " Commit; ". "\r\n";    fwrite($fs,$linha); $i =  0;  }
   }

//   reativa as constraints de chave estrangeira
   $qx = "declare cursor cc is 
                 select 'alter table ' || table_name || ' enable constraint ' || constraint_name   as const
                    from user_constraints where table_name = '$tabela' ;
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end;  \r\n
		 /  \r\n "; 
    $linha = $qx .  "commit; \r\n"; 
    fwrite($fs,$linha);
    $linha = $qx .  "exit; \r\n"; 
    fwrite($fs,$linha);
//  sleep(1);
   fclose($fh);
   fclose($fs);
  echo 'COMPLETE';
   exit;


function cria_tab($tabs,$tabela)  {
	$q = " create table $tabela (";
	$a = 0;
	global $db1;
	$pk = " select cname from sys.SYSCOLUMNS 
	        where upper(tname) = '$tabela'
	        and in_primary_key ='Y' ";
	$pks = $db1->banco_query($pk,'array');
	
	foreach($tabs as $tb)  {
		$coluna = $tb['campo'];
		$tipo      = $tb['tipo'];
		$nulo     = $tb['nulo'];
		if ($a > 0) { $q .= ','; }
		$q .= $coluna.' '.$tipo;
		if ($nulo == 'Y')  {  $q  .= ' null';  } else  { $q .= ' not null';  }
		$a++;
	}
	if (is_array($pks))  {
	   $b = 0;
	   $pp = ' primary key (';
	   foreach ($pks as $pk) {
	   	 $col = $pk['cname'];
	   	 $pp .= '$col';
	   	 if ($b > 0) { $pp .= ','; }
	   	 $b++;
	   }
	   $pp .= ')';
	}
 	$q  .=  ')';
	return $q;
}	

function monta_insert($tabs)  {
$cc = " (";
$a = 0;
reset($tabs);
while(key($tabs) !== null)  {
//   echo key($tabs).'</br>';
   $resto = current($tabs);  
    reset($resto);
    while(key($resto) !== null) {
//        echo key($tabs).' - '.key($resto).'  - '.current($resto).'</br>';
        if (key($resto) === 'campo') {
            if ($a > 0) { $cc .=  ', '; }
            $cc .= current($resto);
            $a++;   
        }
        next($resto);
    }
  next($tabs);
}
 $cc .= ') '; 
  return $cc;
}

function cria_cols($tabs,$cols_ora, $tabela)  {
	foreach($cols_ora as $cols)  {
		$colu[] = $cols['COLUNA'];
	}
	
	$q = " alter table $tabela add (";
	$a = 0;
	foreach($tabs as $tb)  {
		$coluna = strtoupper($tb['campo']);
		$tipo      = $tb['tipo'];
		$nulo     = $tb['nulo'];
		if(!in_array($coluna, $colu)) {
          if ($a > 0) { $q .= ','; }
		  $q .= $coluna.' '.$tipo;
		  if ($nulo == 'Y')  {  $q  .= ' null';  } else  { $q .= ' not null';  }
		  $a++;
		}  
	}
	$q .= ')';
	return $q;
}
