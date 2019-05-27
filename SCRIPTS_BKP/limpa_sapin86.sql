drop tablespace sapin86_index including contents and datafiles cascade constraints;
drop tablespace sapin86_data  including contents and datafiles;
create tablespace SAPIN86_DATA  datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIN86_DATA.dbf'  size 1000M autoextend on next 100M maxsize 32767M;
create tablespace SAPIN86_INDEX datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIN86_INDEX.dbf' size  500M autoextend on next 100M maxsize 32767M;
exit;