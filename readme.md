# Vendo Services SDK for PHP
This SDK helps developers to [integrate with Vendo Services platform](https://docs.vendoservices.com/).

Install using ```composer require vendoservices/vendo-sdk```

See on [Packagist](https://packagist.org/packages/vendoservices/vendo-sdk)

## API documentation
https://docs.vendoservices.com/reference/misc

Important for 2-step payments with the authorisation step: 3DS, Cryptocurrency, PIX
 
Please follow carefully [2 Step payment docs](https://docs.vendoservices.com/reference/payment-gateway-3ds-flow) and pay attention to close the transaction with the 2nd, final request.

Example flow:

1. Call [Cryptocurrency payment example](https://github.com/lbadmin/vendo-sdk-php/blob/master/examples/s2s-api/crypto_payment.php)

2. Save the token


    $token = $response->getPaymentToken(); 

    // something like '2093c199fc3e20ee45af8ed07af0ddf5'


3. Redirect the user to the verification url you got in the response 

    
    $verificationUrl = $response->getResultDetails()->getVerificationUrl();

    // example: 'https://secure.vend-o.com/v/verification?transaction_id=240359080&systemsignature=moJpFrKRgo5PkP9sqStN6iJC6v8'
      
4. After authorization is completed call [Token example](https://github.com/lbadmin/vendo-sdk-php/blob/master/examples/s2s-api/payment_with_saved_token.php)
   - use token you have saved in step 2.

5. Check the Backoffice 'Sales' -> 'Transactions', you should see 2 successful transactions:
   
   - Verification
   - S2S Payment

## Changelog
### v2.0.11
- Added support for "Pay by Bank" transactions
- A minor fix
### v2.0.10
- Fixed password encryption algorithm
- A minor fix
### v2.0.9
- Added cross-sale support
### v2.0.8
- A minor fix
### v2.0.7
- Support for custom PaymentResponse
### v2.0.6
- Bugfix: added missed parameters in SubscriptionBase::postRequest
### v2.0.5
- New feature: Reactivate API endpoint support
### v2.0.4
- New feature: added sucess_url parameter
### v2.0.3
- Bug fix: added missing subscription_id
### v2.0.2
- New feature: S2S Payment API - item id can now hold any string value
- Dockerized examples
### v2.0.1
- Updated the internal version number
### v2.0.0

- Fixed support for **SEPA** payment method in S2S Payment API

#### Backwards-incompatible changes

- Renamed Gateway to S2S
- Removed deprecated classes
- Reorganized other classes and namespaces

### v1.0.7

- Added support for `non_recurring` parameter in S2S Payment API

### v1.0.6

- Added support for **Cryptocurrency** payment method in S2S Payment API
