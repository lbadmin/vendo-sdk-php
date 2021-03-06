<?php
/**
 * This example shows you how to change a subscription schedule
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $changeSubscription = new \VendoSdk\Gateway\ChangeSubscription();
    $changeSubscription->setApiSecret('your_secret_api_secret');

    $changeSubscription->setIsTest(true);
    $changeSubscription->setMerchantId(1);//Your Vendo Merchant ID
    $changeSubscription->setSubscriptionId(160042557);//The Vendo Subscription ID that you want to change.
    /** Set new schedule */

    $schedule = new \VendoSdk\Gateway\Request\Details\SubscriptionSchedule();
    $schedule->setNextRebillDate('2025-10-11');
    $schedule->setRebillDuration(12);//days
    $schedule->setRebillAmount(10.34);//billing currency
    $changeSubscription->setSubscriptionSchedule($schedule);

    $response = $changeSubscription->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_OK) {
        echo "The subscription schedule was successfully updated. The Vendo Subscription ID is: " . $response->getSubscriptionDetails()->getId();
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


