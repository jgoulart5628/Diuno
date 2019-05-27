@echo off
rem Seta variáveis
call ..\backupdef.bat
set BB_ARCH_SQL=%BB_PATH%\ext\delarch.sql
set BB_ARCH_LOG=%BB_PATH%\log\barc.log
set BB_ARCH_LOG_TEMP=%BB_PATH%\log\barc1.log

del %BB_ARCH_LOG% 2> nul
del %BB_ARCH_LOG_TEMP% 2> nul

rem Verifica se a estrutura de diretórios está criada
call ..\etc\verificadir.bat

if "%1"=="bhot" goto :RODA

if exist %BB_PATH%\lock\bhot.lock goto :LOCK

:RODA

rem Cria SQL atualizado
call ..\ext\delarch.bat

sqlplus -S %BB_ORABKP_LOGIN%/%BB_ORABKP_SENHA%@%BB_ORABKP_SERVICE% @%BB_ARCH_SQL% > %BB_ARCH_LOG% 2> %BB_ARCH_LOG_TEMP% < nul
type %BB_ARCH_LOG_TEMP% >> %BB_ARCH_LOG% 2> nul
del %BB_ARCH_LOG_TEMP% 2> nul

for /F %%i in (%BB_ARCH_LOG%) do if /I "%%i" neq "" goto ACHOUERRO

REM Caso não deu erro, copia remoto...

if "%BB_ORABKP_REMOTO%" == "1" call %BB_PATH%\ext\copia_remoto.bat arch 2> %BB_ARCH_LOG_TEMP%
type %BB_ARCH_LOG_TEMP% >> %BB_ARCH_LOG% 2> nul
del %BB_ARCH_LOG_TEMP% 2> nul

REM Procurando por ORA- ou ERRO no log
for /F %%i in (%BB_ARCH_LOG%) do if /I "%%i" neq "" goto ACHOUERRO

REM Exclui status anterior
if exist %BB_PATH%\log\green.barc del %BB_PATH%\log\green.barc
if exist %BB_PATH%\log\red.barc del %BB_PATH%\log\red.barc
if exist %BB_PATH%\log\purple.barc del %BB_PATH%\log\purple.barc

REM Gera arquivo de sucesso
date /T > %BB_PATH%\log\green.barc
echo. >> %BB_PATH%\log\green.barc
echo Backup Archives executado com sucesso! >> %BB_PATH%\log\green.barc

goto FIM

:LOCK

REM Exclui status anterior
if exist %BB_PATH%\log\green.barc del %BB_PATH%\log\green.barc
if exist %BB_PATH%\log\red.barc del %BB_PATH%\log\red.barc
if exist %BB_PATH%\log\purple.barc del %BB_PATH%\log\purple.barc

date /T > %BB_PATH%\log\red.barc
echo. >> %BB_PATH%\log\red.barc
echo Arquivo de lock de backup encontrado! >> %BB_PATH%\log\red.barc

goto FIM

:ACHOUERRO

REM Exclui status anterior
if exist %BB_PATH%\log\green.barc del %BB_PATH%\log\green.barc
if exist %BB_PATH%\log\red.barc del %BB_PATH%\log\red.barc
if exist %BB_PATH%\log\purple.barc del %BB_PATH%\log\purple.barc

date /T > %BB_PATH%\log\red.barc
echo. >> %BB_PATH%\log\red.barc
type %BB_ARCH_LOG% >> %BB_PATH%\log\red.barc
del /Q %BB_ARCH_LOG% > nul

:FIM
