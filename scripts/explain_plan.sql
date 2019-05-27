SELECT MIN(i.key_tabela) FROM produtos_fiscal_vendas i

SELECT * FROM   TABLE(DBMS_XPLAN.DISPLAY);
SELECT * FROM   TABLE(DBMS_XPLAN.DISPLAY('PLAN_TABLE','','BASIC'));


explain plan for 
select * from (
SELECT NULL AS det_imposto_IPI_clEnq, 
               NULL AS det_imposto_IPI_CNPJProd,
               NULL AS det_imposto_IPI_cSelo, 
               NULL AS det_imposto_IPI_qSelo, 
               '0' AS det_imposto_IPI_cEnq,  
               RIGHT('00' || COALESCE(t.cod_trib_ipi, nsp.cod_tributacao), 2) AS det_imposto_IPI_IPITrib_CST,
               CAST(CASE WHEN (NOT (nsp.perc_reducao >= 100 OR nsi.eh_servico = 'S')) THEN nsp.valor_tributado ELSE 0 END AS NUMERIC(18,2)) || '' AS det_imposto_IPI_IPITrib_vBC,
               CAST(CASE WHEN (NOT (nsp.perc_reducao >= 100 OR nsi.eh_servico = 'S')) THEN nsp.aliquota ELSE 0 END AS NUMERIC(18,2)) || '' AS det_imposto_IPI_IPITrib_pIPI, 
               CAST(CASE WHEN (NOT (nsp.perc_reducao >= 100 OR nsi.eh_servico = 'S')) THEN nsi.qtde_docto ELSE 0 END AS NUMERIC(18,4)) || '' AS det_imposto_IPI_IPITrib_qUnid,
               CAST(CASE WHEN (NOT (nsp.perc_reducao >= 100 OR nsi.eh_servico = 'S')) THEN pfv.aliquota_ipi_valor ELSE 0 END AS NUMERIC(18,4)) || '' AS det_imposto_IPI_IPITrib_vUnid,
               CAST(CASE WHEN (NOT (nsp.perc_reducao >= 100 OR nsi.eh_servico = 'S')) THEN nsp.valor_imposto ELSE 0 END AS NUMERIC(18,2)) || '' AS det_imposto_IPI_IPITrib_vIPI,
               RIGHT('00' || COALESCE(t.cod_trib_ipi, nsp.cod_tributacao), 2) AS det_imposto_IPI_IPINT_CST,
               COALESCE(pfv.tipo_aliquota_ipi, 'P') AS tipo_aliquota_ipi
               ,pfv.key_tabela as chavez    
               ,nsi.produto
               ,nsi.modelo
               ,nsi.tamanho
 FROM notaf_sai_item nsi, produtos_fiscal_vendas pfv, notaf_sai_impostos nsp,  notaf_sai_base nsb, tributacao t, tab_filial, tipo_imposto ti
 WHERE ti.tipo_imposto = 'IPI'
   and ti.cod_imposto = nsp.cod_imposto
   and tab_filial.codigo_filial = nsb.filial
--   and pfv.key_tabela  = (SELECT MIN(i.key_tabela) FROM produtos_fiscal_vendas i
--          WHERE i.codigo_materail = nsi.produto AND
--            i.modelo = nsi.modelo AND
--            i.tamanho = nsi.tamanho)
   and nsp.key_docto_base = nsi.key_docto_base
   and nsp.item = nsi.item
   and nsb.key_docto_base = nsi.key_docto_base
   and t.codigo_tributacao = nsp.cod_tributacao)  zzz
where zzz.chavez = (SELECT MIN(i.key_tabela) FROM produtos_fiscal_vendas i
                      WHERE i.codigo_materail = zzz.produto AND
                        i.modelo = zzz.modelo AND
                       i.tamanho = zzz.tamanho) 
                       