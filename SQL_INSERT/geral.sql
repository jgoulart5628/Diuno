set define off  
spool NOTAF_SAI_IMPOSTOS.log  
 declare cursor cc is 
                 select 'alter table ' || table_name || ' disable constraint ' || constraint_name  || ' cascade '  as const
                    from user_constraints where table_name = 'NOTAF_SAI_IMPOSTOS' ;
              begin
              for cc_rec in cc
            loop
                 execute immediate cc_rec.const;    
           end loop;
         end; 
     / 
     
 commit; 
delete from  NOTAF_SAI_IMPOSTOS ; 
 commit;  
