<?php
/**
 * This example shows you how to change a subscription schedule
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $changeSubscription = new \VendoSdk\S2S\Request\ChangeSubscription();
    $changeSubscription->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');

    $changeSubscription->setIsTest(true);
    $changeSubscription->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $changeSubscription->setSubscriptionId(160116338);//The Vendo Subscription ID that you want to change.
    /** Set new schedule */

    $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
    $schedule->setNextRebillDate('2025-10-11');
    $schedule->setRebillDuration(12);//days
    $schedule->setRebillAmount(10.34);//billing currency
    $changeSubscription->setSubscriptionSchedule($schedule);

    $response = $changeSubscription->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The subscription schedule was successfully updated. The Vendo Subscription ID is: " . $response->getSubscriptionDetails()->getId();
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


