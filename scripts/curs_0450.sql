/*
SELECT PLAN_TABLE_OUTPUT 
  FROM TABLE(DBMS_XPLAN.DISPLAY());
  
EXPLAIN PLAN FOR  
*/

SELECT tp.codigo_texto_padrao AS cod_inf,
			 tp.texto_padrao AS txt 
	FROM texto_padrao tp
	WHERE EXISTS (SELECT 1 
					  FROM notaf_sai_item nsi 
					  JOIN notaf_sai_base nsb ON 
							 nsb.key_docto_base = nsi.key_docto_base 
					  JOIN documentos_fiscais df ON
							 df.codigo_documento_fiscal = nsb.docto_cod_fiscal
					  WHERE (nsb.texto_padrao_1 = tp.codigo_texto_padrao OR 
								nsb.texto_padrao_2 = tp.codigo_texto_padrao OR 
								nsb.texto_padrao_3 = tp.codigo_texto_padrao OR 
								nsi.texto_padrao_1 = tp.codigo_texto_padrao ) AND 
								nsb.docto_data BETWEEN '2014-09-01' AND '2014-09-30' AND 
								nsb.filial = '1' AND
								df.emite_livros_fiscais IN ('A', 'S') AND  
								SUBSTRING(nsi.cod_cfo, 1, 1) >= '5' AND
								nsb.data_cancelamento IS NULL AND
								LTRIM(RTRIM(df.cod_modelo_fiscal)) <> '55' /*Esta condicao existe porque nfe nao gera itens*/
			UNION SELECT 1 
					  FROM notaf_ent_item nei 
					  JOIN notaf_ent_base neb ON 
							 neb.key_docto_base = nei.key_docto_base 
					  JOIN documentos_fiscais df ON
							 df.codigo_documento_fiscal = neb.docto_cod_fiscal
					  WHERE (neb.texto_padrao_1 = tp.codigo_texto_padrao OR 
								nei.texto_padrao_1 = tp.codigo_texto_padrao) AND 
								neb.data_movto BETWEEN '2014-09-01' AND '2014-09-30' AND 
								neb.filial = '1' AND  
								df.emite_livros_fiscais IN ('A', 'E') AND
								SUBSTRING(nei.cod_cfo, 1, 1) < '5' AND
								neb.data_cancelamento IS NULL);
