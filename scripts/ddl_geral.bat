@echo off
set DIR=D:\SCRIPTS_ORACLE\scripts\
FOR %%F in (DDL\*.sql) DO (
  echo exit; >> %DIR%%%F
rem  echo %DIR%%%F 
rem   sqlplus semear/semear@clientes @DDL/%%F
@rem php load_tabela.php DDL/%%F
)
