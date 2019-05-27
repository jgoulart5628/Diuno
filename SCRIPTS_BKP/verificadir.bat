rem @echo off

if "%1" == "remoto"  goto REMOTO

:LOCAL

if exist %BB_ORABKP_PATH% goto :HomeExiste

mkdir %BB_ORABKP_PATH%

:HomeExiste
if not exist %BB_ORABKP_PATH%\arch mkdir %BB_ORABKP_PATH%\arch
if not exist %BB_ORABKP_PATH%\fisico mkdir %BB_ORABKP_PATH%\fisico
if not exist %BB_ORABKP_PATH%\logico mkdir %BB_ORABKP_PATH%\logico

goto FIM

:REMOTO

if not exist %BB_ORAREM_UNIDADE%:\arch mkdir %BB_ORAREM_UNIDADE%:\arch
if not exist %BB_ORAREM_UNIDADE%:\fisico mkdir %BB_ORAREM_UNIDADE%:\fisico
if not exist %BB_ORAREM_UNIDADE%:\logico mkdir %BB_ORAREM_UNIDADE%:\logico

:FIM
