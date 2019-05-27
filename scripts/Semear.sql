select bb.filial, bb.produto, bb.lote, bb.datax, saldo  from (
select filial,  produto, lote, max(datam) datax from vestoque_dia 
where datam <= '2005-07-25'
  group by filial, produto, lote ) bb,  vestoque_dia cc
where cc.produto = bb.produto 
  and cc.datam = bb.datax
  and cc.filial = bb.filial
  and cc.lote   = bb.lote
  order by 1, 2, 3, 4

create or replace view VESTOQUE_DIA as

select es.filial, es.produto, to_char(es.data_movimento,'YYYY-MM-dd') datam, es.lote, sum(es.quantidade) qtde
,SUM(es.quantidade) OVER (partition by produto ORDER BY es.filial, es.produto, es.data_movimento,es.lote  ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) as saldo, 
   pr.codigo_barras, pr.unidade_medida_estoque, pr.nome_completo
 from movto_estoque  es, produtos pr
where es.produto = pr.codigo_material
  and pr.subgrupo = '13'
--  and es.produto = '3'
group by es.filial, es.produto, es.data_movimento, es.lote, pr.codigo_barras, pr.unidade_medida_estoque, pr.nome_completo, es.quantidade 
order by es.filial, es.produto, es.data_movimento, es.lote


<<< [16:47:15] Regina Arruda: vou lhe mostrar o select do extrator para obter estoque
Obtendo Estoque de Produtos da empresa 10

Select  substring( cast(S0.codi_rev as varchar(20)) from 1 for 20),

substring( cast(S0.codi_fab as varchar(20)) from 1 for 20),

substring( cast(S0.codi_pro as varchar(20)) from 1 for 20),

substring( cast(S0.desc_pro as varchar(60)) from 1 for 60),

substring( cast(S0.unid_pro as varchar(10)) from 1 for 10),

substring( cast(S0.lote_pro as varchar(20)) from 1 for 20),

substring( cast(S0.barr_pro as varchar(14)) from 1 for 14),

cast(S0.date_pro as date),

cast(S0.qtde_pro as numeric(18,3)),

cast(S0.qtdi_pro as numeric(18,3)),

cast(S0.qttr_pro as numeric(18,3)),

cast(S0.qtbl_pro as numeric(18,3)),

cast(S0.cmed_pro as numeric(18,4)),

cast(S0.cuen_pro as numeric(18,4))

from BASF_PRODUCTSTOCK S0



where S0.codi_rev = 10 and
S0.date_pro = '30.11.2014' and
S0.codi_fab in ('BASF') and

( S0.qtde_pro > 0 or S0.qtdi_pro > 0 or S0.qttr_pro > 0 or S0.qtbl_pro > 0 )




up vote 4 down vote
	

To use parameters in a view one way is to create a package which will set the values of your parameters and have functions that can be called to get those values. For example:

create or replace package MYVIEW_PKG as
  procedure SET_VALUES(FROMDATE date, TODATE date);

  function GET_FROMDATE
    return date;

  function GET_TODATE
    return date;
end MYVIEW_PKG;

create or replace package body MYVIEW_PKG as
  G_FROM_DATE   date;
  G_TO_DATE     date;

  procedure SET_VALUES(P_FROMDATE date, P_TODATE date) as
  begin
    G_FROM_DATE := P_FROMDATE;
    G_TO_DATE := P_TODATE;
  end;

  function GET_FROMDATE
    return date is
  begin
    return G_FROM_DATE;
  end;

  function GET_TODATE
    return date is
  begin
    return G_TO_DATE;
  end;
end MYVIEW_PKG;

Then your view can be created thus:

create or replace view myview as
    select 
        d.dateInRange as dateval,
        eventdesc,
        nvl(td.dist_ucnt, 0) as dist_ucnt
    from (
        select 
            MYVIEW_PKG.GET_FROMDATE + rownum - 1 as dateInRange
        from all_objects
        where rownum <= MYVIEW_PKG.GET_FROMDATE - MYVIEW_PKG.GET_TODATE + 1
    ) d
    left join (
        select 
            to_char(user_transaction.transdate,'dd-mon-yyyy') as currentdate,
            count(distinct(grauser_id)) as dist_ucnt,
            eventdesc 
        from
            gratransaction, user_transaction 
      where gratransaction.id = user_transaction.trans_id and 
      user_transaction.transdate between MYVIEW_PKG.GET_FROMDATE and MYVIEW_PKG.GET_TODATE
        group by  to_char(user_transaction.transdate, 'dd-mon-yyyy'), eventdesc 
    ) td on td.currentdate = d.dateInRange order by d.dateInRange asc;
    
