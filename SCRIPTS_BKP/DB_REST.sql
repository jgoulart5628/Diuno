drop tablespace meta including contents and datafiles;
drop tablespace sapiens_data  including contents and datafiles;
drop tablespace sapiens_index including contents and datafiles;
drop tablespace sapin86_data  including contents and datafiles;
drop tablespace sapin86_index including contents and datafiles;
drop tablespace sapiens_teste_data  including contents and datafiles;
drop tablespace sapiens_teste_index including contents and datafiles;
create tablespace META datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\META.DBF' size 2000M autoextend on next 100M mazsize 32767M;
create tablespace SAPIENS_DATA  datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_DATA.DBF'  size 2000M autoextend on next 100M mazsize 32767M;
create tablespace SAPIENS_INDEX datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_INDEX.DBF' size 2000M autoextend on next 100M mazsize 32767M;
create tablespace SAPIN86_DATA  datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIN86_DATA.DBF'  size 1000M autoextend on next 100M mazsize 32767M;
create tablespace SAPIN86_INDEX datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIN86_INDEX.DBF' size  500M autoextend on next 100M mazsize 32767M;
create tablespace SAPIENS_TESTE_DATA  datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_TESTE_DATA.DBF'   size 1000M autoextend on next 100M mazsize 32767M;
create tablespace SAPIENS_TESTE_INDEX datafile 'C:\ORACLE\11G\ORAHOME_1\DATABASE\SAPIENS_TESTE_INDEX.DBFF' size  500M autoextend on next 100M mazsize 32767M;

