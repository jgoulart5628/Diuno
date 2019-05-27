<?php 
/*
  Class Parametros Conexão.  Arquivo Parametro.sqlite.
*/
class Parametro_model  {
    protected $SQLITE;
    protected $empresas;
    protected $sql;
    
    public function __construct()  {
      $par = 'inc/parametro.sqlite';
      $this->SQLITE = new PDO('sqlite:'.$par);
      $this->SQLITE->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->Cria_Tabela_Empresa();
    }

     protected function Cria_Tabela_Empresa() {
            $query =  " CREATE TABLE if not exists empresa_banco (id VARCHAR NOT NULL, nome_empresa VARCHAR NOT NULL ,
                    dbms VARCHAR NOT NULL ,dsn VARCHAR, host VARCHAR, dbname VARCHAR, user VARCHAR,pwd VARCHAR, driver varchar
                    PRIMARY KEY (id, dbms))";
            $this->sql = $this->Executa($query);
            return $this->sql;
    }

    public function Lista_empresas()  {
      $query = " SELECT distinct id FROM empresa_banco /* where dbms != 'MYSQL' */ order by id ";
      $this->sql = $this->Executa($query);
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
                    when dbms = 'POSTGRESQL'  then  'S' 
                    when dbms = 'MYSQL'  then  'E'
                    when dbms = 'ODBC'  then  'E'
                    when dbms = 'FIREBIRD'  then  'E'
                    when dbms = 'SYBASE'  then  'E'
                end es       
           from empresa_banco where id = '$empresa'
               order by es ";
        $this->sql = $this->SQLITE->query($query);
        return $this->sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public  function Executa($query)  {
      try {  $this->sql = $this->SQLITE->query($query); }
      catch (Exception $msg)  { $msg = $this->SQLITE->errorInfo();  return $msg; }
      return $this->sql;
   }


   public function leitura_tabela($id='',$dbms='')  {
      $query = 'SELECT * FROM empresa_banco ';
      if($id && $dbms)  { $query .=  " where id =  '$id' and dbms = '$dbms' "; }
      $query .= ' order by id ';
      $this->sql = $this->Executa($query);
      if (is_object($this->sql)) { return $this->sql->fetchAll(PDO::FETCH_ASSOC); }
      else { return 'Erro: '.$query; }  
   }


   public function combo_dbms($dbms='')  {
     $dbs = array('ORACLE','SQLSERVER','SYBASE','FIREBIRD','MYSQL','ODBC','SQLITE','POSTGRESQL');
     $tela  = 'DBMS : <select class="entra" style="width: 110px;"  name="dbms" id="dbms"> <option value ="" class="f_texto" ></option> ';
     foreach($dbs as $db) {
        if ($db === $dbms)  {  $sel = ' selected '; } else { $sel = ''; }
        $tela .= '<option value="'.$db.'"  '.$sel.' class="f_texto" > '.$db.' </option> '; 
     }     
     $tela .= '</select> &nbsp; &nbsp;&nbsp; '; 
     return $tela;
   }  
}
