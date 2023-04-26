<?php
/**
 * This example shows you how to cancel a subscription
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $sharedSecret = getenv('VENDO_SHARED_SECRET', true)?:'Your_Vendo_Shared_Secret__get_it_from_us';

    $refund = new \VendoSdk\Subscription\RefundSubscription($sharedSecret);
    $refund->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $refund->setTransactionId(72700697);//The Vendo Subscription ID that you want to cancel.
    $refund->setRefundReasonId(26);//reason 26: "test transaction"

    /** You can also process a partial refund using the method below */
    $refund->setPartialAmount(1.23);
    $refund->setActionType(\VendoSdk\Subscription\RefundSubscription::ACTION_REFUND_ONLY);

    $response = $refund->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getResponseCode() == \VendoSdk\Vendo::SUBSCRIPTION_REFUND_CODE_OK) {
        echo "The transaction " . $response->getTransactionId() . " was successfully refunded. ";
    } else {
        echo "The transaction " . $response->getTransactionId() . " was not refunded.";
        echo "\nmerchant ID: " . $response->getMerchantId();
        echo "\naction Type: " . $response->getActionType();
        echo "\nError message: " . $response->getResponseMessage();
        echo "\nError code: " . $response->getResponseCode();
    }
    echo "\n\n\n";

} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

