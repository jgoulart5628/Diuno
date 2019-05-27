@echo off
set DIR="dump_new/"
FOR %%F in (dump_new/*.sql) DO (
  type 'exit;' >> %%F
  sqlplus semear/semear@clientes @%DIR%%%F
@rem php load_tabela.php %DIR%%%F
)

