<?php
/**
 * This example shows you how to process a Direct Debit (SEPA) transaction.
 * You can use the payment detail token returned in this example to run the example in payment_with_saved_token.php
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $sepaPayment = new \VendoSdk\S2S\Request\Payment();
    $sepaPayment->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');
    $sepaPayment->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $sepaPayment->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $sepaPayment->setAmount(120.00);
    $sepaPayment->setCurrency(\VendoSdk\Vendo::CURRENCY_EUR);
    $sepaPayment->setIsTest(true);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $sepaPayment->setExternalReferences($externalRef);

    /**
     * Add items to your request, you can add one or more
     */
    $paymentDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Verification();
    $paymentDetails->setVerificationId(240662060);
    $sepaPayment->setPaymentDetails($paymentDetails);

    //repeat the subscription schedule
    $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
    $schedule->setRebillDuration(30);//days
    $schedule->setRebillAmount(2.34);//billing currency
    $sepaPayment->setSubscriptionSchedule($schedule);

    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doe');
    $customer->setEmail('qa+sepa+test@vendoservices.com');
    $customer->setLanguageCode('en');
    $customer->setCountryCode('DE');

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
    $shippingAddress->setAddress('123 Example Strasse');
    $shippingAddress->setCity('Berlin');
    $shippingAddress->setState('FL');
    $shippingAddress->setPostalCode('33000');
    $shippingAddress->setPhone('1000000000');
    $sepaPayment->setShippingAddress($shippingAddress);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $sepaPayment->setRequestDetails($request);

    $response = $sepaPayment->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
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
        echo "\nwhen the user comes back you need to post the request to vendo again, please call credit_card_3ds_verifiction example.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}
