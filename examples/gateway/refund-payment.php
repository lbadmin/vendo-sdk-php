<?php
/**
 * This example shows you how to refund a payment
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $refund = new \VendoSdk\Gateway\RefundPayment();
    $refund->setSharedSecret('Your shared secret');//this is not the API secret. Ask Vendo about your Shared Secret.
    $refund->setMerchantId(1);//Your Vendo Merchant ID
    $refund->setTransactionId(70500011);//The Vendo Transaction ID that you want to refund.
    /** You can also process a partial refund using the method below */
    //$refund->setPartialAmount(5);//this amount must be less than the amount of the transaction that you need to refund.


    echo "Were about to post this request: \n" . $refund->getRawRequest() . "\n";

    $response = $refund->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_OK) {
        echo "The transactions was successfully refunded. The Vendo Transaction ID is: " . $response->getTransaction()->getId();
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

