-- rem Dropa tablespace e recria
drop tablespace &1 including contents and datafiles;
create tablespace &1 datafile '&1.dbf' size 1000M autoextend on next 100M maxsize 32767M;
exit;
	