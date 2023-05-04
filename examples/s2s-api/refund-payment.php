<?php
/**
 * This example shows you how to refund a payment
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $refund = new \VendoSdk\S2S\Request\Refund();
    $refund->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');
    $refund->setIsTest(true);
    $refund->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $refund->setTransactionId(770110209);//The Vendo Transaction ID that you want to refund.
    /** You can also process a partial refund using the method below */
    //$refund->setPartialAmount(5);//this amount must be less than the amount of the transaction that you need to refund.

    $response = $refund->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully refunded. The Vendo Transaction ID is: " . $response->getTransactionDetails()->getId();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
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

