#!/bin/bash
i=1
for arquivo in `ls -1 *.csv`; do 
   EXT=$(echo $arquivo | awk -F . '{print $2}')
   echo $EXT;
#   DATA=$(date -r $arquivo +"%s")
#   SOMA=$(expr $DATA + 86400)
#   DATAFINAL=$(date -d @$SOMA +"%d%m%y")
#   mv -v "$arquivo" "PD-$DATAFINAL-$i.$EXT"
   let i=i+1
done 
