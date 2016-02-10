#!/bin/bash
#load_portfolio.sh

echo `date` " : Starting portfolio load"
/opt/bitnami/mysql/bin/mysql -t -uroot -proot trdsheet <<EOF
-- Archive the current portfolio
insert into portfolio_arch select now() archived_at,portfolio.* from portfolio;

-- Delete old archives
delete from portfolio_arch where archived_at < (select curdate() - interval '30' day);

-- Delete old portfolio
truncate table portfolio_load;

-- Load new portfolio
load data local infile '/home/bitnami/cibc-box/TRDSHT_Asset_And_Accrual_Detail_LATEST.csv'
into table portfolio_load fields terminated by ','
enclosed by '"'
lines terminated by '\n'
IGNORE 1 LINES
(reporting_account_number, reporting_account_name, source_account_number, source_account_name, as_of_date, security_description_1, security_description_2, asset_code, asset_category, sector_name, position_type, acct_base_currency_code, acct_base_currency_name, local_currency_code, local_currency_name, country_code, country_name, shares_par, base_cost, local_cost, base_price, local_price, base_net_income_receivable, local_net_income_receivable, base_market_value, local_market_value, base_notional_cost, local_notional_cost, base_notional_value, local_notional_value, coupon_rate, maturity_date, base_unrealized_gain_loss, local_unrealized_gain_loss, base_unrealized_currency_gain_loss, base_net_unrealized_gain_loss, gen_ledger_acct, pay_date, report_run_date, percent_of_total, link_ref, issuer_id, counterparty_name, exchange_rate, original_strike_price, current_strike_price, mellon_security_id, cins, cusip, ticker, isin, sedol, valoren, sicovam, wpk, quick, underlying_sec_id, loan_id, manager, counterparty, derivative_type, acctg_status_update_est, underlying_security_description_1, underlying_security_description_2, underlying_security_cusip, underlying_security_cins, underlying_security_isin, underlying_security_sedol, underlying_security_ticker, underlying_security_sicovam, underlying_security_valoren, underlying_security_wpk, underlying_security_quick, underlying_security_loan_id, underlying_security_manager, accounting_status);
EOF
x=$?
echo `date` " : Finished portfolio load. Status = " $x
