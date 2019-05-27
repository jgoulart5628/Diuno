@echo  off
type geral.sql %3 final.sql >> %4
D:\ORA_CLIENT\sqlplus.exe -s %1/%2@nucleo @D:\SCRIPTS_ORACLE\SQL_INSERT\%4
del D:\SCRIPTS_ORACLE\SQL_INSERT\%1\%5.*

