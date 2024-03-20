<?php
/**
 * This example shows you how to finish a pay by bank process.
 * You must run pay_by_bank_step1.php first.
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $paymentVerificationObject = new \VendoSdk\S2S\Request\PaymentVerification();
    $paymentVerificationObject->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');

    $paymentVerificationObject->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $paymentVerificationObject->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $paymentVerificationObject->setAmount(10.00);
    $paymentVerificationObject->setCurrency(\VendoSdk\Vendo::CURRENCY_EUR);
    $paymentVerificationObject->setIsTest(true);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $paymentVerificationObject->setExternalReferences($externalRef);
    /**
     * Shipping details. This is required. You can use dummy details if you sell digital content.
     */
    $shippingAddress = new \VendoSdk\S2S\Request\Details\ShippingAddress();
    $shippingAddress->setFirstName('John');
    $shippingAddress->setLastName('Doe');
    $shippingAddress->setCountryCode('ES');
    $shippingAddress->setAddress('An Example 999');
    $shippingAddress->setCity('Barcelona');
    $shippingAddress->setState('BC');
    $shippingAddress->setPostalCode('080088');
    $shippingAddress->setPhone('1000000000');
    $paymentVerificationObject->setShippingAddress($shippingAddress);

    /**
     * Provide the verification_id that you got when you ran pay_by_bank_step1.php
     */
    $verification = new \VendoSdk\S2S\Request\Details\PaymentMethod\Verification();
    $verification->setVerificationId(240513138); //verificationId from pay_by_bank_step1.php
    $paymentVerificationObject->setPaymentDetails($verification);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $paymentVerificationObject->setRequestDetails($request);

    $response = $paymentVerificationObject->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
        echo "\nThis is your transaction reference (the one you set it in the request): " . $response->getExternalReferences()->getTransactionReference();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified again";
        echo "\nYou MUST :";
        echo "\n   1. Save the verificationId: " . $response->getResultDetails()->getVerificationId();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
        echo "\nThe user will authorize the payment and then he will be redirected to the Success URL";
        echo "\nWhen the user comes back you need to post the request to vendo again, like in example in this file.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

