@echo off
setlocal
set "test=%1"

:: Echo the length of TEST
call :strLen test

:: Store the length of TEST in LEN
call :strLen test len
echo len=%len%
exit /b

:strLen  strVar  [rtnVar]
setlocal disableDelayedExpansion
set len=0
if defined %~1 for /f "delims=:" %%N in (
  '"(cmd /v:on /c echo(!%~1!&echo()|findstr /o ^^"'
) do set /a "len=%%N-3"
endlocal & if "%~2" neq "" (set %~2=%len%) else echo %len%
exit /b