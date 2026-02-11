<?php
/**
 * This example shows you how to process a Direct Debit (SEPA) transaction without IBAN/BIC.
 * When IBAN (and BIC) are not set, the payment request will only include payment_method => 'sepa';
 * no iban or bic_swift fields are sent. Use this for flows where the customer provides
 * bank details later (e.g. via redirect/verification) or when your integration uses an ibanless mandate.
 */

include __DIR__ . '/../../vendor/autoload.php';

try {
    $sepaPayment = new \VendoSdk\S2S\Request\Payment();
    $sepaPayment->setApiSecret(getenv('VENDO_SECRET_API', true) ?: 'Your_vendo_secret_api');
    $sepaPayment->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');//Your Vendo Merchant ID
    $sepaPayment->setSiteId(getenv('VENDO_SITE_ID' , true) ?: 'Your_vendo_site_id');//Your Vendo Site ID
    $sepaPayment->setAmount(120.00);
    $sepaPayment->setCurrency(\VendoSdk\Vendo::CURRENCY_EUR);
    $sepaPayment->setIsTest(true);

    //You must set the flag below to TRUE if you're processing a recurring billing transaction
    $sepaPayment->setIsMerchantInitiatedTransaction(false);

    //You may add non_recurring flag to mark no merchant initiated transactions (rebills) will follow, required by some banks
    $sepaPayment->setIsNonRecurring(true);

    $sepaPayment->setPreAuthOnly(false);

    $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
    $externalRef->setTransactionReference('your_tx_reference_ibanless_' . time());
    $sepaPayment->setExternalReferences($externalRef);

    /**
     * Add items to your request
     */
    $cartItem = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem->setId(123);
    $cartItem->setDescription('Registration fee');
    $cartItem->setPrice(4.00);
    $cartItem->setQuantity(1);
    $sepaPayment->addItem($cartItem);

    $cartItem2 = new \VendoSdk\S2S\Request\Details\Item();
    $cartItem2->setId(123);
    $cartItem2->setDescription('Unlimited video download');
    $cartItem2->setPrice(6.50);
    $cartItem2->setQuantity(1);
    $sepaPayment->addItem($cartItem2);

    /**
     * SEPA details without IBAN/BIC – only payment_method 'sepa' is sent in the request.
     * Do not call setIban() or setBicSwift(); the SDK omits these fields when null.
     */
    $sepaDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Sepa();
    $sepaPayment->setPaymentDetails($sepaDetails);

    /**
     * Customer details
     */
    $customer = new \VendoSdk\S2S\Request\Details\Customer();
    $customer->setFirstName('John');
    $customer->setLastName('Doe');
    $customer->setEmail('qa+sepa+ibanless@vendoservices.com');
    $customer->setLanguageCode('en');
    $customer->setCountryCode('DE');
    $sepaPayment->setCustomerDetails($customer);

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
    $shippingAddress->setAddress('123 Example Strasse');
    $shippingAddress->setCity('Berlin');
    $shippingAddress->setState('FL');
    $shippingAddress->setPostalCode('33000');
    $shippingAddress->setPhone('1000000000');
    $sepaPayment->setShippingAddress($shippingAddress);

    /**
     * User request details
     */
    $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
    $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');
    $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
    $sepaPayment->setRequestDetails($request);

    $response = $sepaPayment->postRequest();

    echo "\n\nRESULT BELOW\n";
    if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
        echo "The transaction was successfully processed. Vendo's Transaction ID is: " . $response->getTransactionDetails()->getId();
        echo "\nPayment Details Token: " . $response->getPaymentToken();
        echo "\nTransaction reference: " . $response->getExternalReferences()->getTransactionReference();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
        echo "The transaction failed.";
        echo "\nError message: " . $response->getErrorMessage();
        echo "\nError code: " . $response->getErrorCode();
    } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
        echo "The transaction must be verified";
        echo "\nYou MUST:";
        echo "\n   1. Save the verificationId: " . $response->getResultDetails()->getVerificationId();
        echo "\n   2. Redirect the user to the verification URL: " . $response->getResultDetails()->getVerificationUrl();
    }
    echo "\n\n\n";

} catch (\VendoSdk\Exception $exception) {
    die('An error occurred when processing your API request. Error message: ' . $exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    die('An error occurred when processing the HTTP request. Error message: ' . $e->getMessage());
}
