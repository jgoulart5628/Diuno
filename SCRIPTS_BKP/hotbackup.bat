@echo off
rem Seta variáveis
call ..\backupdef.bat
set BB_BHOT_LOCK=%BB_PATH%\lock\bhot.lock
set BB_BHOT_SQL=%BB_PATH%\ext\bhot.sql
set BB_BHOT_LOG=%BB_PATH%\log\bhot.log
set BB_BHOT_LOG_TEMP=%BB_PATH%\log\bhot1.log

del %BB_BHOT_LOG% 2> nul
del %BB_BHOT_LOG_TEMP% 2> nul

rem Verifica se a estrutura de diretórios está criada
call ..\etc\verificadir.bat

rem Monta o SQL para o backup
call ..\ext\gerabhot.bat

if exist %BB_BHOT_LOCK% goto LOCK

rem Cria lock para evitar que outros backups executem no mesmo momento
echo. > %BB_BHOT_LOCK%

sqlplus -S %BB_ORABKP_LOGIN%/%BB_ORABKP_SENHA%@%BB_ORABKP_SERVICE% @%BB_BHOT_SQL% >%BB_BHOT_LOG% 2> %BB_BHOT_LOG_TEMP% < nul
if exist %BB_BHOT_LOG_TEMP% (
	type %BB_BHOT_LOG_TEMP% >> %BB_BHOT_LOG%
	del %BB_BHOT_LOG_TEMP% 2> nul
)

REM Procurando por ORA- ou ERRO no log
for /F %%i in (%BB_BHOT_LOG%) do if /I "%%i" neq "" goto ACHOUERRO

REM Caso não deu erro, copia remoto...
if "%BB_ORABKP_REMOTO%" == "1" call %BB_PATH%\ext\copia_remoto.bat fisico 2> %BB_BHOT_LOG_TEMP%
if exist %BB_BHOT_LOG_TEMP% (
	type %BB_BHOT_LOG_TEMP% >> %BB_BHOT_LOG%
	del %BB_BHOT_LOG_TEMP% 2> nul
)

REM Procurando por ORA- ou ERRO no log
for /F %%i in (%BB_BHOT_LOG%) do if /I "%%i" neq "" goto ACHOUERRO

REM Exclui status anterior
if exist %BB_PATH%\log\green.bhot del %BB_PATH%\log\green.bhot
if exist %BB_PATH%\log\red.bhot del %BB_PATH%\log\red.bhot
if exist %BB_PATH%\log\purple.bhot del %BB_PATH%\log\purple.bhot

REM Gera arquivo de sucesso
date /T > %BB_PATH%\log\green.bhot
echo. >> %BB_PATH%\log\green.bhot
echo Backup Físico executado com sucesso! >> %BB_PATH%\log\green.bhot

call %BB_PATH%\bin\copia_arch.bat bhot

goto FIM

:LOCK

REM Exclui status anterior
if exist %BB_PATH%\log\green.bhot del %BB_PATH%\log\green.bhot
if exist %BB_PATH%\log\red.bhot del %BB_PATH%\log\red.bhot
if exist %BB_PATH%\log\purple.bhot del %BB_PATH%\log\purple.bhot

date /T > %BB_PATH%\log\red.bhot
echo. >> %BB_PATH%\log\red.bhot
echo ------------------------------ >> %BB_PATH%\log\red.bhot
echo Arquivo de lock de backup encontrado! >> %BB_PATH%\log\red.bhot
echo ------------------------------ >> %BB_PATH%\log\red.bhot

goto FIM

:ACHOUERRO

REM Exclui status anterior
if exist %BB_PATH%\log\green.bhot del %BB_PATH%\log\green.bhot
if exist %BB_PATH%\log\red.bhot del %BB_PATH%\log\red.bhot
if exist %BB_PATH%\log\purple.bhot del %BB_PATH%\log\purple.bhot

date /T > %BB_PATH%\log\red.bhot
echo. >> %BB_PATH%\log\red.bhot
echo ------------------------------ >> %BB_PATH%\log\red.bhot
type %BB_BHOT_LOG% >> %BB_PATH%\log\red.bhot
echo ------------------------------ >> %BB_PATH%\log\red.bhot

:FIM
del /Q %BB_BHOT_LOG% 2> nul
del /Q %BB_BHOT_LOCK%
