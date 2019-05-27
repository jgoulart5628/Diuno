 declare  
    varq_nfe    clob; 
    varq_canc   clob; 
    v_offset number := 1;
    v_file bfile := bfilename('ARQ_NFE','arq_tmp');
    v_file1 bfile := bfilename('ARQ_NFE','arq1_tmp');
    v_lob_length number;
    lb_file_exist boolean;
    ln_size number;
    ln_block_size number;
 begin
--   primeiro arquivo
  dbms_lob.fileopen(v_file);
  v_lob_length := dbms_lob.getlength(v_file);
  dbms_lob.createtemporary(varq_nfe,TRUE);
  dbms_lob.loadfromfile(varq_nfe,v_file,v_lob_length);
  dbms_lob.fileclose(v_file);
--  segundo  arquivo  
  sys.utl_file.fgetattr('ARQ_NFE_DIR','arq1_tmp',lb_file_exist,ln_size,ln_block_size);
  if lb_file_exist then
     dbms_lob.fileopen(v_file1);
     v_lob_length := dbms_lob.getlength(v_file1);
     dbms_lob.createtemporary(varq_canc,TRUE);
     dbms_lob.loadfromfile(varq_canc,v_file1,v_lob_length);
     dbms_lob.fileclose(v_file1);
  end if;   
end; 
  