 declare cursor xx is
    SELECT 'alter table ' || table_name || ' modify  constraint ' || constraint_name || ' disable cascade ' constra 
     FROM USER_CONSTRAINTS
    WHERE R_CONSTRAINT_NAME = (select xx.INDEX_NAME from user_constraints xx
                                where xx.table_name = '$tabela' 
                                  and XX.INDEX_NAME IS NOT NULL) 
     and status = 'ENABLED';
 begin
   for xx_rec in xx
    loop
       execute immediate(xx_rec.constra); 
       commit;
    end loop;
 end;
 
