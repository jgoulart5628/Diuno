drop table fornec;
create table fornec
(
 NR_CPF_CGC varchar2(15), --nao numerico, 14,15
 TP_FAVORECIDO_D varchar2(20), --nao numerico --1,20
 NM_FAVORECIDO varchar2(120), --50,70,100,120
 TX_ENDERECO varchar2(100),
 NR_TELEFONE varchar2(30), --nao numerico --15,30
 TX_CIDADE varchar2(40), --30,40
 NR_BANCO varchar2(15), --nao numerico, 6,10,15
 NR_AGENCIA varchar2(15), --nao numerico,6,15
 SG_UF varchar2(10), --2,10
 NR_CTA_CORRENTE varchar2(17), --10,15,17 --nao numerico
 NR_CEP varchar2(11), --nao numerico,8,9,11
 NR_INSCR_MUNICIPAL varchar2(20), --6, 15 --nao numerico
 NR_INSCR_ESTADUAL varchar2(20), --8 --nao numerico
 TP_RAMO_ATIV_D varchar2(16), --1,10,16 --nao numerico
 IN_SUSPENSAO_D varchar2(16), --1,16
 UG_CD_UG_SUSPEN varchar(20), --?
 IN_COLETIVO_D varchar(20), --?
 IN_EVENTUAL_D varchar2(15), --1,15
 ST_ATIVO varchar2(20), --1,20
 DT_DESATIVACAO date,
 NR_AGENCIA_DIR varchar2(15), --5
 NR_BANCO_DIR varchar2(15), --5 --nao numerico
 NR_CTA_CORRENTE_DIR varchar2(15), --10 --nao numerico
 CD_ORIGEM number(15), --1
 TP_NAT_JURIDICA number(2), --1
 FL_ZONAFRANCASOCIAL varchar(20)
);
