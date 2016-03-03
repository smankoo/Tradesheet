#!/bin/bash

n=0
cat chat.txt | head -20 | while read line
do
	n=$((n+1))
	echo $line | egrep "( PM - | AM - )" | grep ": " >/dev/null 2>&1
	
	if [ $? -eq 0 ]; then
		echo $line
	fi
done
