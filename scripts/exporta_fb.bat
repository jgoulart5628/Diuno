@echo off
D:
cd \SCRIPTS_ORACLE\SQL_INSERT\alumiglass 
D:\SCRIPTS_ORACLE\scripts\fbexport.exe -Si -JY-M-D -D D:\banco\Firebird\ALUMIGLASS.FDB -P masterkey -F %1.sql -Q "SELECT * FROM %1 "
@importa_fb.bat %1
del %1.* 
