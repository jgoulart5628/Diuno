rem Exporta banco de dados diario 
rem FOR /f "tokens=* delims=" %%F in (bancos.txt) DO   echo %%F  
FOR %%A IN (%Date%) DO (
    FOR /F "tokens=1-3 delims=/-" %%B in ("%%~A") DO (
        SET Hoje=%%D%%B%%C
    )
)
expdp system/nucleo directory=DUMP dumpfile=%1.dmp tablespaces=%1
zip -m bkp_%Hoje%  %1.dmp 
rem echo %Hoje%
