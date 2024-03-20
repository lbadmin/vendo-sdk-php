<?php
/**
 * This example shows you how to process a "pay by bank" transaction
 * You need to run this then run pay_by_bank_step2.php after verifying and authorizing the payment by visiting the verification_url
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $paymentObject = new \VendoSdk\S2S\Request\Payment();
    $paymentObject->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');

    $paymentObject->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $paymentObject->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $paymentObject->setAmount(10.00);
    $paymentObject->setCurrency(\VendoSdk\Vendo::CURRENCY_EUR);
    $paymentObject->setIsTest(true);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $paymentObject->setExternalReferences($externalRef);

    $paymentMethod = new \VendoSdk\S2S\Request\Details\PaymentMethod\PayByBank();
    $paymentObject->setPaymentDetails($paymentMethod);

    //The URL to which the user must be redirected after a successful verification.
    //If it isn't specified then our platform will fallback to the Success URL configured in Vendo's back office.
    $paymentObject->setSuccessUrl('https://yoursiteexample.com/successful-payment.php?vendoTransactionId={TRANSACTION_ID}&externalReference={REF}');

    /**
     * Customer details
     */
    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doe');
    $customer->setEmail('qa+paybybank+sdktest@vendoservices.com');
    $customer->setLanguageCode('es');
    $customer->setCountryCode('ES');
    /*
    //The following are optional
    $customer->setAddress('Carrer Example 123');
    $customer->setCity('Barcelona');
    $customer->setState('BC');
    $customer->setPostalCode('08221');
    $customer->setPhone('660660660');
    */
    $paymentObject->setCustomerDetails($customer);

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
    $shippingAddress->setAddress('An Example 999');
    $shippingAddress->setCity('Barcelona');
    $shippingAddress->setState('BC');
    $shippingAddress->setPostalCode('080088');
    $shippingAddress->setPhone('1000000000');
    $paymentObject->setShippingAddress($shippingAddress);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $paymentObject->setRequestDetails($request);

    $response = $paymentObject->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        //STATUS OK is not expected in this first API call.

    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();

    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified and authorized by the user.";
        echo "\nYou MUST :";
        echo "\n   1. Save the verificationId: " . $response->getResultDetails()->getVerificationId();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
        echo "\nThe user will authorize the payment and then he will be redirected to the Success URL";
        echo "\nWhen the user comes back you need to post the request to vendo again, like in example pay_by_bank_step2.php.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

