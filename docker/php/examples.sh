#!/usr/bin/env bash

COMMAND="/var/www/run_example.sh $@"

su -s /bin/bash www-data -p -c "$COMMAND"