@echo off
set DIR="DDL_NUCLEO/views"
FOR %%F in (DDL_NUCLEO/views/*.sql) DO (
  type 'exit;' >> %%F
  sqlplus resolpec/resolpec@clientes @%DIR%%%F
@rem php load_tabela.php %DIR%%%F
)

