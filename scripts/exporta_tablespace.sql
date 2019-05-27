-- set transportable tablespace
EXEC SYS.DBMS_TTS.TRANSPORT_SET_CHECK(ts_list => 'TEST_DATA', incl_constraints => TRUE);

-- verifica se existem erros
SELECT * FROM transport_set_violations;
--
-- coloca a tablespace em read-only
ALTER TABLESPACE test_data READ ONLY;

--  exporta tablespace (shell)
-- $ expdp userid=system/password directory=temp_dir transport_tablespaces=test_data dumpfile=test_data.dmp logfile=test_data_exp.log
-- restaura read write
ALTER TABLESPACE test_data READ WRITE;
--
-- import tablespace no destino
-- usuario deve existir no destino com grant em create session e create table
-- $ impdp userid=system/password directory=temp_dir dumpfile=test_data.dmp logfile=test_data_imp.log transport_datafiles='/u01/app/oracle/oradata/DB11GB/test_data01.dbf'
--
-- checando a tablespace no banco destino:
SELECT tablespace_name, plugged_in, status
FROM   dba_tablespaces
WHERE  tablespace_name = 'TEST_DATA';
