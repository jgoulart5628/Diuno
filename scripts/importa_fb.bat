@echo  off
type geral.sql %1.sql final.sql >> %1.sqlx
sqlplus -s alumiglass/NalumiglassUc10@nucleo @%1.sqlx
