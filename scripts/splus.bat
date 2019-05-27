rem @echo off
set ORACLE_HOME=C:\oraclexe\app\oracle\product\11.2.0\server
set ORACLE_SID=nucleo
set TNS_ADMIN=C:\oraclexe\app\oracle\product\11.2.0\server\network\admin
set PATH="C:\oraclexe\app\oracle\product\11.2.0\server\bin;%PATH%"
sqlplus sys/nucleo@xe as sysdba
