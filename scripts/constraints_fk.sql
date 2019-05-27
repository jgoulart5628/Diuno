declare cursor xx is
select 'alter table '||table_name||' disable constraint '||constraint_name||' cascade ' as constra
from user_constraints
where constraint_type = 'R'
 and status = 'ENABLED';
--and table_name = 'NOTAF_ENT_ELETRONICA'
begin
   for xx_rec in xx
   loop
   execute immediate xx_rec.constra;
   end loop;
end;


declare cursor xx is
select 'alter table '||table_name||' ENABLE novalidate constraint '|| constraint_name || ';' as  constra
from user_constraints
where constraint_type = 'R'
and status = 'DISABLED';
begin
   for xx_rec in xx
   loop
   execute immediate xx_rec.constra;
   end loop;
end;






































