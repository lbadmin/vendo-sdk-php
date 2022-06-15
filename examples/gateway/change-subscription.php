<?php
/**
 * This example shows you how to change a subscription schedule
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $cancel = new \VendoSdk\Gateway\ChangeSubscription();

//@todo remove/uncomment before merge
//    $cancel->setApiSecret('your_secret_api_secret');
$cancel->setApiSecret('23e13e591a99d4394e76bd6848236a892e961fbc78151212654b90db678a9374');

    $cancel->setIsTest(true);
    $cancel->setMerchantId(1);//Your Vendo Merchant ID
    $cancel->setSubscriptionId(160042557);//The Vendo Transaction ID that you want to refund.
    /** You can also process a partial refund using the method below */
    //$cancel->setPartialAmount(5);//this amount must be less than the amount of the transaction that you need to refund.

    $response = $cancel->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_OK) {
        echo "The transactions was successfully cancelled. The Vendo Transaction ID is: " . $response->getTransactionDetails()->getId();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

