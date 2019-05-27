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

   public function cria_tabelas($db) {
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
     $sql = $db->banco_query($query,'sql');
  }

  public function ler_tab_conv($empresa)  {
     $query = " select id, tabela, rows_origem, rows_destino, (rows_origem - rows_destino) difer
                    from converte a where empresa = '$empresa' order by 2 "; 
     $lista = $this->sq->banco_query($query, 'array');
     return $lista;
//                                 , (select group_concat(concat(b.coluna,'|',(b.lidos - b.gravados)) separator ', ') as longs from coluna_long b
//                                       where a.id = b.tabela_id) longs
   }   
  public function ler_coluna_long($id)  {
     $query = " select coluna, (lidos - gravados) difer from colunas_long where tabela_id = $id "; 
     $lista = $this->sq->banco_query($query, 'array');
     return $lista;
   }   

   public function Ultimo_ID($empresa, $tab)  {
      $query = " SELECT id FROM converte where tabela = '$tab' and empresa = '$empresa' ";
      return  $this->sq->banco_query($query,'unico');
   }   

   public function totalRows($empresa, $tabela)  {
     $query = "select rows_origem from converte where tabela = '$tabela' and empresa = '$empresa' ";
     return $this->sq->banco_query($query,'unico');
   }  

   public function insertRow($tab, $conta)  {
          $query = " insert into converte (emoresa, tabela, rows_origem, rows_destino) values('$empresa', '$tab', $conta, 0) "; 
          $e = $this->sq->banco_query($query, 'sql');
          return $e;
   }       

   public function updateRow($id, $regs, $conta)  {
      $query = " update converte set rows_origem = $conta , rows_destino  = $regs where id = $id ";
      $e = $this->sq->banco_query($query, 'sql');
      return $e;
   }

   public function insertRowLong($tabela_id, $coluna, $orig_conta, $dest_conta)  {
          $x = $this->sq->banco_query(" select count(*) conta from colunas_long where tabela_id = $tabela_id  and coluna = '$coluna' ", 'unico');
          if($x > 0) {
                $query = " update  colunas_long set lidos = $orig_conta, gravados = $dest_conta where tabela_id = $tabela_id and coluna = '$coluna' ";
          } else {
            $query = " insert into colunas_long values($tabela_id, '$coluna',$orig_conta, $dest_conta) ";
          }       
          $e =  $this->sq->banco_query($query, 'sql');  
          return $e;
   }

}

class Parametro_model  {
    protected $SQLITE;
    public    $empresas;
    public    $sql;
    
    public function __construct()  {
      $par = '/WEB/nucleo/inc/parametro.sqlite';
      $this->SQLITE = new PDO('sqlite:'.$par);
    }

    public function Lista_empresas()  {
      $query = " SELECT distinct id FROM empresa_banco where id != 'nucleo' order by id ";
      $this->sql = $this->SQLITE->query($query);
      $res  = $this->sql->fetchAll(PDO::FETCH_ASSOC);
      foreach($res as $ban) {
       $this->empresas[] = $ban['id'];  
      }
      return $this->empresas;
    }

    public function Lista_DBMS($empresa)  {
        $query  = " select  dbms,
               case when dbms = 'ORACLE'  then  'S'
                    when dbms = 'SQLSERVER'  then  'S' 
                    when dbms = 'ODBC'  then  'E'
                    when dbms = 'FIREBIRD'  then  'E'
                    when dbms = 'SYBASE'  then  'E'
                end es       
           from empresa_banco where id = '$empresa'
               order by es ";
        $this->sql = $this->SQLITE->query($query);
        return $this->sql->fetchAll(PDO::FETCH_ASSOC);
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

   public function ContaRegs($tabela, $coluna)  {
     $query = " select count(*) conta  from $tabela ";
     if ($coluna) {  $query .= " where $coluna is not null "; }
     return $this->IO->banco_query($query,'unico');
   } 
   
   public function Lista_Tabelas_Orig($conv)  {
       $query = '';
       if ($conv === '1' || $conv === '2')  {  
           $query =    "  SELECT  a.name  FROM sysobjects a, sysusers b
                         WHERE a.type IN ('U', 'S') AND a.uid = b.uid
                           AND b.name = 'DBA'  
                         ORDER BY  a.name ";
       }                  
       if ($conv === '3' || $conv === '4')  {  
          $query = " SELECT RDB\$RELATION_NAME as tabela
                          FROM RDB\$RELATIONS
                        WHERE RDB\$SYSTEM_FLAG = 0
                          AND RDB\$VIEW_BLR IS NULL
                        ORDER BY RDB\$RELATION_NAME ";
       }
       return $this->IO->banco_query($query, 'array');
   }    

   public function DDL_Origem($tabela, $conv)  {
       $query = ''; 
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
      return $this->IO->banco_query($query, 'array');
   }   

   public function CONSTR_ORACLE($tabela, $op)  {
   if ($op === 'D')  { 
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
     $res = $this->IO->banco_query($query,'array'); 
     if (count($res)  > 0)  {
         foreach ($res as $ch)  {
            $tab =  $ch['TABELA'];
            $fk  =  $ch['FK'];
            $qx  =  "alter table $tab modify  constraint $fk  disable novalidate "; 
            $e   =  $this->IO->banco_query($qx, 'sql');
         }
     }
   } 

   if ($op === 'E')  { 
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
      $res = $this->IO->banco_query($query,'array'); 
      if (count($res)  > 0)  {
         foreach ($res as $ch)  {
            $tab =  $ch['TABELA'];
            $fk  =  $ch['FK'];
            $qx  =  "alter table $tab modify constraint $fk enable novalidate "; 
            $e   =  $this->IO->banco_query($qx, 'sql');
         }
     }
   }
 }

 public function PK_FIREBIRD($tabela)  {
     $pk = " SELECT RIS.RDB\$FIELD_NAME as PKNAME
                     FROM RDB\$RELATION_CONSTRAINTS RC
                      JOIN RDB\$INDEX_SEGMENTS RIS
                     ON RIS.RDB\$INDEX_NAME = RC.RDB\$INDEX_NAME
                      WHERE RC.RDB\$RELATION_NAME = '$tabela'
                      AND RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY' ";
     return  $this->IO->banco_query($pk,'array');
  }

 public function Verifica_Destino($tabela, $conv)  {
     $query = '';
     if ($conv === '2' || $conv === '4')  {
        $query = " select name from sys.tables where name = '$tabela' ";
     }   
     if ($conv === '1' || $conv === '3')  {
        $query = " select table_name from user_tables where table_name = '$tabela' ";
     }
     return  $this->IO->banco_query($query,'unico');
  }   

 public function Executa_Query_Single($query)  {
     return  $this->IO->banco_query($query, 'single');
 }    
 public function Executa_Query_Unico($query)  {
     return  $this->IO->banco_query($query, 'unico');
 }    

 public function Executa_Query_SQL($query)  {
     return  $this->IO->banco_query($query,'sql');
 }    

 public function Executa_Query_Array($query)  {
     return  $this->IO->banco_query($query,'array');
 }    

 public function Executa_Query_Nrows($query)  {
     return  $this->IO->banco_query($query,'nrows');
 }    

 public function Colunas_Oracle($tabela)  {
     $query = " select column_name coluna, column_id  from user_tab_cols where table_name = '$tabela'  order by column_id ";
     return  $this->IO->banco_query($query,'array');
 }    

 public function Colunas_SQLServer($tabela)  {
     $query =  " select name as coluna, column_id  from sys.columns where object_name(object_id)='$tabela'  order by column_id ";
     return  $this->IO->banco_query($query,'array');
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
}
