<?php
/**
 * This example shows you how to process a OXXO transaction.
 * You need to redirect user to the url returned by API to let her/him finish the operation
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $payment = new \VendoSdk\S2S\Request\Payment();
    $payment->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');
    $payment->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $payment->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id' ?: 'Your_vendo_site_id');//Your Vendo Site ID


    $payment->setAmount(624.95);
    $payment->setCurrency(\VendoSdk\Vendo::CURRENCY_MXN);
    $payment->setIsTest(true);

    $payment->setIsMerchantInitiatedTransaction(false);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $payment->setExternalReferences($externalRef);

    /**
     * Add items to your request, you can add one or more
     */
    $cartItem = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem->setId(123);//set your product id
    $cartItem->setDescription('Registration fee');//your product description
    $cartItem->setPrice(4.00);
    $cartItem->setQuantity(1);
    $payment->addItem($cartItem);

    $cartItem2 = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem2->setId(123);//set your product id
    $cartItem2->setDescription('Unlimited video download');//your product description
    $cartItem2->setPrice(6.50);
    $cartItem2->setQuantity(1);
    $payment->addItem($cartItem2);

    /**
     * Customer details
     */
    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('Paid');
    $customer->setLastName('Oxxo');
    $customer->setEmail('qa+oxxo+'.rand(100000,999999).'@vendoservices.com');

    $paymentDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Verification();
    $paymentDetails->setVerificationId(240584178);
    $payment->setPaymentDetails($paymentDetails);

    $customer->setLanguageCode('es');
    /** OXXO payments are supported for Mexico only */
    $customer->setCountryCode('MX');

    /**
     * Shipping details. This is required.
     */
    $shippingAddress = new \VendoSdk\S2S\Request\Details\ShippingAddress();
    $shippingAddress->setFirstName($customer->getFirstName());
    $shippingAddress->setLastName($customer->getLastName());
    $shippingAddress->setAddress($customer->getAddress());
    $shippingAddress->setCountryCode($customer->getCountryCode());
    $shippingAddress->setCity($customer->getCity());
    $shippingAddress->setState($customer->getState());
    $shippingAddress->setPostalCode($customer->getPostalCode());
    $shippingAddress->setPhone($customer->getPhone());
    //If you're selling digital content then you are allowed to use dummy details like the ones below
    $shippingAddress->setAddress('123 Example Street');
    $shippingAddress->setCity('Miami');
    $shippingAddress->setState('FL');
    $shippingAddress->setPostalCode('33000');
    $shippingAddress->setPhone('1000000000');
    $payment->setShippingAddress($shippingAddress);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $payment->setRequestDetails($request);

    $response = $payment->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The payment is complete";
        echo "\nTransaction id: " . $response->getTransactionDetails()->getId();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } else {
        echo 'Something went wrong, invalid response: ' . $response->getStatus();
    }
    echo "\n\n\n";
} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}
