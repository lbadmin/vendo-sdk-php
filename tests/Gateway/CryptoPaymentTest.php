<?php

namespace VendoSdkUnit\Gateway;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Gateway\Request\Details\Crypto;
use VendoSdk\Gateway\Request\Details\Request;
use VendoSdk\Vendo;

class CryptoPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testCryptoPaymentSuccess()
    {
        $payment = self::createPartialMock(\VendoSdk\Gateway\Payment::class, ['getHttpRequest']);
        $payment->setApiSecret('your_secret_api_secret');

        $payment->setMerchantId(1);//Your Vendo Merchant ID
        $payment->setSiteId(1);//Your Vendo Site ID
        $payment->setAmount(10.50);
        $payment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
        $payment->setIsTest(true);
        $payment->setIsMerchantInitiatedTransaction(false);

        $externalRef = new \VendoSdk\Gateway\Request\Details\ExternalReferences();
        $externalRef->setTransactionReference('your_tx_reference_123');
        $payment->setExternalReferences($externalRef);

        /**
         * Add items to your request, you can add one or more
         */
        $cartItem = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem->setId(123);//set your product id
        $cartItem->setDescription('Registration fee');//your product description
        $cartItem->setPrice(4.00);
        $cartItem->setQuantity(1);
        $payment->addItem($cartItem);

        $cartItem2 = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem2->setId(123);//set your product id
        $cartItem2->setDescription('Unlimited video download');//your product description
        $cartItem2->setPrice(6.50);
        $cartItem2->setQuantity(1);
        $payment->addItem($cartItem2);

        /**
         * Customer details
         */
        $customer = new \VendoSdk\Gateway\Request\Details\Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setEmail('john.doe.test@thisisatest.test');
        $customer->setLanguageCode('en');
        $customer->setCountryCode('BR');
        $customer->setNationalIdentifier('723.785.048-29');
        $payment->setCustomerDetails($customer);

        $payment->setPaymentDetails(new Crypto());

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
        $payment->setShippingAddress($shippingAddress);

        $requestDetails = $this->createMock(Request::class);
        $payment->setRequestDetails($requestDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_VERIFICATION_REQUIRED,
            'external_references' => [
                'transaction_reference' => "your_tx_reference_123",
            ],
            'transaction' => [
                'id' => 240168518,
                'amount' => '10.50',
                'currency' => 'USD',
                'datetime' => '2022-06-15T15:31:14+01:00',
            ],
            'result' => [
                'code' => 6307,
                'message' => 'Vendo Risk rules requires verification for this transaction.',
                'verification_url' =>  "https://secure.vend-o.com/v/verification?transaction_id=240168641&systemsignature=PyZaal0pUxehT2A-MfSgNSH0mfA"
            ],
            'request_id' => 'yij_234',
        ]));
        $httpClient->method('send')->willReturn($response);

        $payment->setHttpClient($httpClient);
        $payment->expects(self::once())->method('getHttpRequest')
            ->with(
                'POST',
                'https://secure.vend-o.com/api/gateway/payment',
                [],
                '{"api_secret":"your_secret_api_secret","is_test":1,"merchant_id":1,"amount":10.5,"currency":"USD","external_references":{"transaction_reference":"your_tx_reference_123"},"items":[{"item_id":123,"item_description":"Registration fee","item_price":4,"item_quantity":1},{"item_id":123,"item_description":"Unlimited video download","item_price":6.5,"item_quantity":1}],"customer_details":{"first_name":"John","last_name":"Doe","language":"en","email":"john.doe.test@thisisatest.test","country":"BR","national_identifier":"723.785.048-29"},"shipping_address":{"first_name":"John","last_name":"Doe","address":"123 Example Street","city":"Miami","state":"FL","country":"BR","postal_code":"33000","phone":"1000000000"},"request_details":null,"payment_details":{"payment_method":"crypto"},"mit":false,"non_recurring":false,"site_id":1,"preauth_only":false}',
                '1.1'
            );

        $payment->postRequest();

        $this->assertEquals(true, $payment->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/payment', $payment->getApiEndpoint());
        $this->assertEquals('your_secret_api_secret', $payment->getApiSecret());
        $this->assertEquals(1, $payment->getMerchantId());
        $this->assertEquals('{"status":2,"external_references":{"transaction_reference":"your_tx_reference_123"},"transaction":{"id":240168518,"amount":"10.50","currency":"USD","datetime":"2022-06-15T15:31:14+01:00"},"result":{"code":6307,"message":"Vendo Risk rules requires verification for this transaction.","verification_url":"https:\/\/secure.vend-o.com\/v\/verification?transaction_id=240168641&systemsignature=PyZaal0pUxehT2A-MfSgNSH0mfA"},"request_id":"yij_234"}', $payment->getRawResponse());
    }

    public function testCryptoPaymentError()
    {
        $payment = new \VendoSdk\Gateway\Payment();
        $payment->setApiSecret('your_secret_api_secret');

        $payment->setMerchantId(1);//Your Vendo Merchant ID
        $payment->setSiteId(0);//Your Vendo Site ID (invalid value)
        $payment->setAmount(10.50);
        $payment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
        $payment->setIsTest(true);

        $externalRef = new \VendoSdk\Gateway\Request\Details\ExternalReferences();
        $externalRef->setTransactionReference('your_tx_reference_123');
        $payment->setExternalReferences($externalRef);

        $payment->setPaymentDetails(new Crypto());

        /**
         * Add items to your request, you can add one or more
         */
        $cartItem = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem->setId(123);//set your product id
        $cartItem->setDescription('Registration fee');//your product description
        $cartItem->setPrice(4.00);
        $cartItem->setQuantity(1);
        $payment->addItem($cartItem);

        $cartItem2 = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem2->setId(123);//set your product id
        $cartItem2->setDescription('Unlimited video download');//your product description
        $cartItem2->setPrice(6.50);
        $cartItem2->setQuantity(1);
        $payment->addItem($cartItem2);

        /**
         * Customer details
         */
        $customer = new \VendoSdk\Gateway\Request\Details\Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setEmail('john.doe.test@thisisatest.test');
        $customer->setLanguageCode('en');
        $customer->setCountryCode('US');
        $customer->setNationalIdentifier('723.785.048-29');
        $payment->setCustomerDetails($customer);

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
        $payment->setShippingAddress($shippingAddress);

        $requestDetails = $this->createMock(Request::class);
        $payment->setRequestDetails($requestDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_NOT_OK,
            'error' => [
                'code' => 8105,
                'message' => 'Invalid siteId value',
            ],
            'request_id' => 'yij_234',
        ]));
        $httpClient->method('send')->willReturn($response);

        $payment->setHttpClient($httpClient);
        $payment->postRequest();

        $this->assertEquals(true, $payment->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/payment', $payment->getApiEndpoint());
        $this->assertEquals('your_secret_api_secret', $payment->getApiSecret());
        $this->assertEquals(1, $payment->getMerchantId());
        $this->assertEquals('{"status":0,"error":{"code":8105,"message":"Invalid siteId value"},"request_id":"yij_234"}', $payment->getRawResponse());
    }
}
