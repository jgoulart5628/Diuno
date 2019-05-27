set serveroutput on
declare cursor ana is
 select table_name as tabela, 'analyze table ' || table_name || ' compute statistics for all indexes '  analisa from user_tables;
 msg varchar2(50);        
begin
   for ana_rec in ana
  loop
    msg := ana_rec.tabela;
    execute immediate ana_rec.analisa; 
    DBMS_OUTPUT.PUT_LINE (msg || ': Done!');	
  end loop;
end;  
