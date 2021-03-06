<?php
/**
 * This example shows you how to change a subscription schedule
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $cancelSubscription = new \VendoSdk\Gateway\CancelSubscription();
    $cancelSubscription->setApiSecret('your_secret_api_secret');

    $cancelSubscription->setIsTest(true);
    $cancelSubscription->setMerchantId(1);//Your Vendo Merchant ID
    $cancelSubscription->setSubscriptionId(160042564);//The Vendo Subscription ID that you want to cancel.
    $cancelSubscription->setReasonId(26);

    $response = $cancelSubscription->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_OK) {
        echo "The subscription was successfully cancelled. The Vendo Subscription ID is: " . $response->getSubscriptionDetails()->getId();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_NOT_OK) {
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


