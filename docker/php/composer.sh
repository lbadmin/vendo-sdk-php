#!/usr/bin/env bash

COMMAND="/usr/local/bin/composer $@"

su -s /bin/bash www-data -p -c "$COMMAND"
