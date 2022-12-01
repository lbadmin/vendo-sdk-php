<?php

namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\Vendo;

class CreditCardSignupTest extends \PHPUnit\Framework\TestCase
{
    public function testCreditCardSignupSuccess()
    {
        $creditCardSignup = new \VendoSdk\S2S\Request\Payment();
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
        $creditCardSignup->setPreAuthOnly(false);

        $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
        $externalRef->setTransactionReference('your_tx_reference_123');
        $creditCardSignup->setExternalReferences($externalRef);

        /**
         * Add items to your request, you can add one or more
         */
        $cartItem = new \VendoSdk\S2S\Request\Details\Item();
        $cartItem->setId(123);//set your product id
        $cartItem->setDescription('Registration fee');//your product description
        $cartItem->setPrice(4.00);
        $cartItem->setQuantity(1);
        $creditCardSignup->addItem($cartItem);

        $cartItem2 = new \VendoSdk\S2S\Request\Details\Item();
        $cartItem2->setId(123);//set your product id
        $cartItem2->setDescription('Unlimited video download');//your product description
        $cartItem2->setPrice(6.50);
        $cartItem2->setQuantity(1);
        $creditCardSignup->addItem($cartItem2);

        /**
         * Provide the credit card details that you collected from the user
         */
        $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
        $ccDetails->setNameOnCard('John Doe');
        $ccDetails->setCardNumber('4111111111111111');//this is a test card number, it will only work for test transactions
        $ccDetails->setExpirationMonth('05');
        $ccDetails->setExpirationYear('2029');
        $ccDetails->setCvv(123);//do not store nor log the CVV
        $creditCardSignup->setPaymentDetails($ccDetails);

        /**
         * Customer details
         */
        $customer = new \VendoSdk\S2S\Request\Details\Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setEmail('john.doe.test@thisisatest.test');
        $customer->setLanguageCode('en');
        $customer->setCountryCode('US');
        $creditCardSignup->setCustomerDetails($customer);

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
        $creditCardSignup->setShippingAddress($shippingAddress);

        //subscription schedule
        $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
        $schedule->setRebillDuration(30);//days
        $schedule->setRebillAmount(2.34);//billing currency
        $creditCardSignup->setSubscriptionSchedule($schedule);

        $requestDetails = $this->createMock(ClientRequest::class);
        $creditCardSignup->setRequestDetails($requestDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_OK,
            'external_references' => [
                'transaction_reference' => "your_tx_reference_123",
            ],
            'transaction' => [
                'id' => 240168518,
                'amount' => '10.50',
                'currency' => 'USD',
                'datetime' => '2022-06-15T15:31:14+01:00',
            ],
            'card_details' => [
                'auth_code' => '8ac7a49f81643f3b018167c68a444a68',
            ],
            'payment_details_token' => '67616ff84d046c83708254cac19acb67',
            'request_id' => 'yij_234',
        ]));
        $httpClient->method('send')->willReturn($response);

        $creditCardSignup->setHttpClient($httpClient);
        $creditCardSignup->postRequest();

        $this->assertEquals(true, $creditCardSignup->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/payment', $creditCardSignup->getApiEndpoint());
        $this->assertEquals('your_secret_api_secret', $creditCardSignup->getApiSecret());
        $this->assertEquals(1, $creditCardSignup->getMerchantId());
        $this->assertEquals('{"status":1,"external_references":{"transaction_reference":"your_tx_reference_123"},"transaction":{"id":240168518,"amount":"10.50","currency":"USD","datetime":"2022-06-15T15:31:14+01:00"},"card_details":{"auth_code":"8ac7a49f81643f3b018167c68a444a68"},"payment_details_token":"67616ff84d046c83708254cac19acb67","request_id":"yij_234"}', $creditCardSignup->getRawResponse());
    }

    public function testCreditCardSignupError()
    {
        $creditCardSignup = new \VendoSdk\S2S\Request\Payment();
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
        $creditCardSignup->setPreAuthOnly(false);

        $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
        $externalRef->setTransactionReference('your_tx_reference_123');
        $creditCardSignup->setExternalReferences($externalRef);

        /**
         * Add items to your request, you can add one or more
         */
        $cartItem = new \VendoSdk\S2S\Request\Details\Item();
        $cartItem->setId(123);//set your product id
        $cartItem->setDescription('Registration fee');//your product description
        $cartItem->setPrice(4.00);
        $cartItem->setQuantity(1);
        $creditCardSignup->addItem($cartItem);

        $cartItem2 = new \VendoSdk\S2S\Request\Details\Item();
        $cartItem2->setId(123);//set your product id
        $cartItem2->setDescription('Unlimited video download');//your product description
        $cartItem2->setPrice(6.50);
        $cartItem2->setQuantity(1);
        $creditCardSignup->addItem($cartItem2);

        /**
         * Provide the credit card details that you collected from the user
         */
        $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
        $ccDetails->setNameOnCard('John Doe');
        $ccDetails->setCardNumber('4111111111111111');//this is a test card number, it will only work for test transactions
        $ccDetails->setExpirationMonth('05');
        $ccDetails->setExpirationYear('2029');
        $ccDetails->setCvv(123);//do not store nor log the CVV
        $creditCardSignup->setPaymentDetails($ccDetails);

        /**
         * Customer details
         */
        $customer = new \VendoSdk\S2S\Request\Details\Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setEmail('john.doe.test@thisisatest.test');
        $customer->setLanguageCode('en');
        $customer->setCountryCode('US');
        $creditCardSignup->setCustomerDetails($customer);

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
        $creditCardSignup->setShippingAddress($shippingAddress);

        //subscription schedule
        $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
        $schedule->setRebillDuration(30);//days
        $schedule->setRebillAmount(2.34);//billing currency
        $creditCardSignup->setSubscriptionSchedule($schedule);

        $requestDetails = $this->createMock(ClientRequest::class);
        $creditCardSignup->setRequestDetails($requestDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_NOT_OK,
            'error' => [
                'code' => '8105',
                'message' => 'Invalid currency value',
            ],
            'request_id' => 'yij_234',
        ]));
        $httpClient->method('send')->willReturn($response);

        $creditCardSignup->setHttpClient($httpClient);
        $creditCardSignup->postRequest();

        $this->assertEquals(true, $creditCardSignup->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/payment', $creditCardSignup->getApiEndpoint());
        $this->assertEquals('your_secret_api_secret', $creditCardSignup->getApiSecret());
        $this->assertEquals(1, $creditCardSignup->getMerchantId());
        $this->assertEquals('{"status":0,"error":{"code":"8105","message":"Invalid currency value"},"request_id":"yij_234"}', $creditCardSignup->getRawResponse());
    }
}
