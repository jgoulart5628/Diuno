SELECT 'L', clientes.codigo_cliente, clientes.filial_cliente,   clientes.razao_social, clientes.cep_cliente, clientes.fone_a,   movto_financeiro_lancamentos.data_mvto, movto_financeiro_lancamentos.codigo_lcto_financeiro, movto_financeiro_lancamentos.valor_pagamento, movto_financeiro_lancamentos.valor_desconto,   movto_financeiro_lancamentos.tipo_deb_cre,   movto_financeiro_lancamentos.historico,   movto_financeiro_lancamentos.data_registro,   movto_financeiro_lancamentos.rec_pag,   movto_financeiro_lancamentos.data_sistema,   movto_financeiro_lancamentos.cancelado,   movto_financeiro_lancamentos.key_movto_taxa,   movto_financeiro_titulo.numero_documento,   movto_financeiro_titulo.parcela,   movto_financeiro_titulo.data_vencimento,   movto_financeiro_titulo.observacao,   movto_financeiro_banco.cod_banco,   movto_financeiro_banco.cod_agencia,   movto_financeiro_banco.num_recibo,   movto_financeiro_banco.cod_taxa,cast(0 as numeric(9,2)), movto_financeiro_lancamentos.sequencia, movto_financeiro_lancamentos.valor_juros, movto_financeiro_lancamentos.valor_despesas  
,movto_financeiro_titulo.key_emissao
 FROM movto_financeiro_titulo  LEFT OUTER JOIN clientes  ON 
  movto_financeiro_titulo.codigo_cliente = clientes.codigo_cliente AND movto_financeiro_titulo.filial_cliente = clientes.filial_cliente,
  movto_financeiro_lancamentos  
LEFT OUTER JOIN movto_financeiro_banco  ON movto_financeiro_lancamentos.key_movto_taxa = movto_financeiro_banco.key_movto_taxa, lancamentos_financeiros   
WHERE ( movto_financeiro_lancamentos.key_emissao = movto_financeiro_titulo.key_emissao ) and          ( movto_financeiro_lancamentos.codigo_lcto_financeiro = lancamentos_financeiros.codigo_lancamento )     
AND movto_financeiro_titulo.filial ='1' AND movto_financeiro_lancamentos.rec_pag = 'R' and movto_financeiro_lancamentos.data_registro >= '2014-01-21' 
AND movto_financeiro_lancamentos.data_registro <= '2014-01-21' AND movto_financeiro_lancamentos.cancelado is null  AND movto_financeiro_titulo.cancelado is null  
AND movto_financeiro_lancamentos.key_emissao > 0  and clientes.codigo_cliente >= 1231 AND clientes.codigo_cliente <= 1231 



SELECT 'L'
        , cl.codigo_cliente
        , cl.filial_cliente
        , cl.razao_social
        , cl.cep_cliente
        , cl.fone_a
        , ml.data_mvto
        , ml.codigo_lcto_financeiro
        , ml.valor_pagamento
        , ml.valor_desconto
        , ml.tipo_deb_cre
        , ml.historico
        , ml.data_registro
        , ml.rec_pag
        , ml.data_sistema
        , ml.cancelado
        , ml.key_movto_taxa
        , mt.numero_documento
        , mt.parcela
        , mt.data_vencimento
        , mt.observacao
        , mb.cod_banco
        , mb.cod_agencia
        , mb.num_recibo
        , mb.cod_taxa,cast(0 as numeric(9,2))
        , ml.sequencia
        , ml.valor_juros
        , ml.valor_despesas  
        , mt.key_emissao
 FROM movto_financeiro_titulo mt,  clientes cl, movto_financeiro_lancamentos ml, movto_financeiro_banco mb, lancamentos_financeiros lf 
where mt.codigo_cliente = cl.codigo_cliente
  AND mt.filial_cliente = cl.filial_cliente
  AND ml.key_movto_taxa = mb.key_movto_taxa
  AND ml.key_emissao = mt.key_emissao
  AND ml.codigo_lcto_financeiro = lf.codigo_lancamento      
  AND mt.filial ='1' 
  AND ml.rec_pag = 'R'
  AND ml.data_registro between '2014-01-21' and '2014-01-21'  
  AND ml.cancelado is null
  AND mt.cancelado is null  
  AND ml.key_emissao > 0 
  AND cl.codigo_cliente between 1231 AND  1231 
