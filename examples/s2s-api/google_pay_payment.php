<?php
/**
 * This example shows you how to process a Google Pay transaction.
 * You can use the payment detail token returned in this example to run the example in payment_with_saved_token.php
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $googlePayPayment = new \VendoSdk\S2S\Request\Payment();
    $googlePayPayment->setApiSecret(getenv('VENDO_SECRET_API', true) ?: '23e13e591a99d4394e76bd6848236a892e961fbc78151212654b90db678a9374');
    $googlePayPayment->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $googlePayPayment->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $googlePayPayment->setAmount(10.50);
    $googlePayPayment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
    $googlePayPayment->setIsTest(true);

    //You must set the flag below to TRUE if you're processing a recurring billing transaction
    $googlePayPayment->setIsMerchantInitiatedTransaction(false);

    //You may add non_recurring flag to mark no merchant initiated transactions (rebills) will follow, required by some banks
    $googlePayPayment->setIsNonRecurring(true);

    //Set this flag to true when you do not want to capture the transaction amount immediately, but only validate the
    // payment details and block (reserve) the amount. The capture of a preauth-only transaction can be performed with
    // the CapturePayment class.
    $googlePayPayment->setPreAuthOnly(false);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $googlePayPayment->setExternalReferences($externalRef);

    /**
     * Add items to your request, you can add one or more
     */
    $cartItem = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem->setId(123);//set your product id
    $cartItem->setDescription('Registration fee');//your product description
    $cartItem->setPrice(4.00);
    $cartItem->setQuantity(1);
    $googlePayPayment->addItem($cartItem);

    $cartItem2 = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem2->setId(123);//set your product id
    $cartItem2->setDescription('Unlimited video download');//your product description
    $cartItem2->setPrice(6.50);
    $cartItem2->setQuantity(1);
    $googlePayPayment->addItem($cartItem2);

    /**
     * Provide the Google Pay token that you collected from the user
     * The token should be a JSON stringified Google Pay token as provided by Google Pay API
     * You can set it via environment variable: export VENDO_GOOGLE_PAY_TOKEN='{"signature":"...","protocolVersion":"ECv2","signedMessage":"..."}'
     */
    $googlePayDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\GooglePay();
    //set your actual test Google token. ENV variable - for testing
    $googlePayDetails->setToken(getenv('VENDO_GOOGLE_PAY_TOKEN', true));
    $googlePayPayment->setPaymentDetails($googlePayDetails);

    /**
     * Customer details
     */
    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doe');
    $customer->setEmail('john.doe.test@thisisatest.test');
    $customer->setLanguageCode('en');
    $customer->setCountryCode('US');
    $googlePayPayment->setCustomerDetails($customer);

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
    $googlePayPayment->setShippingAddress($shippingAddress);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress('127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent('a user agent');
    $googlePayPayment->setRequestDetails($request);

    $response = $googlePayPayment->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transactions was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
        echo "\nThe credit card payment Auth Code is: " . $response->getCreditCardPaymentResult()->getAuthCode();
        echo "\nThe Payment Details Token is: ". $response->getPaymentToken();
        echo "\nYou must save the payment details token if you need or want to process future recurring billing or one-clicks\n";
        echo "\nThis is your transaction reference (the one you set it in the request): " . $response->getExternalReferences()->getTransactionReference();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified";
        echo "\nYou MUST :";
        echo "\n   1. Save the verificationId: " . $response->getResultDetails()->getVerificationId();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
        echo "\nthe user will verify his payment details, then he will be redirected to the Success URL that's configured in your account at Vendo's back office.";
        echo "\nwhen the user comes back you need to post the request to vendo again, please call google_pay_verification example.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}
