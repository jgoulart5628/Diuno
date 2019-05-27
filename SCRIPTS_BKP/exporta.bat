@echo off
REM Deleta status anterior
del /Q %BB_PATH%\log\*.exporta 2> nul
del /Q %BB_PATH%\log\exporta%NUM%-zip.log 2> nul
del /Q %BB_ORABKP_PATH%\logico\fullbkp%NUM%.log 2> nul

REM Executa export FULL
exp %BB_ORABKP_LOGIN%/%BB_ORABKP_SENHA%@%BB_ORABKP_SERVICE% file=%BB_ORABKP_PATH%\logico\fullbkp%NUM% log=%BB_ORABKP_PATH%\logico\fullbkp%NUM% statistics=none consistent=y full=y 2> %BB_PATH%\log\exporta%NUM%-exp.log < nul

REM Procurando por EXP- ou ORA-
for /F %%i in ('findstr /I /M "ORA- EXP-" %BB_ORABKP_PATH%\logico\fullbkp%NUM%.log') do if /I "%%i" == "%BB_ORABKP_PATH%\logico\fullbkp%NUM%.log" goto ACHOUERRO

REM Procurando por String de sucesso no backup
for /F %%i in ('findstr /I /M /C:"com sucesso sem advertências" /C:"com sucesso, sem advertências" /C:"com sucesso sem advertÛncias" /C:"com sucesso, sem advertÛncias" /C:"successfully without warnings" /C:"com êxito, sem advertências" /C:"com Ûxito, sem advertÛncias" %BB_ORABKP_PATH%\logico\fullbkp%NUM%.log') do if /I "%%i" == "%BB_ORABKP_PATH%\logico\fullbkp%NUM%.log" goto ACHOUSUCESSO

goto ACHOUERRO

:ACHOUERRO

if exist %BB_ORABKP_PATH%\logico\fullbkp%NUM%.log (
	type %BB_ORABKP_PATH%\logico\fullbkp%NUM%.log > %BB_PATH%\log\red.exporta
) else (
	type %BB_PATH%\log\exporta%NUM%-exp.log > %BB_PATH%\log\red.exporta
)

if exist %BB_PATH%\log\exporta%NUM%-zip.log type %BB_PATH%\log\exporta%NUM%-zip.log > %BB_PATH%\log\red.exporta

goto FIM

:ACHOUSUCESSO

del /Q %BB_ORABKP_PATH%\logico\fullbkp%NUM%.ZIP 2> nul
%BB_PATH%\ext\zip\ZIP -mju9 %BB_ORABKP_PATH%\logico\fullbkp%NUM%.ZIP %BB_ORABKP_PATH%\logico\fullbkp%NUM%.dmp 1> nul 2> %BB_PATH%\log\exporta%NUM%-zip.log

rem Verifica erro no zip (tirando o WARNING de arquivo não encontrado...)
for /F %%i in ('findstr /I /V /C:"not found or empty" %BB_PATH%\log\exporta%NUM%-zip.log') do if /I "%%i" neq "" goto ACHOUERRO

if "%BB_ORABKP_REMOTO%" == "1" (
	call %BB_PATH%\ext\copia_remoto.bat logico fullbkp%NUM%.ZIP 2> %BB_PATH%\log\exporta%NUM%-zip.log

	rem Verifica erro na cópia remota
	for /F %%i in (%BB_PATH%\log\exporta%NUM%-zip.log) do if /I "%%i" neq "" goto ACHOUERRO
)

type %BB_ORABKP_PATH%\logico\fullbkp%NUM%.log > %BB_PATH%\log\green.exporta

rem del /Q %BB_PATH%\log\exporta%NUM%-zip.log 2> nul

:FIM
