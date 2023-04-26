<?php
/**
 * This example shows you how to use a payment detail token that was saved in a previous transaction.
 * You can get a test payment detail token by running the example in credit_card_payment.php
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $tokenPayment = new \VendoSdk\S2S\Request\Payment();
    $tokenPayment->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');
    $tokenPayment->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $tokenPayment->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id' ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $tokenPayment->setAmount(10.50);
    $tokenPayment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
    $tokenPayment->setIsTest(true);

    //You must set the flag below to TRUE if you're processing a recurring billing transaction or if you initiated this
    //payment on behalf of your user.
    $tokenPayment->setIsMerchantInitiatedTransaction(false);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_555');
    $tokenPayment->setExternalReferences($externalRef);

    /**
     * Add items to your request, you can add one or more
     */
    $cartItem = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem->setId(123);//set your product id
    $cartItem->setDescription('Registration fee');//your product description
    $cartItem->setPrice(4.00);
    $cartItem->setQuantity(1);
    $tokenPayment->addItem($cartItem);

    $cartItem2 = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem2->setId(123);//set your product id
    $cartItem2->setDescription('Unlimited video download');//your product description
    $cartItem2->setPrice(6.50);
    $cartItem2->setQuantity(1);
    $tokenPayment->addItem($cartItem2);

    /**
     * Provide the token of the payment details that were used by this user for this site
     */
    $token = new \VendoSdk\S2S\Request\Details\PaymentMethod\Token();
    $token->setToken('cfc715414b5231e1302ef559be109d38');//this is a dummy example, get it from your database or use a token from a previous test
    $tokenPayment->setPaymentDetails($token);

    /**
     * Shipping details. This is required.
     */
    $shippingAddress = new \VendoSdk\S2S\Request\Details\ShippingAddress();
    $shippingAddress->setFirstName('John');
    $shippingAddress->setLastName('Doe');
    $shippingAddress->setCountryCode('US');
    //If you're selling digital content then you are allowed to use dummy details like the ones below
    $shippingAddress->setAddress('123 Example Street');
    $shippingAddress->setCity('Miami');
    $shippingAddress->setState('FL');
    $shippingAddress->setPostalCode('33000');
    $shippingAddress->setPhone('1000000000');
    $tokenPayment->setShippingAddress($shippingAddress);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $tokenPayment->setRequestDetails($request);

    $response = $tokenPayment->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
        if (!empty($response->getCreditCardPaymentResult())) {
            echo "\nThe credit card payment Auth Code is: " . $response->getCreditCardPaymentResult()->getAuthCode();
        }

        if (!empty($response->getPaymentToken())) {
            echo "\nThe Payment Details Token is: " . $response->getPaymentToken();
            echo "\nYou must save the payment details token if you need or want to process future recurring billing or one-clicks\n";
        }

        echo "\nThis is your transaction reference (the one you set it in the request): " . $response->getExternalReferences()->getTransactionReference();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified";
        echo "\nYou MUST :";
        echo "\n   1. Save the payment token :" . $response->getPaymentToken();
        echo "\n   2. Redirect the user to the verification URL:" . $response->getResultDetails()->getVerificationUrl();
        echo "\nthe user will verify his payment details, then he will be redirected to the Success URL that's configured in your account at Vendo's back office.";
        echo "\nwhen the user comes back you need to post the request to vendo again, you can use the TokenPayment class again.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

