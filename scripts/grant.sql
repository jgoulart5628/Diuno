begin
	declare maximo_cod numeric;
	for reg as curs cursor for ( select 'grant select on ' || table_name || ' to consulta;'  granta  from systab where creator = 1 ) do
		execute immediate granta;
	end for;
end;