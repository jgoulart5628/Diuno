exec dbms_stats.gather_schema_stats(ownname => 'COOP');
SELECT table_name, tablespace_name, num_rows
 FROM dba_tables WHERE owner='COOP' ORDER BY num_rows DESC;
  