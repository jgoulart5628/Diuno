<?php
/**  
 unload de table no ASA
  dbunload -v -c "DSN=COOP;UID=DBA;PWD=***" -r "C:\Users\Joao\Documents\reload.sql" -d -xx -t "DBA.notaf_ent_impostos" "C:\Users\Joao\Documents\unload"
    Ajax Streaming without polling
*
*
    declare cursor cc is 
 select 'alter table ' || xx.tabela || ' enable constraint ' || zz.constraint_name || ';'    as const from
 (select table_name tabela from user_tables) xx, user_constraints zz
where zz.table_name = xx.tabela
 and zz.constraint_type in ('C', 'P')
 and zz.status  =  'DISABLED';
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end; 

// disable foreign keys de outras tabelas.

      select zz.table_name, zz.constraint_name fk from (
select  constraint_name  chave 
  from user_constraints where table_name = 'NOTAF_SAI_BASE' and constraint_type in ('P')  and status = 'ENABLED' )  xx, user_constraints zz
where  xx.chave = zz.r_constraint_name
 and  zz.constraint_type = 'R'
 and status = 'ENABLED' 
   
*/         
function  ajax_stream($arq, $cliente, $conv, $tipo)  {
  $resp = new xajaxResponse();
//type octet-stream. 
  global    $my;
  $tabela  = $arq;
  $cliente = $cliente;
  $conv    = $conv;
  $tp      = $tp;   // 1- direta   2 - Arq CSV, 3 - SQL Insert  4- Atualilza campos long (xml  nf sybase) -5 Insere faltantes 
  if (!$tabela)  {  $resp->alert('Sem tabela escolhida!'); return $resp; }
 // banco MYSQL controle de tabelas a atualizar por cliente
  $query = "select rows_origem from converte where tabela = '$tabela'  and empresa = '$cliente' ";
  $total = $my->banco_query($query,'unico');

//  ---------------------------------------------------
//  Escolha da conexão
  switch ($conv)  {
	     case "1":    $entra  =   'ODBC_'.$cliente;						  
                     $saida  =  'ORACLE_'.$cliente;						  
                     break;
	    case "2":    $entra  =   'ODBC_'.$cliente;						  
                     $saida  =  'ODBC_'.$cliente;						  
					 break;
	    case "3":    $entra  =   'FIREBIRD_'.$cliente;						  
                     $saida  =  'ORACLE_'.$cliente;						  
					 break;
	    case "4":    $entra  =   'FIREBIRD_'.$cliente;						  
                     $saida  =  'ODBC_'.$cliente;						  
					 break;
   }	

//$msg =  $entra . ' - ' . $saida .' - ' .$total.'-'.$tipo.'-'.$conv; 
//send_message(0, $msg, 0); 

  $sai  = new banco_dados($saida);  
  if ($sai->erro)  {
     $msg = 'Não conectou '.$saida.'  Verifique.'.$sai->erro;
     $resp->alert($msg); 
	   return $resp;  
  }
  $en = new banco_dados($entra); 
  if ($en->erro)  { 
     $msg = 'Não conectou '.$entra.'  Verifique.'.$en->erro;
     $resp->alert($msg); 
     return $resp;   
  }
//$msg =  $entra . ' - ' . $saida .' - ' .$en->erro; 
//send_message(0, $msg, 0); 
// Sybase para Oracle include conforme a opção
  switch ($conv)   {
     case '1':  
              switch ($tp)  {
                case '1':  require_once('load_dados_direto_sybase.php');
                case '2':  require_once('load_dados_csv_sybase.php');
                case '3':  require_once('gera_sql_insert_sybase.php'); 
                case '4':  require_once('update_nf_sybase.php');
                           update_nf_sybase($tabela, $cliente,$en,$sai);
                case '5':  require_once('load_dados_falta_sybase.php');
                default:
                       if($tp !== '4')  {  SYBASE_ORACLE($tabela, $en, $sai);     }    

              }
      case '3': require_once('load_dados_direto_fb_ora.php');
                FIREBIRD_ORACLE($tabela,$en, $sai);
      case '4': require_once('load_dados_direto_fb_sql.php'); 
                FIREBIRD_SQLSERVER($tabela,$en, $sai);
  } // end switch $conv
  $mesg = date('G:i:s').' Concluido!';
  $resp->assign("progressor", "value", $mesg); 
//document.getElementById('progressor').style.width = result.progress + "%";
  return $resp;
}

function  SYBASE_ORACLE($tabela,$en, $sai)    {
  global $total;
	global $tp;
	global $conv;
  $tabs = DDL_SYBASE($tabela,$en);
  $cc =  monta_insert_oracle($tabs,$tp);
  CRIA_TABELAS_ORACLE($tabela,$tabs,$conv,$sai);
  if($tp  !== '3')   {  disable_constraints_ora($tabela, $sai);  }
	if($tp  === '1')   {  load_dados_direto_sybase($tabela, $tabs, $cc, $total,$sai);  }
	if($tp  === '2')   {  load_dados_csv_sybase($tabela, $tabs, $cc, $total, $sai);  }
	if($tp  === '3')   {  gera_sql_insert_sybase($tabela, $tabs, $cc, $total, $sai);  }
	if($tp  === '5')   {  load_dados_falta_sybase($tabela, $tabs, $cc, $total, $sai);  }
  if($tp  !== '3')   {  enable_constraints_ora($tabela,$sai);  }
}

function  FIREBIRD_ORACLE($tabela, $en, $sai)    {
    global $total;
  	global $tp;
 	  global $conv;
    $tabs = DDL_FIREBIRD($tabela,$en);
    CRIA_TABELAS_ORACLE($tabela,$tabs,$conv);
	  disable_constraints_ora($tabela,$sai);
	  load_dados_direto_fb_ora($tabela, $tabs, $total,$sai); 
	  enable_constraints_ora($tabela,$sai);
}

function  FIREBIRD_SQLSERVER($tabela)    {
    global $total;
  	global $tp;
    $tabs = DDL_FIREBIRD($tabela);
    CRIA_TABELAS_SQLSERVER($tabela,$tabs,$sai);
	  if($tp  === '1')  {  load_dados_direto_fb_sql($tabela, $tabs, $total,$sai);  }
}
 
  
 function DDL_SYBASE($tabela, $en)   {
   //   leitura da DDlL no  banco de origem para obter as colunas  paara montar a primeira parte do INSERT
  $qq = " select cname campo, coltype tipo, length tam, syslength decx, nulls nulo, in_primary_key pk 
           from sys.SYSCOLUMNS  where upper(tname) = '$tabela' order by colno ";
/*  
  select  c.column_name  campo ,  c.base_type_str tipo,  c.column_id, nulls  nulo  from systabcol c 
                      key join systab t on t.table_id=c.table_id 
                      where upper(t.table_name) =  '$tabela' 
					     order by column_id ";
*/
  $tabs = $en->banco_query($qq,'array');
  return $tabs;
}


function DDL_FIREBIRD($tabela,$en)   {
   $qq = " SELECT r.RDB\$FIELD_NAME AS campo,
                  CASE f.RDB\$FIELD_TYPE
                     WHEN 261 THEN 'BLOB'
                     WHEN 14 THEN 'CHAR'
                     WHEN 40 THEN 'CSTRING'
                     WHEN 11 THEN 'D_FLOAT'
                     WHEN 27 THEN 'DOUBLE'
                     WHEN 10 THEN 'FLOAT'
                     WHEN 16 THEN 'INT64'
                     WHEN 8 THEN 'INTEGER'
                     WHEN 9 THEN 'QUAD'
                     WHEN 7 THEN 'SMALLINT'
                     WHEN 12 THEN 'DATE'
                     WHEN 13 THEN 'TIME'
                     WHEN 35 THEN 'TIMESTAMP'
                     WHEN 37 THEN 'VARCHAR'
                  ELSE 'UNKNOWN'
                END AS tipo,
                r.RDB\$NULL_FLAG as nulo,				
				f.RDB\$FIELD_LENGTH as tamx,
                f.RDB\$FIELD_PRECISION as tamn,
 				f.RDB\$FIELD_SCALE as decx
             FROM RDB\$RELATION_FIELDS r 
              LEFT JOIN RDB\$FIELDS f ON r.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
                    WHERE r.RDB\$RELATION_NAME='$tabela'
                   ORDER BY r.RDB\$FIELD_POSITION ";
    $tabs = $en->banco_query($qq,'array');
    return $tabs;
 }

 
function send_message($id, $message, $progress) {
    $d = array('message' => $message , 'progress' => $progress);
     echo json_encode($d) . PHP_EOL;
    
    //PUSH THE data out by all FORCE POSSIBLE
    ob_flush();
    flush();
}

 function enable_constraints_ora($tabela,$sai)   {  
   //   reativa as constraints de chave estrangeira
   $query = " select aa.table_name tabela
             , aa.constraint_name fk
            from user_constraints aa
           where aa.table_name = '$tabela'
            and aa.constraint_type = 'R'
            and aa.status = 'DISABLED'
           union all  
         select  (select z.table_name from User_Constraints z where aa.r_constraint_name = z.constraint_name) tab_r  
                 ,  aa.r_constraint_name con_r
                 from user_constraints aa
                where aa.table_name = '$tabela'
                  and aa.constraint_type = 'R'
                  and aa.status = 'DISABLED' ";
     $res = $sai->banco_query($query,'array'); 
     if (count($res)  > 0)  {
         foreach ($res as $ch)  {
            $tab =  $ch['TABELA'];
            $fk  =  $ch['FK'];
            $qx  =  "alter table $tab modify constraint $fk enable novalidate "; 
            $e   =  $sai->banco_query($qx, 'sql');
            if ($sai->erro)  {  send_message(0,$sai->erro,0); }
         }
     }
   /*
   $qx = " declare cursor cc is 
                 select 'alter table ' || table_name || ' enable constraint ' || constraint_name  as const
                    from user_constraints where table_name = '$tabela' and constraint_type in ('C','R') and status = 'DISABLED' ;
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end; "; 
     $e = $sai->banco_query($qx,'sql');		 
     */
 } 

function disable_constraints_ora($tabela,$sai)  {
   //  -------------------  Inicia a Carga ------------------------------------
   // Desabilita as Chaves estrangeiras 
   $query = " select aa.table_name tabela
             , aa.constraint_name fk
            from user_constraints aa
           where aa.table_name = '$tabela'
            and aa.constraint_type = 'R'
            and aa.status = 'ENABLED'
           union all  
         select  (select z.table_name from User_Constraints z where aa.r_constraint_name = z.constraint_name) tab_r  
                 ,  aa.r_constraint_name con_r
                 from user_constraints aa
                where aa.table_name = '$tabela'
                  and aa.constraint_type = 'R'
                  and aa.status = 'ENABLED' ";
     $res = $sai->banco_query($query,'array'); 
     if (count($res)  > 0)  {
         foreach ($res as $ch)  {
            $tab =  $ch['TABELA'];
            $fk  =  $ch['FK'];
            $qx  =  "alter table $tab modify  constraint $fk  disable novalidate "; 
            $e   =  $sai->banco_query($qx, 'sql');
            if ($sai->erro)  {  send_message(0,$sai->erro,0); }
         }
     }
 /*
   $qx = " declare cursor cc is 
                 select 'alter table ' || table_name || ' disable constraint ' || constraint_name  || '  cascade '   as const
                    from user_constraints where table_name = '$tabela' and constraint_type in ('C','R') and status = 'ENABLED' ;
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end; "; 
    $e = $sai->banco_query($qx,'sql');
    */
}

function CRIA_TABELAS_ORACLE($tabela,$tabs,$conv,$sai)   {
   // verifica se existe a tabela no DB de destino, se não existir, executa CREATE TABLE 
   // se existe, verifica se as colunas nos DBs de origem e destino são iguais, caso haja diferença, insere aa colunaa faltantes
   $query = " select table_name from user_tables where table_name = '$tabela' ";
   $unico = $sai->banco_query($query,'unico');
   if (!$unico) { 
	   send_message(0, 'Criando TABELA '.$tabela.'!', 0);
	   if($conv === '3') { $query = cria_tab_fb_oracle($tabs,$tabela); }
	   else { $query = cria_tab_syb_oracle($tabs,$tabela); }
       $e = $sai->banco_query($query,'sql');  	   
       if ($e === 2) { send_message(0, 'TABELA NAO EXISTE NO DESTINO!'.$query, 0); exit; }
   }
   $cols_ora = $sai->banco_query(" select column_name coluna, column_id  from user_tab_cols where table_name = '$tabela'  order by column_id ",'array');
   if (count($tabs) > count($cols_ora))  {
	  send_message(0, 'Ajustando Tabela '.$tabela.'!', 0);
	  if($conv === '3') { $query = cria_cols_fb_oracle($tabs,$cols_ora, $tabela); } 
	  else { $query = cria_cols_syb_oracle($tabs,$cols_ora, $tabela); }
	  $e = $sai->banco_query($query,'sql');
	  if ($e === 2) {send_message(0, 'TABELA NAO CORRIGIDA!', 0); exit;  }
   }
 }  


  function CRIA_TABELAS_SQLSERVER($tabela,$tabs,$sai)   {
   //  ----- 
   // verifica se existe a tabela no DB de destino, se não existir, executa CREATE TABLE 
   // se existe, verifica se as colunas nos DBs de origem e destino são iguais, caso haja diferença, insere aa colunaa faltantes
   $query = " select name from sys.tables where name = '$tabela' ";
   $unico = $sai->banco_query($query,'unico');
   if (!$unico) { 
	   send_message(0, 'Criando TABELA '.$tabela.'!', 0);
	   $query = cria_tab_sqlserver($tabs,$tabela);
//	   echo $query; 
 	   $e = $sai->banco_query($query,'sql'); 
       if ($e === 2) { send_message(0, 'TABELA NAO EXISTE NO DESTINO!'.$e, 0); break;  }
   }
   $cols_sql = $sai->banco_query(" select name as coluna, column_id  from sys.columns where object_name(object_id)='$tabela'  order by column_id ",'array');
   	foreach($cols_sql as $cols)  {
		$colu[] = $cols['coluna'];
	}
   if (count($tabs) > count($colu))  {
	  send_message(0, 'Ajustando Tabela '.$tabela.'!', 0);
	  $query = cria_cols_sqlserver($tabs,$colu, $tabela);
	  $e = $sai->banco_query($query,'sql');
	  if ($e === 2) {send_message(0, 'TABELA NAO CORRIGIDA!'.$query, 0); exit;  }
   }
 }  

 
  
function cria_tab_sqlserver($tabs,$tabela,$en)  {
	$q = " create table $tabela (";
	$pk = " SELECT RIS.RDB\$FIELD_NAME as PKNAME
                     FROM RDB\$RELATION_CONSTRAINTS RC
                      JOIN RDB\$INDEX_SEGMENTS RIS
                     ON RIS.RDB\$INDEX_NAME = RC.RDB\$INDEX_NAME
                      WHERE RC.RDB\$RELATION_NAME = '$tabela'
                      AND RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY' ";
	$pks = $en->banco_query($pk,'array');
	print_r($pks);
	$a = 0;
	foreach($tabs as $tb)  {
		$coluna = $tb['CAMPO'];
		$tipo   = trim($tb['TIPO']);
		$nulo   = $tb['NULO'];
		$tamx   = $tb['TAMX'];
		$tamn   = $tb['TAMN'];
		$dec    = $tb['DECX'];
		if($dec < 0)  { $dec = $dec * -1;  }
		switch ($tipo)  {
		    case 'VARCHAR'   :  $tipx = 'varchar('.$tamx.')'; break;
		    case 'CHAR'      :  $tipx = 'varchar('.$tamx.')'; break;
		    case 'INTEGER'   :  $tipx = 'numeric('.$tamn.','.$dec.')';  break;
		    case 'NUMERIC'   :  $tipx = 'numeric('.$tamn.','.$dec.')';  break;
		    case 'SMALLINT'  :  $tipx = 'numeric('.$tamn.','.$dec.')';  break;
            case 'INT64'     :  $tipx = 'numeric('.$tamn.','.$dec.')';  break;
		    case 'BLOB'      :  $tipx = 'varchar(max)';  break;
		    case 'TIMESTAMP' :  $tipx = 'datetime';  break;
    		default:  $tipx = $tipo;  
		}
		if ($a > 0) { $q .= ','; }
		$q .= $coluna.' '.$tipx;
		if ($nulo == '1')  {  $q  .= ' not null';  } else  { $q .= ' null';  }
		$a++;
	}
	if (is_array($pks))  {
	   $b = 0;
	   $pp = ' primary key (';
	   foreach ($pks as $pk) {
	   	 $col = $pk['PKNAME'];
	   	 $pp .= '$col';
	   	 if ($b > 0) { $pp .= ','; }
	   	 $b++;
	   }
	   $pp .= ')';
	}
 	$q  .=  ')';
	return $q;
}	

function cria_cols_sqlserver($tabs,$colu, $tabela)  {
	$q = " alter table $tabela add ";
	$a = 0;
	foreach($tabs as $tb)  {
		$coluna = trim(strtoupper($tb['CAMPO']));
		$nulo   = $tb['NULO'];
		$tipo   = trim($tb['TIPO']);
		$tamx   = $tb['TAMX'];
		$tamn   = $tb['TAMN'];
		$dec    = $tb['DECX'];
		if($dec < 0)  { $dec = $dec * -1;  }
		if(!in_array($coluna, $colu)) {
     		switch ($tipo)  {
              case 'VARCHAR'   :    $tipx = 'varchar('.$tamx.')'; break;
		      case 'CHAR'      :    $tipx = 'varchar('.$tamx.')'; break;
		      case 'INTEGER'   :    $tipx = 'numeric('.$tamn.','.$dec.')';  break;
		      case 'SMALLINT'  :    $tipx = 'numeric('.$tamn.','.$dec.')';  break;
		      case 'INT64'     :    $tipx = 'numeric('.$tamn.','.$dec.')';  break;
		      case 'BLOB'      :    $tipx = 'varchar(max)';  break;
              case 'TIMESTAMP' :    $tipx = 'datetime';  break;
    		  default:  $tipx = $tipo;  
	       }
          if ($a > 0) { $q .= ','; }
		  $q .= $coluna.' '.$tipx;
		  if ($nulo == '1')  {  $q  .= ' not null';  } else  { $q .= '  null';  }
		  $a++;
		}  
	}
	return $q;
}

 
function cria_tab_syb_oracle($tabs,$tabela)  {
	$q = " create table $tabela (";
	$a = 0;
    $pks = '';
	foreach($tabs as $tb)  {
		$coluna    = $tb['campo'];
		$tipo      = trim($tb['tipo']);
		$nulo      = $tb['nulo'];
		$tam       = $tb['tam'];
		$dec       = $tb['decx'];
        $pk        = $tb['pk']; 
		if($pk === 'Y')   {
		  $pks[] = $coluna;
		}
		switch ($tipo)  {
		    case 'char'    :  $tipx = 'varchar2('.$tam.')'; break;
		    case 'varchar' :  $tipx = 'varchar2('.$tam.')'; break;
		    case 'integer' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'double'  :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'numeric' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'smallint' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'long'    :  $tipx = 'clob';  break;
		    case 'time'    :  $tipx = 'timestamp';  break;
    		default:  $tipx = $tipo;  
		}
		if ($a > 0) { $q .= ','; }
		$q .= $coluna.' '.$tipx;
		if ($nulo == 'Y')  {  $q  .= ' null';  } else  { $q .= ' not null';  }
		$a++;
	}
	if(is_array($pks))  {
      $pp  = ', primary key (';
	  for($b = 0; $b < count($pks); $b++) {
         if ($b > 0) { $pp .= ','; }
         $pp .= $pks[$b];
	  }
      $pp .= ')';
	}
 	$q  .=  $pp.')';
	return $q;
}	

function cria_tab_fb_oracle($tabs,$tabela,$en)  {
	$q = " create table $tabela (";
	$pk = " SELECT RIS.RDB\$FIELD_NAME as PKNAME
                     FROM RDB\$RELATION_CONSTRAINTS RC
                      JOIN RDB\$INDEX_SEGMENTS RIS
                     ON RIS.RDB\$INDEX_NAME = RC.RDB\$INDEX_NAME
                      WHERE RC.RDB\$RELATION_NAME = '$tabela'
                      AND RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY' ";
	$pks = $en->banco_query($pk,'array');
	print_r($pks);
	$a = 0;
	foreach($tabs as $tb)  {
		$coluna = $tb['CAMPO'];
		$tipo      = trim($tb['TIPO']);
		$nulo     = $tb['NULO'];
		$tam     = $tb['TAM'];
		$dec     = $tb['DECX'];
		switch ($tipo)  {
		    case 'CHAR'    :  $tipx = 'varchar2('.$tam.')'; break;
		    case 'VARCHAR' :  $tipx = 'varchar2('.$tam.')'; break;
		    case 'INTEGER' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'NUMERIC' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'SMALLINT':  $tipx = 'numeric('.$tam.','.$dec.')';  break;
    	    case 'INT64'   :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'BLOB'    :  $tipx = ' clob ';  break;
    		default:  $tipx = $tipo;  
		}
		if ($a > 0) { $q .= ','; }
		$q .= $coluna.' '.$tipx;
		if ($nulo == '1')  {  $q  .= ' not null';  } else  { $q .= ' null';  }
		$a++;
	}
	$pp = '';
	if (is_array($pks))  {
	   $b = 0;
	   $pp = ', primary key (';
	   foreach ($pks as $pk) {
	   	 $col = $pk['PKNAME'];
	   	 $pp .= '$col';
	   	 if ($b > 0) { $pp .= ','; }
	   	 $b++;
	   }
	   $pp .= $pp.')';
	}
	$q  .=  ')';
	return $q;
}	


function cria_cols_syb_oracle($tabs,$cols_ora, $tabela)  {
	foreach($cols_ora as $cols)  {
		$colu[] = $cols['COLUNA'];
	}
	
	$q = " alter table $tabela add (";
	$a = 0;
	foreach($tabs as $tb)  {
     	$coluna = strtoupper($tb['campo']);
		$tipo      = trim($tb['tipo']);
		$nulo      = $tb['nulo'];
		$tam       = $tb['tam'];
		$dec       = $tb['decx'];
    	switch ($tipo)  {
		    case 'char'    :  $tipx = 'varchar2('.$tam.')'; break;
		    case 'varchar' :  $tipx = 'varchar2('.$tam.')'; break;
		    case 'integer' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'double'  :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'numeric' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'smallint' :  $tipx = 'numeric('.$tam.','.$dec.')';  break;
		    case 'long'    :  $tipx = 'clob';  break;
		    case 'time'    :  $tipx = 'timestamp';  break;
    		default:  $tipx = $tipo;  
		}
		if(!in_array($coluna, $colu)) {
          if ($a > 0) { $q .= ','; }
		  $q .= $coluna.' '.$tipx;
		  if ($nulo == 'Y')  {  $q  .= ' null';  } else  { $q .= ' not null';  }
		  $a++;
		}  
	}
	$q .= ')';
	return $q;
}

function cria_cols_fb_oracle($tabs,$cols_ora, $tabela)  {
	foreach($cols_ora as $cols)  {
		$colu[] = $cols['COLUNA'];
	}
	$q = " alter table $tabela add (";
	$a = 0;
	foreach($tabs as $tb)  {
		$coluna = trim(strtoupper($tb['CAMPO']));
		$nulo     = $tb['NULO'];
		$tipo      = trim($tb['TIPO']);
		$tam     = $tb['TAM'];
		$dec     = $tb['DECX'];
		if($dec < 0)  { $dec = $dec * -1;  }
		if(!in_array($coluna, $colu)) {
     		switch ($tipo)  {
		      case 'VARCHAR'         :    $tipx = 'varchar2('.$tam.')'; break;
		      case 'CHAR'         :    $tipx = 'varchar2('.$tam.')'; break;
		      case 'INTEGER'  :     $tipx = 'numeric('.$tam.','.$dec.')';  break;
		      case 'SMALLINT':     $tipx = 'numeric('.$tam.','.$dec.')';  break;
		      case 'INT64'        :      $tipx = 'numeric('.$tam.','.$dec.')';  break;
		      case 'BLOB'        :      $tipx = ' clob';  break;
    		  default:  $tipx = $tipo;  
	       }
          if ($a > 0) { $q .= ','; }
		  $q .= $coluna.' '.$tipx;
		  if ($nulo == '1')  {  $q  .= ' not null';  } else  { $q .= '  null';  }
		  $a++;
		}  
	}
	$q .= ')';
	return $q;

}



function monta_insert_oracle($tabs,$tp)  {
   $cc = '';
   $a = 0;
   foreach($tabs as $tb)  {
    	$coluna = $tb['campo'];
	    $tipo      = $tb['tipo'];
	    if ($tp !== '1')  {
         if ($a > 0) { $cc .=  ', '; }
         if (substr($tipo,0,4)  !== 'long')  { $cc .= $coluna; } else { $cc .= 'null as '.$coluna; }
         $a++;   
      }  else {  	
    	if ($a > 0) { $cc .=  ', '; }
        $cc .= $coluna;
        $a++;   
	    }	   
   }
   return $cc;
}