<?php
/**
 * This example shows you how to run a payment method registration with verification.
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $registerPaymentMethod = new \VendoSdk\S2S\Request\RegisterPaymentMethod();
    $registerPaymentMethod->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');
    $registerPaymentMethod->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $registerPaymentMethod->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id' ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $registerPaymentMethod->setCurrency(\VendoSdk\Vendo::CURRENCY_EUR);
    $registerPaymentMethod->setIsTest(true);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_555');
    $registerPaymentMethod->setExternalReferences($externalRef);

    /**
     * Provide the credit card details that you collected from the user
     */
    $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
    $ccDetails->setNameOnCard('John Doelek');
    $ccDetails->setCardNumber('4000012892688323');//this is a test card number, it will only work for test transactions
    $ccDetails->setExpirationMonth('05');
    $ccDetails->setExpirationYear('2029');
    $ccDetails->setCvv(123);//do not store nor log the CVV
    $registerPaymentMethod->setPaymentDetails($ccDetails);
    /**
     * Customer details
     */
    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doelek');
    $customer->setEmail('qa+3d+test3@vendoservices.com');
    $customer->setLanguageCode('en');
    $customer->setCountryCode('DE');
    $registerPaymentMethod->setCustomerDetails($customer);


    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $registerPaymentMethod->setRequestDetails($request);

    $response = $registerPaymentMethod->postRequest();

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
        echo "\n   1. Save the verification ID: " . $response->getResultDetails()->getVerificationId();
        echo "\n   2. Save the payment token: " . $response->getPaymentToken();
        echo "\n   3. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
        echo "\nthe user will verify his payment details, then he will be redirected to the Success URL that's configured in your account at Vendo's back office.";
        echo "\nwhen the user comes back you need to post the request to vendo again, you can use the TokenPayment class.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

