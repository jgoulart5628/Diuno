#!/bin/bash
sqlplus sys/jogola01@casa as sysdba @cria_emp.sql $1 $2
