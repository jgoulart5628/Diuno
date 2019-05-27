-- verifica escala errada
select ' alter table ' || table_name || ' modify ' || column_name || ' ' || data_type || '(' || data_precision || ',' || ( data_scale * -1) || ');'
from SYS.ALL_TAB_COLS where owner = 'ALUMIGLASS' and data_type = 'NUMBER' and data_scale < 0
--
--

DECLARE
      CURSOR consCols (theCons VARCHAR2, theOwner VARCHAR2) IS
        select * from user_cons_columns
            where constraint_name = theCons and owner = theOwner
            order by position;
      firstCol BOOLEAN := TRUE;
    begin
        -- For each constraint
        FOR cons IN (select * from user_constraints
            where delete_rule = 'NO ACTION'
            and constraint_name not like '%MODIFIED_BY_FK'  -- these constraints we do not want delete cascade
            and constraint_name not like '%CREATED_BY_FK'
            order by table_name)
        LOOP
            -- Drop the constraint
            DBMS_OUTPUT.PUT_LINE('ALTER TABLE ' || cons.OWNER || '.' || cons.TABLE_NAME || ' DROP CONSTRAINT ' || cons.CONSTRAINT_NAME || ';');
            -- Re-create the constraint
            DBMS_OUTPUT.PUT('ALTER TABLE ' || cons.OWNER || '.' || cons.TABLE_NAME || ' ADD CONSTRAINT ' || cons.CONSTRAINT_NAME 
                                        || ' FOREIGN KEY (');
            firstCol := TRUE;
            -- For each referencing column
            FOR consCol IN consCols(cons.CONSTRAINT_NAME, cons.OWNER)
            LOOP
                IF(firstCol) THEN
                    firstCol := FALSE;
                ELSE
                    DBMS_OUTPUT.PUT(',');
                END IF;
                DBMS_OUTPUT.PUT(consCol.COLUMN_NAME);
            END LOOP;                                    

            DBMS_OUTPUT.PUT(') REFERENCES ');

            firstCol := TRUE;
            -- For each referenced column
            FOR consCol IN consCols(cons.R_CONSTRAINT_NAME, cons.R_OWNER)
            LOOP
                IF(firstCol) THEN
                    DBMS_OUTPUT.PUT(consCol.OWNER);
                    DBMS_OUTPUT.PUT('.');
                    DBMS_OUTPUT.PUT(consCol.TABLE_NAME);        -- This seems a bit of a kluge.
                    DBMS_OUTPUT.PUT(' (');
                    firstCol := FALSE;
                ELSE
                    DBMS_OUTPUT.PUT(',');
                END IF;
                DBMS_OUTPUT.PUT(consCol.COLUMN_NAME);
            END LOOP;                                    

            DBMS_OUTPUT.PUT_LINE(')  ON DELETE CASCADE  ENABLE VALIDATE;');
        END LOOP;
    end;