<?php
/**
 * This example shows you how to change payment details of the subscription
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $changeSubscription = new \VendoSdk\S2S\Request\UpdatePaymentMethod();
    $changeSubscription->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');

    $changeSubscription->setIsTest(true);
    $changeSubscription->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $changeSubscription->setSubscriptionId(160116532);//The Vendo Subscription ID that you want to change.
    /** Set new payment details */

    $verificationDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Verification();
    $verificationDetails->setVerificationId(4602);//use verification_id returned in change-subscription-payment-details-request
    $changeSubscription->setPaymentDetails($verificationDetails);

    /**
     * User request details, mandatory for payment details change
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $changeSubscription->setRequestDetails($request);

    $response = $changeSubscription->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The subscription payment details were successfully updated. The Vendo Subscription ID is: " . $response->getSubscriptionDetails()->getId();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The operation failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}


