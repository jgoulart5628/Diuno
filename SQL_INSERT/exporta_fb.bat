@echo off
D:
cd \SCRIPTS_ORACLE\SQL_INSERT
if '%1%' == '' echo Escolha o Usuario; GOTO SAI
set CLI=%1
set TAB=%2
set ARQ=%1\%2.sql
set ARQ1=%1\%2.sqlx
call :strLen CLI LEN
set len=%LEN%
set PASS=N%1Uc%LEN%
D:\SCRIPTS_ORACLE\scripts\fbexport.exe -Si -JY-M-D -C 100 -M -D D:\banco\Firebird\%1.FDB -P masterkey -F %ARQ% -Q "SELECT * FROM %2 "
@importa_fb.bat %CLI% %PASS% %ARQ% %ARQ1% %TAB%

:strLen  strVar  [rtnVar]
setlocal disableDelayedExpansion
set len=0
if defined %~1 for /f "delims=:" %%N in (
  '"(cmd /v:on /c echo(!%~1!&echo()|findstr /o ^^"'
) do set /a "len=%%N-3"
endlocal & if "%~2" neq "" (set %~2=%len%) else echo %len%
exit /b
:SAI
