<?php
/**
 * This example shows you how to capture a payment that was previously pre-authorized.
 * You can see how to pre-authorize a credit card in pre-authorize_credit-card.php
 */

include __DIR__ . '/../../vendor/autoload.php';


try {
    $capture = new \VendoSdk\S2S\Request\CapturePayment();
    $capture->setApiSecret('your_secret_api_secret');
    $capture->setMerchantId(1);//Your Vendo Merchant ID
    $capture->setIsTest(true);
    $capture->setTransactionId(70110209);//The Vendo Transaction ID that you want to capture.

    $response = $capture->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully captured. The Vendo Transaction ID is: " . $response->getTransactionDetails()->getId();
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

