#!/bin/bash
# merge_new_stocks.sh

echo `date` " : Starting new stock merge"
/opt/bitnami/mysql/bin/mysql -t -uroot -proot trdsheet <<EOF
insert into stocks(isin,bloomberg_fin_code,description) 
select distinct isin , concat(ticker,
CASE local_currency_code 
WHEN 'USD' THEN ' US'
WHEN 'CAD' THEN ' CN'
ELSE ' ERR'
END) as bloomberg_fin_code,
security_description
from portfolio where isin not in (select isin from stocks);
EOF
x=$?
echo `date` " : Finished new stock merge. Status = " $x
