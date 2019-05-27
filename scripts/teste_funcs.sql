select ltrim(NOME_FANTASIA) um, rtrim(NOME_FANTASIA) dois from clientes where CODIGO_CLIENTE = 12

select RTRIM(LTRIM('   TESTE DE TRIM   ')); 

select left('Joao da Silva', 5)

select cast(convert(datetime, getdate(),120),4)



select cast(format(getdate(),'YYYY-MM-DD HH:mm:SS') as datetime)


DECLARE @datetime DATETIME
SET @datetime = GETDATE()
 
SELECT @datetime AS [DATE TIME],
CONVERT(VARCHAR(19), @datetime, 120) AS [NOW],
CONVERT(VARCHAR(11), @datetime, 120) AS [TODAY]
GO

SELECT FORMAT(CURRENT_TIMESTAMP,N'yyyyMMdd HH:mm:ss')



CREATE FUNCTION NOW () 
  returns varchar
  as 
begin
   declare @agora varchar(19)
   declare @datetime datetime
   set @datetime = current_timestamp
   return SELECT @agora = FORMAT(@datetime,N'yyyyMMdd HH:mm:ss')
end;
 

CREATE FUNCTION dbo.TRIM(@string VARCHAR(MAX))
RETURNS VARCHAR(MAX)
BEGIN
RETURN LTRIM(RTRIM(@string))
END
GO
SELECT dbo.TRIM(' String ')
GO