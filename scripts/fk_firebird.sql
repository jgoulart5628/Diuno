select
rc.rdb$constraint_name AS fk_name
, rcc.rdb$relation_name AS child_table
, list(distinct isc.rdb$field_name,',') AS child_column
, rcp.rdb$relation_name AS parent_table
, list(distinct isp.rdb$field_name,',') AS parent_column
, rc.rdb$update_rule AS update_rule
, rc.rdb$delete_rule AS delete_rule
from rdb$ref_constraints AS rc
inner join rdb$relation_constraints AS rcc
on rc.rdb$constraint_name = rcc.rdb$constraint_name
inner join rdb$index_segments AS isc
on rcc.rdb$index_name = isc.rdb$index_name
inner join rdb$relation_constraints AS rcp
on rc.rdb$const_name_uq  = rcp.rdb$constraint_name
inner join rdb$index_segments AS isp
on rcp.rdb$index_name = isp.rdb$index_name
where rcc.rdb$relation_name = 'FRUTA_MOVTO_CLASSIFICADA'
group by rc.rdb$constraint_name, rcc.rdb$relation_name, rcp.rdb$relation_name, rc.rdb$update_rule, rc.rdb$delete_rule
