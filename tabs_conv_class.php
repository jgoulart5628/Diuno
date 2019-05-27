<?php
// error_reporting(E_ALL);
// ini_set('display_errors', true);
 // echo '<script type="text/javascript">alert(\'Teste!\'); </script>';
require 'inc/banco_pdo_class.php';
class Converte_model extends banco_dados {
   public  $sq;
   public  $resul;
   public  $banco;
   public  $cliente;
   private $id;
   private $tabela;
   private $rows_origem;
   private $rows_destino;
   private $tabela_id;
   private $coluna;
   private $lidos;
   private $gravados;
   private $difer;
   

   public function __construct($banco)  {
      $this->sq = new banco_dados($banco);
//      if (is_object($this->sq)) {  $this->cria_tabelas($this->sq); }
   }

   public function cria_tabelas($db, $tela='') {
      $query = " CREATE TABLE if not exists `converte` (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
                 `empresa` varchar(20) COLLATE utf8_bin NOT NULL,
                 `tabela` varchar(40) COLLATE utf8_bin NOT NULL,
                 `rows_origem` int(11) NOT NULL,
                 `rows_destino` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `key` (`empresa`,`tabela`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
     $sql = $db->banco_query($query,'sql');
     $query = " CREATE TABLE if not exists `colunas_long` (
                `tabela_id` int(11) NOT NULL,
                `coluna` varchar(30) COLLATE utf8_bin NOT NULL,
                `lidos` int(11) NOT NULL default '0',
                `gravados` int(11) NOT NULL default '0',
                PRIMARY KEY (`tabela_id`, `coluna`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
     $sql = $db->banco_query($query,'sql', $tela);
  }

  public function ler_tab_conv($empresa, $tela='')  {
     $query = " select id, tabela, rows_origem, rows_destino, (rows_origem - rows_destino) difer
                    from converte a where empresa = '$empresa' order by 2 "; 
     $lista = $this->sq->banco_query($query, 'array', $tela);
     return $lista;
//                                 , (select group_concat(concat(b.coluna,'|',(b.lidos - b.gravados)) separator ', ') as longs from coluna_long b
//                                       where a.id = b.tabela_id) longs
   }   
  public function ler_coluna_long($id, $tela='')  {
     $query = " select coluna, (lidos - gravados) difer from colunas_long where tabela_id = $id "; 
     $lista = $this->sq->banco_query($query, 'array', $tela);
     return $lista;
   }   

   public function Ultimo_ID($empresa, $tab, $tela='')  {
      $query = " SELECT id FROM converte where tabela = '$tab' and empresa = '$empresa' ";
      return  $this->sq->banco_query($query,'unico', $tela);
   }   

   public function totalRows($empresa, $tabela, $tela='')  {
     $query = "select rows_origem from converte where tabela = '$tabela' and empresa = '$empresa' ";
     return $this->sq->banco_query($query,'unico', $tela);
   }  
   
   public function totalTabs($empresa, $tela='')  {
       $query = "select count(*) conta from converte where  empresa = '$empresa' ";
       return $this->sq->banco_query($query,'unico', $tela);
   }
   
   public function insertRow($empresa, $tab, $conta, $tela='')  {
          $query = " insert into converte (empresa, tabela, rows_origem, rows_destino) values('$empresa', '$tab', $conta, 0) "; 
          $e = $this->sq->banco_query($query, 'sql', $tela);
          return $e;
   }       

   public function updateRow($id, $regs, $conta, $tela='')  {
      $query = " update converte set rows_origem = $conta , rows_destino  = $regs where id = $id ";
      $e = $this->sq->banco_query($query, 'sql', $tela);
      return $e;
   }

   public function insertRowLong($tabela_id, $coluna, $orig_conta, $dest_conta, $tela='')  {
          $x = $this->sq->banco_query(" select count(*) conta from colunas_long where tabela_id = $tabela_id  and coluna = '$coluna' ", 'unico', $tela);
          if($x > 0) {
                $query = " update  colunas_long set lidos = $orig_conta, gravados = $dest_conta where tabela_id = $tabela_id and coluna = '$coluna' ";
          } else {
            $query = " insert into colunas_long values($tabela_id, '$coluna',$orig_conta, $dest_conta) ";
          }       
          $e =  $this->sq->banco_query($query, 'sql', $tela);  
          return $e;
   }
   public function Entra_Sai_DB($conv, $cliente)  {
      switch ($conv)  {
         case "1":    $entra  =   'ODBC_'.$cliente;              
                      $saida  =  'ORACLE_'.$cliente;             
                      break;
         case "2":    $entra  =   'ODBC_'.$cliente;              
                      $saida  =  'SQLSERVER_'.$cliente;              
                      break;
         case "3":    $entra  =   'FIREBIRD_'.$cliente;              
                      $saida  =  'ORACLE_'.$cliente;             
                      break;
         case "4":    $entra  =   'FIREBIRD_'.$cliente;              
                      $saida  =  'SQLSERVER_'.$cliente;              
                      break;
         case "5":    $entra  =   'FIREBIRD_'.$cliente;              
                      $saida  =  'POSTGRESQL_'.$cliente;              
                      break;
//         case "6":    $entra  =   'ODBC_'.$cliente;              
 //                     $saida  =  'POSTGRESQL_'.$cliente;              
//                      break;
         case "6":    $entra  =   'MYSQL_'.$cliente;
                      $saida  =  'POSTGRESQL_'.$cliente;
                      break;
      }  
       return $entra.'|'.$saida;
   }

}

class InOut_model extends banco_dados {
   protected   $IO;
   public   $error;
//   public  $banco;
//   public  $cliente;
//   private $id;
//   private $tabela;
   

   public function __construct($in_out)  {
     $this->IO = new banco_dados($in_out);
     $vars = get_object_vars ( $this->IO);
     return $this->error = $vars['erro'];
   }

   public function __toString() {
        return $this->IO;
   }

   public function ContaRegs($tabela, $coluna='', $tela='')  {
     $query = " select count(*) conta  from $tabela ";
     if ($coluna) {  $query .= " where $coluna is not null "; }
     return $this->IO->banco_query($query,'unico', $tela);
   } 
   
   public function Lista_Tabelas_Orig($conv, $tela='')  {
       $query = ''; 
       if ($conv === '1' || $conv === '2')  {  
           $query =    "  SELECT  a.name  FROM sysobjects a, sysusers b
                         WHERE a.type IN ('U', 'S') AND a.uid = b.uid
                           AND b.name = 'DBA'  
                         ORDER BY  a.name ";
       }                  
       if ($conv === '3' || $conv === '4')  {  
          $query = " SELECT RDB\$RELATION_NAME as name
                          FROM RDB\$RELATIONS
                        WHERE RDB\$SYSTEM_FLAG = 0
                          AND RDB\$VIEW_BLR IS NULL
                        ORDER BY RDB\$RELATION_NAME ";
       }
       if ($conv === '5')  {  
          $query = " SELECT table_name as name FROM information_schema.tables WHERE table_schema='public'
                        ORDER BY table_name ";
       }
       // table schema
       if ($conv === '6')  {
           $query = " SELECT table_name as name FROM information_schema.tables WHERE table_schema='deal'
                        ORDER BY table_name ";
       }
       return $this->IO->banco_query($query, 'array', $tela);
   }    

  //-ALTER TABLE "b" ADD CONSTRAINT "FK_b" FOREIGN KEY ("a_id")
  //   REFERENCES "a"("id") ON DELETE CASCADE ON UPDATE CASCADE;
   public function ATUALIZA_FK($tela='')  {
    $query = " select constra, tabela, list(distinct col,',') as coluna, 
               referencia_tabela, list(distinct referencia_col,',') as referencia_coluna,
               on_update, ON_DELETE
       from  (
        select
          rc.rdb\$constraint_name AS CONSTRA
        , rcc.rdb\$relation_name AS TABELA  --filha
        , isc.rdb\$field_name AS COL      -- filha
        , rcp.rdb\$relation_name AS REFERENCIA_TABELA -- parent_table
        , isp.rdb\$field_name as REFERENCIA_COL -- parent_column
        , rc.rdb\$update_rule AS ON_UPDATE -- update_rule
        , rc.rdb\$delete_rule AS ON_DELETE  --delete_rule
      from rdb\$ref_constraints AS rc
inner join rdb\$relation_constraints AS rcc
on rc.rdb\$constraint_name = rcc.rdb\$constraint_name
inner join rdb\$index_segments AS isc
on rcc.rdb\$index_name = isc.rdb\$index_name
inner join rdb\$relation_constraints AS rcp
on rc.rdb\$const_name_uq  = rcp.rdb\$constraint_name
inner join rdb\$index_segments AS isp
on rcp.rdb\$index_name = isp.rdb\$index_name
)
GROUP BY constra, tabela, referencia_tabela, on_update, ON_DELETE ";
// where rcc.rdb$relation_name = 'NOTAF_SAI_ITEM'

    return $this->IO->banco_query($query, 'array', $tela);
   }


   public function DDL_Origem($tabela, $conv, $tela='')  {
       $query = ''; 
       if ($conv === '6')  {  
          $query = "  select column_name as campo, data_type as tipo, case when character_maximum_length is not null then character_maximum_length else numeric_precision end  as tam, 
numeric_scale as decx, column_key as pk  from information_schema.columns where table_name = '$tabela' ";
       }
       if ($conv === '1' || $conv === '2')  {  
          $query = " select cname campo, coltype tipo, length tam, syslength decx, nulls nulo, in_primary_key pk 
             from sys.SYSCOLUMNS  where upper(tname) = '$tabela' order by colno ";
       }
       if ($conv === '3' || $conv === '4')  {  
          $query = " SELECT r.RDB\$FIELD_NAME AS campo,
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
        f.RDB\$FIELD_SCALE as decx,
        ( SELECT 'Y' as PK
            FROM RDB\$RELATION_CONSTRAINTS RC
            JOIN RDB\$INDEX_SEGMENTS RIS
              ON RIS.RDB\$INDEX_NAME = RC.RDB\$INDEX_NAME
           WHERE RC.RDB\$RELATION_NAME = r.RDB\$RELATION_NAME
            AND RIS.RDB\$FIELD_NAME   = r.RDB\$FIELD_NAME
            AND RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY') as PK
          FROM RDB\$RELATION_FIELDS r 
              LEFT JOIN RDB\$FIELDS f ON r.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
                    WHERE r.RDB\$RELATION_NAME='$tabela'
                   ORDER BY r.RDB\$FIELD_POSITION ";
      } 
      return $this->IO->banco_query($query, 'array', $tela);
   }   

   public function CONSTR_ORACLE($tabela, $op, $tela='')  {
   if ($op === 'D')  { 
      $query = " declare cursor xx is
  SELECT 'alter table ' || tabela || ' modify  constraint ' || fk || ' disable cascade ' constra
  from (
    select (select x.table_name from user_constraints x where x.constraint_name = aa.r_constraint_name) tabela
        ,aa.r_constraint_name as fk
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = '$tabela'
union all
select aa.table_name as tabela, aa.constraint_name fk
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = '$tabela'
);
 begin
    for xx_rec in xx
    loop
       execute immediate xx_rec.constra; 
       commit;
     end loop;
  end; ";
   } 

   if ($op === 'E')  {
       $query = " declare cursor xx is
                 SELECT 'alter table ' || tabela || ' modify  constraint ' || fk || ' enable novalidate ' constra 
                  from (
        select (select x.table_name from user_constraints x where x.constraint_name = aa.r_constraint_name) tabela
               ,aa.r_constraint_name as fk 
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = '$tabela'
union all
select aa.table_name as tabela, aa.constraint_name fk
  from user_constraints aa
 where aa.constraint_type = 'R' 
   and aa.table_name = '$tabela'
);
          begin
             for xx_rec in xx
             loop
              execute immediate xx_rec.constra; 
              commit;
            end loop;
            end; ";  
   }
   $e  =  $this->IO->banco_query($query, 'sql', $tela);
 }

 public function PK_FIREBIRD($tabela, $tela='')  {
     $pk = " SELECT RIS.RDB\$FIELD_NAME as PKNAME
                     FROM RDB\$RELATION_CONSTRAINTS RC
                      JOIN RDB\$INDEX_SEGMENTS RIS
                     ON RIS.RDB\$INDEX_NAME = RC.RDB\$INDEX_NAME
                      WHERE RC.RDB\$RELATION_NAME = '$tabela'
                      AND RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY' ";
     return  $this->IO->banco_query($pk,'array', $tela);
  }

 public function PKS_FIREBIRD($tela='')  {
     $pk = " SELECT list(RIS.RDB\$FIELD_NAME,',') as COLUNA, RC.RDB\$CONSTRAINT_NAME as PKNAME, RC.RDB\$RELATION_NAME as TABELA
                     FROM RDB\$RELATION_CONSTRAINTS RC
                      JOIN RDB\$INDEX_SEGMENTS RIS
                     ON RIS.RDB\$INDEX_NAME = RC.RDB\$INDEX_NAME
                      WHERE RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY' 
                      GROUP BY  RC.RDB\$CONSTRAINT_NAME , RC.RDB\$RELATION_NAME ";
     return  $this->IO->banco_query($pk,'array', $tela);
  }

 public function Verifica_Destino($tabela, $conv, $tela='')  {
     $query = '';
     if ($conv === '2' || $conv === '4')  {
        $query = " select name from sys.tables where table_name = '$tabela' ";
     }   
     if ($conv === '1' || $conv === '3')  {
        $query = " select table_name from user_tables where table_name = '$tabela' ";
     }
     if ($conv === '6')  {
        $query = " select table_name from information_schema.tables where table_name = '$tabela' ";
     }

     return  $this->IO->banco_query($query,'unico', $tela);
  }   

 public function Lista_Tabs_Sybase($tela='')  {
    $query = " select name from sys.tables where name = '$tabela' ";
     return  $this->IO->banco_query($query,'array', $tela);
  }   

 public function Executa_Query_Single($query, $tela='')  {
     return  $this->IO->banco_query($query, 'single', $tela);
 }    
 public function Executa_Query_Unico($query, $tela='')  {
     return  $this->IO->banco_query($query, 'unico', $tela);
 }    

 public function Executa_Query_SQL($query, $tela='')  {
     return  $this->IO->banco_query($query,'sql', $tela);
 }    

 public function Executa_Query_Array($query, $tela='')  {
     return  $this->IO->banco_query($query,'array', $tela);
 }    

 public function Executa_Query_Nrows($query, $tela='')  {
     return  $this->IO->banco_query($query,'nrows', $tela);
 }    

 public function Colunas_Oracle($tabela, $tela='')  {
     $query = " select column_name coluna, column_id  from user_tab_cols where table_name = '$tabela'  order by column_id ";
     return  $this->IO->banco_query($query,'array', $tela);
 }    

 public function Colunas_SQLServer($tabela, $tela='')  {
     $query =  " select name as coluna, column_id  from sys.columns where object_name(object_id)='$tabela'  order by column_id ";
     return  $this->IO->banco_query($query,'array', $tela);
 }    


//$db = new banco_dados('ORACLE_coop');
//var_dump($db);
//$db = new SQLite3('d:\banco\sqlite\testes.db');
//$q1 = "insert into clientes (id, nome) values(null,'Joao Goulart') "; 
//$sql = $db->query($q1);
//echo $db->lastErrorMsg().'</br>';
//echo $db->lastInsertRowID();
//$db = new Converte_model('SQLITE_coop');
//var_dump($db);
//$sql = $db->query($q);
//while($res = $sql->fetchArray(SQLITE3_ASSOC)){ 
//    echo $res['id'].'-'.$res['nome'].'</br>';
//} 
// print_r($res);
/*
select
rc.rdb$constraint_name AS fk_name
, rcc.rdb$relation_name AS child_table
, isc.rdb$field_name AS child_column
, rcp.rdb$relation_name AS parent_table
, isp.rdb$field_name AS parent_column
, rc.rdb$update_rule AS update_rule
, rc.rdb$delete_rule AS delete_rule
from rdb$ref_constraints AS rc
inner join rdb$relation_constraints AS rcc
on rc.rdb$constraint_name = rcc.rdb$constraint_name
inner join rdb$index_segments AS isc
on rcc.rdb$index_name = isc.rdb$index_name
inner join rdb$relation_constraints AS rcp
on rc.rdb$const_name_uq  = rcp.rdb$constraint_name
inner join rdb$index_segments AS isp
on rcp.rdb$index_name = isp.rdb$index_name
*/
}