 commit; 
declare cursor cc is 
                 select 'alter table ' || table_name || ' enable constraint ' || constraint_name   as const
                    from user_constraints where table_name = 'NOTAF_SAI_IMPOSTOS' ;
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end;  

     /  
 spool off 
exit; 
