#!/usr/bin/env bash

COMMAND="php /var/www/composer.phar $@"

su -s /bin/bash www-data -p -c "$COMMAND"
