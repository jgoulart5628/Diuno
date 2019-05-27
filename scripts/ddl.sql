ALTER TABLE cnd_ord_fab
ADD	entrega_prevista      	date NULL;

ALTER TABLE cnd_ord_fab
ADD	inspecao_recebimento  	char(1) NULL;

ALTER TABLE cnd_ord_fab
ADD	inspecao_final        	varchar(50) NULL;

ALTER TABLE cnd_ord_fab
ADD	num_orc       		numeric(15,0) NULL;

ALTER TABLE cnd_ord_fab
ADD	saida_atrasada        	char(1) NULL;
