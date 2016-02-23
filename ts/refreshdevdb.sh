#!/bin/bash
#refreshdevdb.sh

if [ `hostname` != "sumeet-dell" ]; then
    echo "Hostname " `hostname` " is not dev host. Exiting."
    exit;
fi

