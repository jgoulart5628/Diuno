@echo off
rem  processo de importação do banco de dados
rem  recebe nome usuario a importar.
if '%1%' == '' echo Informe o arquivo de DUMP;   goto SAI
echo %1
rem acessar o banco e eliminara tablesapce e recriar
sqlplus sys/senha as sysdba @limpa.sql %1
rem importa o arquivo
impdp system/senha  directory=DUMP dumpfile=%1.dmp  tablespaces=%1
:SAI
