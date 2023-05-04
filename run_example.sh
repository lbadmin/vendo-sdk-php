#!/usr/bin/env bash

if [[ -z "$1" ]]
then
    echo "Runs example script with selected environment values";
    echo "Copy .env.example to .env_prod or .env_dev or .env_staging"
    echo " - then you can use selected settings just by adding this postfix as second parameter";
    echo "usage example: ./run_example.sh crypto_payment dev|staging //default value is prod"
else
    example_script=$(find examples -name "$1.php")

    if [[ -z "$2" ]]
    then
      environment=".env_prod"
    else
      environment=".env_$2"
    fi


    if [[ -f "$example_script" && -f "$environment" ]]
      then
        echo $(cat $environment | xargs)
        export $(cat $environment | xargs)
        php -d error_reporting=E_ERROR "$example_script"
      else
        echo "There is no such example script or there is no settings file: $1, $environment"
   fi
fi