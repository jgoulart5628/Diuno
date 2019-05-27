SELECT RIGHT(Concat('000', LTRIM(RTRIM(COALESCE(tributacao_icm.cod_trib_icm, nsp_icm.cod_tributacao)))), 3) AS cst_icms,
			 LEFT(nsi.cod_cfo, 4) AS cfop,
			 
         CAST(LEFT(CASE WHEN RIGHT(nsi.agrupamento_fiscal, 1) NOT IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9') THEN
                         SUBSTRING(nsi.agrupamento_fiscal, 1, LENGTH(nsi.agrupamento_fiscal) - 1)
                     ELSE
                         nsi.agrupamento_fiscal
                     END, 3) AS NUMERIC(9,6)) / 10 AS aliq_icms, /*os 23973*/
					  
			 COALESCE(SUM((nsc.valor_custo_item + nsc.valor_custo_armaz + nsc.valor_custo_outros + nsc.valor_custo_financ + nsc.valor_custo_transp + nsc.valor_custo_embal) + 
				           (CASE WHEN nsp_icm.valor_substituicao > 0 THEN nsp_icm.valor_substituicao ELSE 0 END) +
                       (SELECT SUM(CASE WHEN ABS(tipo_imposto.tira_soma_nada) = 1 THEN nsp.valor_imposto ELSE 0 END)
                        FROM notaf_sai_impostos nsp
                        JOIN tipo_imposto ON
                       		  tipo_imposto.cod_imposto = nsp.cod_imposto
                        WHERE nsp.key_docto_Base = nsi.key_docto_base AND
                       		   nsp.item = nsi.item)), 0.00) AS vl_opr,
			 
			 COALESCE(SUM(CASE WHEN nsi.eh_servico = 'N' THEN nsp_icm.valor_tributado ELSE 0 END), 0.00) AS vl_bc_icms,
			 COALESCE(SUM(CASE WHEN nsi.eh_servico = 'N' THEN nsp_icm.valor_imposto ELSE 0 END), 0.00) AS vl_icms, 
			 COALESCE(SUM(CASE WHEN nsi.eh_servico = 'N' THEN nsp_icm.valor_base_substituicao ELSE 0 END), 0.00) AS vl_bc_icms_st,
			 COALESCE(SUM(CASE WHEN nsi.eh_servico = 'N' THEN nsp_icm.valor_substituicao ELSE 0 END), 0.00) AS vl_icms_st,
			 COALESCE(SUM(CASE WHEN nsi.eh_servico = 'N' THEN nsp_icm.valor_nao_tributado + nsp_icm.valor_outros + nsp_icm.valor_base_substituicao ELSE 0 END), 0.00) AS vl_red_bc,
			 COALESCE(SUM(CASE WHEN NOT (nsp_ipi.perc_reducao >= 100 OR tab_filial.opcao_tributaria_federal = '0' OR nsi.eh_servico = 'S') THEN nsp_ipi.valor_imposto ELSE 0 END), 0.00) AS vl_ipi,
			 NULL AS cod_obs, /*OS 24732*/
			 (SELECT c.uf FROM notaf_sai_base i
			  JOIN cep ON cep.cod_cep = i.cep
			  JOIN cidade c ON c.cod_cidade = cep.cod_cidade
			  WHERE i.key_docto_base = :al_key_docto_base) AS uf
	FROM notaf_sai_item nsi
	JOIN notaf_sai_base nsb ON
		  nsi.key_docto_base = nsb.key_docto_base   
	LEFT JOIN notaf_sai_impostos nsp_icm ON
		  nsp_icm.key_docto_base = nsi.key_docto_base AND
		  nsp_icm.item = nsi.item AND
		  nsp_icm.cod_imposto = 'ICM'
	LEFT JOIN notaf_sai_impostos nsp_ipi ON
		  nsp_ipi.key_docto_base = nsi.key_docto_base AND
		  nsp_ipi.item = nsi.item AND
		  nsp_ipi.cod_imposto = 'IPI'
	LEFT JOIN notaf_sai_custos nsc ON
		  nsc.key_docto_base = nsi.key_docto_base AND
		  nsc.item = nsi.item
	LEFT JOIN documentos_fiscais df ON
		  nsb.docto_cod_fiscal = df.codigo_documento_fiscal
   LEFT JOIN tributacao tributacao_icm ON
        tributacao_icm.codigo_tributacao = nsp_icm.cod_tributacao
	LEFT JOIN tab_filial ON 
	     tab_filial.codigo_filial = nsb.filial
	WHERE nsb.key_docto_base = :al_key_docto_base 
  GROUP BY tributacao_icm.cod_trib_icm, 
           nsp_icm.cod_tributacao,
           nsi.cod_cfo,
           nsi.agrupamento_fiscal
	ORDER BY cst_icms,
				cfop,
				aliq_icms;
