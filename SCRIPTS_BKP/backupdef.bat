@echo off
rem Diret�rio onde est� instalado o BB
set BB_PATH=c:\backupORA

rem Diret�rio onde ser�o enviados os backups
set BB_ORABKP_PATH=C:\backupORA

rem Diret�rio onde est�o os archives
set BB_ORABKP_ARCH=C:\oracle\app\Administrator\oradata\dbprod\archive

rem Quantidade de dias que os archives devem permanecer em backup
set BB_ORABKP_TEMPO_ARCH=5

rem Usu�rio para executar o backups
rem Senha do usu�rio BB_ORABKP_LOGIN
rem String de conex�o para conectar ao banco
set BB_ORABKP_LOGIN=backup
set BB_ORABKP_SENHA=oradbwin64
set BB_ORABKP_SERVICE=dbprod

rem Faz c�pia remota dos backups?
set BB_ORABKP_REMOTO=0

rem Qual a m�quina?
set BB_ORAREM_MAQUINA=fileserver03
rem Qual o compartilhamento?
set BB_ORAREM_COMP=senior\backup	
rem Qual usu�rio de rede?
set BB_ORAREM_USU_REDE=senior
rem Qual a senha do usu�rio?
set BB_ORAREM_USU_SENHA=karla2010
rem Unidade a ser mapeada
set BB_ORAREM_UNIDADE=S

rem SID do banco
set ORACLE_SID=dbprod

rem ARQUIVOS EST�TICOS PARA USO INTERNO DO SISTEMA

rem Arquivo SQL para o backup ARCHIVES
set BB_ARCH_SQL=%BB_PATH%\ext\delarch.sql
