cat fornec.bad|cut -f20 -d';'>dt2.txt
more dt2.txt
exit
for i in `cat fornec.bad|cut -f20 -d';'`
do
  echo "[$i]"
done
#echo "["`cat fornec.bad|cut -f20 -d';' |more`"]" 
