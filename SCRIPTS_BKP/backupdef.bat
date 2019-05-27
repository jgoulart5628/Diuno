@echo off
rem Diretório onde está instalado o BB
set BB_PATH=c:\backupORA

rem Diretório onde serão enviados os backups
set BB_ORABKP_PATH=C:\backupORA

rem Diretório onde estão os archives
set BB_ORABKP_ARCH=C:\oracle\app\Administrator\oradata\dbprod\archive

rem Quantidade de dias que os archives devem permanecer em backup
set BB_ORABKP_TEMPO_ARCH=5

rem Usuário para executar o backups
rem Senha do usuário BB_ORABKP_LOGIN
rem String de conexão para conectar ao banco
set BB_ORABKP_LOGIN=backup
set BB_ORABKP_SENHA=oradbwin64
set BB_ORABKP_SERVICE=dbprod

rem Faz cópia remota dos backups?
set BB_ORABKP_REMOTO=0

rem Qual a máquina?
set BB_ORAREM_MAQUINA=fileserver03
rem Qual o compartilhamento?
set BB_ORAREM_COMP=senior\backup	
rem Qual usuário de rede?
set BB_ORAREM_USU_REDE=senior
rem Qual a senha do usuário?
set BB_ORAREM_USU_SENHA=karla2010
rem Unidade a ser mapeada
set BB_ORAREM_UNIDADE=S

rem SID do banco
set ORACLE_SID=dbprod

rem ARQUIVOS ESTÁTICOS PARA USO INTERNO DO SISTEMA

rem Arquivo SQL para o backup ARCHIVES
set BB_ARCH_SQL=%BB_PATH%\ext\delarch.sql
