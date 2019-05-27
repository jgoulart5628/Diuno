@echo off
set DIR=%1/
set TAM=%1
call :strLen TAM LEN
set len=%LEN%
FOR %%F in (%1/*.sql) DO (
rem type 'exit;' >> %%F
rem echo %%F
sqlplus %1/N%1Uc%LEN%@nucleo @%DIR%%%F
del /Q %DIR%%%F 2> null
)
exit /b

:strLen  strVar  [rtnVar]
setlocal disableDelayedExpansion
set len=0
if defined %~1 for /f "delims=:" %%N in (
  '"(cmd /v:on /c echo(!%~1!&echo()|findstr /o ^^"'
) do set /a "len=%%N-3"
endlocal & if "%~2" neq "" (set %~2=%len%) else echo %len%
exit /b
