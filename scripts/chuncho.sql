declare cursor xx is
select chave  from (
select codigo_produto, modelo, tamanho, UNIDADE_MEDIDA, num_lista , to_char(DATA_ULTIMA_DIGITACAO,'yyyymmdd') digita, rowid as chave,
  count(*)  over (partition by codigo_produto, modelo, tamanho, UNIDADE_MEDIDA, num_lista order by codigo_produto, modelo, tamanho, UNIDADE_MEDIDA, num_lista) conta
 , row_number() over (partition by codigo_produto, modelo, tamanho, UNIDADE_MEDIDA, num_lista order by codigo_produto, modelo, tamanho, UNIDADE_MEDIDA, num_lista) ordem
-- count(*)
from lista_preco 
 WHERE CODIGO_PRODUTO = '4065'
 )
 where conta > 1 and ordem < 2;
begin
   for xx_rec in xx
   loop
     delete from lista_preco where rowid = xx_rec.chave;
     commit;
   end loop;
end;   
