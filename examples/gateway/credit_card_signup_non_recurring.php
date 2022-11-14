<?php
/**
 * This example shows you how to process a credit card signup if no recurring fees are planned.
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $creditCardSignup = new \VendoSdk\Gateway\Payment();
    $creditCardSignup->setApiSecret('your_secret_api_secret');

    $creditCardSignup->setMerchantId(1);//Your Vendo Merchant ID
    $creditCardSignup->setSiteId(1);//Your Vendo Site ID
    $creditCardSignup->setAmount(10.50);
    $creditCardSignup->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
    $creditCardSignup->setIsTest(true);

    //You must set the flag below to TRUE if you're processing a recurring billing transaction
    $creditCardSignup->setIsMerchantInitiatedTransaction(false);

    //Set this flag to true when you do not want to capture the transaction amount immediately, but only validate the
    // payment details and block (reserve) the amount. The capture of a preauth-only transaction can be performed with
    // the CapturePayment class.
    $creditCardSignup->setIsPreAuth(false);

    $externalRef = new \VendoSdk\Gateway\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_123');
    $creditCardSignup->setExternalReferences($externalRef);

    /**
     * Add items to your request, you can add one or more
     */
    $cartItem = new \VendoSdk\Gateway\Request\Details\Item();
    $cartItem->setId(123);//set your product id
    $cartItem->setDescription('Registration fee');//your product description
    $cartItem->setPrice(4.00);
    $cartItem->setQuantity(1);
    $creditCardSignup->addItem($cartItem);

    $cartItem2 = new \VendoSdk\Gateway\Request\Details\Item();
    $cartItem2->setId(123);//set your product id
    $cartItem2->setDescription('Unlimited video download');//your product description
    $cartItem2->setPrice(6.50);
    $cartItem2->setQuantity(1);
    $creditCardSignup->addItem($cartItem2);

    /**
     * Provide the credit card details that you collected from the user
     */
    $ccDetails = new \VendoSdk\Gateway\Request\Details\CreditCard();
    $ccDetails->setNameOnCard('John Doe');
    $ccDetails->setCardNumber('4111111111111111');//this is a test card number, it will only work for test transactions
    $ccDetails->setExpirationMonth('05');
    $ccDetails->setExpirationYear('2029');
    $ccDetails->setCvv(123);//do not store nor log the CVV
    $creditCardSignup->setPaymentDetails($ccDetails);

    /**
     * Customer details
     */
    $customer = new \VendoSdk\Gateway\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doe');
    $customer->setEmail('john.doe.test@thisisatest.test');
    $customer->setLanguageCode('en');
    $customer->setCountryCode('US');
    $creditCardSignup->setCustomerDetails($customer);

    /**
     * Shipping details. This is required.
     */
    $shippingAddress = new \VendoSdk\Gateway\Request\Details\ShippingAddress();
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
    $creditCardSignup->setShippingAddress($shippingAddress);

    //subscription schedule
    //you need to send "non_recurring" flag set to true OR subscription_schedule, but not both
    $creditCardSignup->setIsNonRecurring(true);

    /**
     * User request details
     */
    $request = new \VendoSdk\Gateway\Request\Details\Request();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');//you must pass a valid IPv4 address
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
    $creditCardSignup->setRequestDetails($request);

    $response = $creditCardSignup->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_OK) {
        echo "The transactions was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
        echo "\nThe credit card payment Auth Code is: " . $response->getCreditCardPaymentResult()->getAuthCode();
        echo "\nThe Payment Details Token is: ". $response->getPaymentToken();
        echo "\nYou must save the payment details token if you need or want to process future recurring billing or one-clicks\n";
        echo "\nThis is your transaction reference (the one you set it in the request): " . $response->getExternalReferences()->getTransactionReference();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::GATEWAY_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified";
        echo "\nYou MUST :";
        echo "\n   1. Save the payment token: " . $response->getPaymentToken();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
        echo "\nthe user will verify his payment details, then he will be redirected to the Success URL that's configured in your account at Vendo's back office.";
        echo "\nwhen the user comes back you need to post the request to vendo again, you can use the TokenPayment class.";
    }
    echo "\n\n\n";


} catch (\VendoSdk\Exception $exception) {
    die ('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die ('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}

