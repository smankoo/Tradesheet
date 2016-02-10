#!/bin/bash
# update_portfolio.sh

/home/bitnami/scripts/pull_portfolio.sh 2>&1 >> /home/bitnami/scripts/log/pull_portfolio.log

if [ $? -ne 0 ]; then
	echo `date` " : Error while pulling portfolio. Load not run" >> /home/bitnami/scripts/log/load_portfolio.log
	exit
fi

/home/bitnami/scripts/load_portfolio.sh 2>&1 >> /home/bitnami/scripts/log/load_portfolio.log
