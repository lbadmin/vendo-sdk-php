<?php
/**
 * This example shows you how to reactivate a subscription
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $sharedSecret = getenv('VENDO_SHARED_SECRET', true)?:'Your_Vendo_Shared_Secret__get_it_from_us';

    $reactivate = new \VendoSdk\Subscription\ReactivateSubscription($sharedSecret);
    $reactivate->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $reactivate->setSubscriptionId(72537045);//The Vendo Subscription ID that you want to reactivate.

    $response = $reactivate->getRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getResponseCode() == \VendoSdk\Vendo::SUBSCRIPTION_REACTIVATE_RESPONSE_CODE_OK) {
        echo "The subscription " . $response->getSubscriptionId() . " was successfully reactivated. The Vendo Transaction ID is: ";
    } else {
        echo "The subscription " . $response->getSubscriptionId() . " was not reactivated.";
        echo "\nmerchant ID: " . $response->getMerchantId();
        echo "\nError message: " . $response->getResponseMessage();
        echo "\nError code: " . $response->getResponseCode();
    }
    echo "\n\n\n";

} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

