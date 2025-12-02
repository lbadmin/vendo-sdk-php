# Developer notes

## Execute unit tests

You need:
- php 7.4
- xdebug 3


    vendor/bin/phpunit


## Docker and running examples 

### PHP version: 8.1.2
  - to change the PHP version you have to modify docker/php/Dockerfile

## Install and run:

1. run container


`  docker compose up --build -d`


2. then install libraries:

`docker compose exec vendo-sdk-php _composer install`

3. Next, you have to copy the `.env.staging` file to something like `.env_dev` or `.env_merchant1` and fill up parameters with correct values.

4. To run the example you have to run the command:

`docker compose exec vendo-sdk-php _examples [example name (without .php)] [yours env file postfix, for example dev]
`

Example:

`docker compose exec vendo-sdk-php _examples crypto_payment dev`

## Google Pay Token Setup

For Google Pay examples (`google_pay_payment.php`, `google_pay_verification.php`), you need to set the Google Pay token environment variable:

**Option 1: Set in your shell session:**
```bash
export VENDO_GOOGLE_PAY_TOKEN='{"signature":"ME...","protocolVersion":"ECv2","signedMessage":"..."}'
```

**Option 2: Add to your `.env` file:**
Add the following line to your `.env_dev` or `.env_merchant1` file:
```
VENDO_GOOGLE_PAY_TOKEN='{"signature":"ME...","protocolVersion":"ECv2","signedMessage":"..."}'
```

**Note:** The token has a few days lifetime, so one setup should last for the entire test session. If the environment variable is not set, the example will use a test token placeholder.