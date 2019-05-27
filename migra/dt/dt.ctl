LOAD DATA
 INFILE 'dt2.txt'
 APPEND
 INTO TABLE dt
 FIELDS TERMINATED BY ';'
 (
dt_desativacao "to_date (:dt_desativacao, 'dd/mm/rrrr hh24:mi:ss')"
 ) 
