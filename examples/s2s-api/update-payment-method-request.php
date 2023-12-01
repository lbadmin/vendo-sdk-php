<?php
/**
 * This example shows you how to change payment details of the subscription
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $changeSubscription = new \VendoSdk\S2S\Request\UpdatePaymentMethod();
    $changeSubscription->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');

    $changeSubscription->setIsTest(true);
    $changeSubscription->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $changeSubscription->setSubscriptionId(160116532);//The Vendo Subscription ID that you want to change.
    /** Set new payment details */

    $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
    $ccDetails->setNameOnCard('John Doe');
    $ccDetails->setCardNumber('4000012892688323');//this is a test card number, it will only work for test transactions
    $ccDetails->setExpirationMonth('05');
    $ccDetails->setExpirationYear('2029');
    $ccDetails->setCvv(123);//do not store nor log the CVV
    $changeSubscription->setPaymentDetails($ccDetails);

    /**
     * User request details, mandatory for payment details change
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
    $changeSubscription->setRequestDetails($request);

    $response = $changeSubscription->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The subscription payment details were successfully updated. The Vendo Subscription ID is: " . $response->getSubscriptionDetails()->getId();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The change must be verified";
        echo "\nYou MUST :";
        echo "\n   1. Save the verification_id: " . $response->getVerificationId();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getVerificationUrl();
        echo "\nthe user will verify his payment details, then he will be redirected to the Success URL that's configured in your account at Vendo's back office.";
        echo "\nwhen the user comes back you need to post the verification request to vendo, for that you will need saved verification_id.";
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


