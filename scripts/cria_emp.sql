drop tablespace &1 including contents and datafiles;
create tablespace &1 datafile '/AUX/oracle/oradata/CASA/datafile/&1&2' size 1000M autoextend on next 100M maxsize 32767M ;
grant "CONNECT", "RESOURCE"  to  &1 identified by  &3;
ALTER USER &1 DEFAULT TABLESPACE &1 TEMPORARY TABLESPACE "TEMP" ACCOUNT UNLOCK ;
GRANT CREATE SESSION TO &1;
GRANT CREATE ANY VIEW TO &1;
GRANT UNLIMITED TABLESPACE TO &1;
EXIT;
