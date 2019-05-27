create or replace trigger dtlogin
  after logon on database 
  begin
   execute immediate 'ALTER SESSION SET NLS_DATE_FORMAT="YYYY-MM-DD HH24:MI:SS"';
  end;
  
