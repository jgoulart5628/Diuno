@echo off
set ORACLE_HOME=C:\oraclexe\app\oracle\product\11.2.0\server
set ORACLE_SID=nucleo
set TNS_ADMIN=C:\oraclexe\app\oracle\product\11.2.0\server\network\admin
set PATH=%PATH%;C:\oraclexe\app\oracle\product\11.2.0\server\bin
rem  processo de criação de cliente no oracle
rem  informar: 1. Nome do usuario
if '%1%' == '' echo Escolha o Usuario; GOTO SAI
set TAM=%1
set EXT=.dbf
call :strLen TAM LEN
set len=%LEN%
set PASS=N%1Uc%LEN%
rem acessar o banco e eliminara tablespace e recriar
sqlplus sys/nucleo@xe as sysdba @cria_emp.sql %TAM% %EXT% %PASS%

:strLen  strVar  [rtnVar]
setlocal disableDelayedExpansion
set len=0
if defined %~1 for /f "delims=:" %%N in (
  '"(cmd /v:on /c echo(!%~1!&echo()|findstr /o ^^"'
) do set /a "len=%%N-3"
endlocal & if "%~2" neq "" (set %~2=%len%) else echo %len%
exit /b
:SAI
