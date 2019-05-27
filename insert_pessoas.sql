-- Script para inserir registros na tabela pes_pessoas, pes_pessoa_fisica ou pes_pessoa_juridica, pes_fornecedor
-- usando a entrada da tabela fornec, criada por dados importados pelo DBA da Diuno
 select ' insert into pes_pessoa (id, NM_PESSOA, TP_SITUACAO_PESSOA, TP_PESSOA, IND_PESSOA_ESTRANGEIRA, NR_DOCUMENTO_ESTRANGEIRO,
 NR_VERSAO, DT_CRIADO_EM, COD_USU_CRIADO, DT_ALTERADO_EM, COD_USU_ALTERADO) values (' || (select * from (select a.id + 1 as novoid
from pes_pessoa a  left outer join pes_pessoa r on a.id + 1 = r.id where r.id is null) where rownum = 1) || ',' ||
 a.nm_favorecido || ', 1, "J", 0, ' || current_timestamp || ', 1);' from fornec a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica b
 );
 /*
 PES_PESSOA
ID                       NOT NULL NUMBER(18)          
NM_PESSOA                NOT NULL VARCHAR2(128 CHAR) 
TP_SITUACAO_PESSOA       NOT NULL NUMBER(1)          
TP_PESSOA                NOT NULL CHAR(1)            
IND_PESSOA_ESTRANGEIRA   NOT NULL NUMBER(1)          
NR_DOCUMENTO_ESTRANGEIRO          VARCHAR2(32)       
NR_VERSAO                         NUMBER(10)         
DT_CRIADO_EM             NOT NULL TIMESTAMP(6)       
COD_USU_CRIADO           NOT NULL NUMBER(18)         
DT_ALTERADO_EM                    TIMESTAMP(6)       
COD_USU_ALTERADO                  NUMBER(18)  


select Nr_cpf_cgc, case when lengthb(nr_cpf_cgc) <= 11 then 'cpf' else 'cgc' end tipo from fornec 

-- pegar os números vagos

select * from (select a.id + 1 as novoid
from pes_pessoa a
  left outer join pes_pessoa r on a.id + 1 = r.id
where r.id is null)
where rownum = 1;

---------------------

declare cursor fornec is
select * from fornec a where a.nr_cpf_cgc not in (select cnpj from pes_pessoa_juridica 
 );
linha_insert varchar(500);
v_file_id UTL_FILE.FILE_TYPE;
 begin
 v_file_id := UTL_FILE.FOPEN('c:\tmp','output.sql','W');
for xx.rec in fornec
  loop
 linha_insert :=  select ' insert into pes_pessoa (id, NM_PESSOA, TP_SITUACAO_PESSOA, TP_PESSOA, IND_PESSOA_ESTRANGEIRA, NR_DOCUMENTO_ESTRANGEIRO,
 NR_VERSAO, DT_CRIADO_EM, COD_USU_CRIADO, DT_ALTERADO_EM, COD_USU_ALTERADO) values (' || (select (max(id) + 1) as novoid
from pes_pessoa a) || ',' ||  xx.rec.nm_favorecido || ', 1, "J", 0, ' || current_timestamp || ', 1);';
 utl_file.put_line(v_file_id,v_rec);
  end loop;
 end; 
 utl_file.fclose(v_file_id);
end;  

FORNEC                                                           PES_PESSOA

"NR_CPF_CGC" VARCHAR2(15 BYTE), 
	"TP_FAVORECIDO_D" VARCHAR2(20 BYTE),                       TP_PESSOA                NOT NULL CHAR(1)            
	"NM_FAVORECIDO" VARCHAR2(120 BYTE),                        NM_PESSOA       NOT NULL VARCHAR2(128 CHAR) 
	"TX_ENDERECO" VARCHAR2(100 BYTE), 
	"NR_TELEFONE" VARCHAR2(30 BYTE), 
	"TX_CIDADE" VARCHAR2(40 BYTE), 
	"NR_BANCO" VARCHAR2(15 BYTE), 
	"NR_AGENCIA" VARCHAR2(15 BYTE), 
	"SG_UF" VARCHAR2(10 BYTE), 
	"NR_CTA_CORRENTE" VARCHAR2(17 BYTE), 
	"NR_CEP" VARCHAR2(11 BYTE), 
	"NR_INSCR_MUNICIPAL" VARCHAR2(20 BYTE), 
	"NR_INSCR_ESTADUAL" VARCHAR2(20 BYTE), 
	"TP_RAMO_ATIV_D" VARCHAR2(16 BYTE), 
	"IN_SUSPENSAO_D" VARCHAR2(16 BYTE), 
	"UG_CD_UG_SUSPEN" VARCHAR2(20 BYTE), 
	"IN_COLETIVO_D" VARCHAR2(20 BYTE), 
	"IN_EVENTUAL_D" VARCHAR2(15 BYTE), 
	"ST_ATIVO" VARCHAR2(20 BYTE), 
	"DT_DESATIVACAO" DATE, 
	"NR_AGENCIA_DIR" VARCHAR2(15 BYTE), 
	"NR_BANCO_DIR" VARCHAR2(15 BYTE), 
	"NR_CTA_CORRENTE_DIR" VARCHAR2(15 BYTE), 
	"CD_ORIGEM" NUMBER(15,0), 
	"TP_NAT_JURIDICA" NUMBER(2,0), 
	"FL_ZONAFRANCASOCIAL" VARCHAR2(20 BYTE)
   ) SEGMENT CREATION IMMEDIATE 
*/