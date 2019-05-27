drop table mater;
create table mater
(
 CD_MATERIAL number(11), --12,11
 DS_MATERIAL_PADR varchar2(60), --10,20,30,40,50,52,57,58,60
 DS_MATERIAL_COMPL varchar2(50), --20,30,40,50
 IN_UNIDADE char(34), --2,3,5,12,13,18,20,21,22,25,26,27,34
 VL_MATERIAL varchar2(18), --n12,2 ,v14,v18  --nao numerico
 ST_MATERIAL char(7), --1,4,5,7
 TP_ITEM char(2) --1,2
);
