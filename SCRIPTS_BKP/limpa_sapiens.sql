drop tablespace sapiens_index including contents and datafiles cascade constraints;
drop tablespace sapiens_data  including contents and datafiles cascade constraints;
create tablespace SAPIENS_DATA  datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_DATA.dbf'  size 2000M autoextend on next 100M maxsize 32767M;
create tablespace SAPIENS_INDEX datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_INDEX.dbf' size 2000M autoextend on next 100M maxsize 32767M;
exit;
