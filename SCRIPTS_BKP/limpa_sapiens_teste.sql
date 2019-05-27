drop tablespace sapiens_teste_index including contents and datafiles cascade constraints;
drop tablespace sapiens_teste_data  including contents and datafiles;
create tablespace SAPIENS_TESTE_DATA  datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_TESTE_DATA.dbf'   size 1000M autoextend on next 100M maxsize 32767M;
create tablespace SAPIENS_TESTE_INDEX datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_TESTE_INDEX.dbf' size  500M autoextend on next 100M maxsize 32767M;
exit;