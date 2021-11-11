<?php
/**
 * This example shows you how to cancel a subscription
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $sharedSecret = 'Your_Vendo_Shared_Secret__get_it_from_us';

    $cancel = new \VendoSdk\Subscription\CancelSubscription($sharedSecret);
    $cancel->setMerchantId(1);//Your Vendo Merchant ID
    $cancel->setSubscriptionId(72537045);//The Vendo Subscription ID that you want to cancel.
    /** You can also process a partial refund using the method below */

    $response = $cancel->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getResponseCode() == \VendoSdk\Vendo::SUBSCRIPTION_CANCEL_RESPONSE_CODE_OK) {
        echo "The subscription " . $response->getSubscriptionId() . " was successfully cancelled. The Vendo Transaction ID is: ";
    } else {
        echo "The subscription " . $response->getSubscriptionId() . " was not cancelled.";
        echo "\nmerchant ID: " . $response->getMerchantId();
        echo "\nError message: " . $response->getResponseMesage();
        echo "\nError code: " . $response->getResponseCode();
    }
    echo "\n\n\n";

} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

