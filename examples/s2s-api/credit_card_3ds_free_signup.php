<?php
/**
 * This example shows you how to process a credit card transaction.
 * You can use the payment detail token returned in this example to run the example in payment_with_saved_token.php
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $creditCardFreSignup3d = new \VendoSdk\S2S\Request\Payment();
    $creditCardFreSignup3d->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');

    $creditCardFreSignup3d->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $creditCardFreSignup3d->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $creditCardFreSignup3d->setAmount(0.00);
    $creditCardFreSignup3d->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
    $creditCardFreSignup3d->setIsTest(true);

    //You must set the flag below to TRUE if you're processing a recurring billing transaction
    $creditCardFreSignup3d->setIsMerchantInitiatedTransaction(false);

    //Set this flag to true when you do not want to capture the transaction amount immediately, but only validate the
    // payment details and block (reserve) the amount. The capture of a preauth-only transaction can be performed with
    // the CapturePayment class.
    $creditCardFreSignup3d->setPreAuthOnly(false);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $creditCardFreSignup3d->setExternalReferences($externalRef);

    /**
     * Provide the credit card details that you collected from the user
     */
    $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
    $ccDetails->setNameOnCard('John Doe');
    $ccDetails->setCardNumber('4000012892688323');//this is a test card number, it will only work for test transactions
    $ccDetails->setExpirationMonth('05');
    $ccDetails->setExpirationYear('2029');
    $ccDetails->setCvv(123);//do not store nor log the CVV
    $creditCardFreSignup3d->setPaymentDetails($ccDetails);

    /**
     * Customer details
     */
    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doe');
    $customer->setEmail('qa+opp3d+test34@vendoservices.com');
    $customer->setLanguageCode('en');
    $customer->setCountryCode('US');
    $creditCardFreSignup3d->setCustomerDetails($customer);

    /**
     * Shipping details. This is required.
     */
    $shippingAddress = new \VendoSdk\S2S\Request\Details\ShippingAddress();
    $shippingAddress->setFirstName($customer->getFirstName());
    $shippingAddress->setLastName($customer->getLastName());
    $shippingAddress->setAddress($customer->getAddress());
    $shippingAddress->setCountryCode($customer->getCountryCode());
    $shippingAddress->setCity($customer->getCity());
    $shippingAddress->setState($customer->getState());
    $shippingAddress->setPostalCode($customer->getPostalCode());
    $shippingAddress->setPhone($customer->getPhone());
    //If you're selling digital content then you are allowed to use dummy details like the ones below
    $shippingAddress->setAddress('123 Example Street');
    $shippingAddress->setCity('Miami');
    $shippingAddress->setState('FL');
    $shippingAddress->setPostalCode('33000');
    $shippingAddress->setPhone('1000000000');
    $creditCardFreSignup3d->setShippingAddress($shippingAddress);

    //subscription schedule
    $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
    $schedule->setRebillDuration(30);//days
    $schedule->setRebillAmount(10.00);//billing currency
    $schedule->setNextRebillDate('2026-12-01');//trial time for 0.00, set date in the feature
    $creditCardFreSignup3d->setSubscriptionSchedule($schedule);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $creditCardFreSignup3d->setRequestDetails($request);

    $response = $creditCardFreSignup3d->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
        echo "\nThe credit card payment Auth Code is: " . $response->getCreditCardPaymentResult()->getAuthCode();
        echo "\nThe Payment Details Token is: ". $response->getPaymentToken();
        echo "\nYou must save the payment details token if you need or want to process future recurring billing or one-clicks\n";
        echo "\nThis is your transaction reference (the one you set it in the request): " . $response->getExternalReferences()->getTransactionReference();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified";
        echo "\nYou MUST :";
        echo "\n   1. Save the verificationId: " . $response->getResultDetails()->getVerificationId();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
        echo "\nthe user will verify his payment details, then he will be redirected to the Success URL that's configured in your account at Vendo's back office.";
        echo "\nwhen the user comes back you need to post the request to vendo again, like in example credit_card_3ds_free_signup.php.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}
