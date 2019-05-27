CREATE TABLE adodb_logsql (
   created date NOT NULL,
   sql0 varchar(250) NOT NULL,
   sql1 varchar(4000) NOT NULL,
   params varchar(4000),
   tracer varchar(4000),
   timer decimal(16,6) NOT NULL
  )
  