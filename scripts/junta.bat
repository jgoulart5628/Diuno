@echo off
set DIR=dump_new
set ARQ=export.sql
rem type '' > %ARQ%
FOR %%F in (dump_new/*.sql) DO (
rem  echo %DIR%/%%F --  %ARQ%
  type %DIR%\%%F >> export.sql
)
