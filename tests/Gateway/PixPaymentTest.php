<?php

namespace VendoSdkUnit\Gateway;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Gateway\Request\Details\Request;
use VendoSdk\Vendo;

class PixPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testPixPaymentSuccess()
    {
        $pixPayment = new \VendoSdk\Gateway\PixPayment();
        $pixPayment->setApiSecret('your_secret_api_secret');

        $pixPayment->setMerchantId(1);//Your Vendo Merchant ID
        $pixPayment->setSiteId(1);//Your Vendo Site ID
        $pixPayment->setAmount(10.50);
        $pixPayment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
        $pixPayment->setIsTest(true);

        $externalRef = new \VendoSdk\Gateway\Request\Details\ExternalReferences();
        $externalRef->setTransactionReference('your_tx_reference_123');
        $pixPayment->setExternalReferences($externalRef);

        /**
         * Add items to your request, you can add one or more
         */
        $cartItem = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem->setId(123);//set your product id
        $cartItem->setDescription('Registration fee');//your product description
        $cartItem->setPrice(4.00);
        $cartItem->setQuantity(1);
        $pixPayment->addItem($cartItem);

        $cartItem2 = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem2->setId(123);//set your product id
        $cartItem2->setDescription('Unlimited video download');//your product description
        $cartItem2->setPrice(6.50);
        $cartItem2->setQuantity(1);
        $pixPayment->addItem($cartItem2);

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
        $pixPayment->setCustomerDetails($customer);

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
        $pixPayment->setShippingAddress($shippingAddress);

        $requestDetails = $this->createMock(Request::class);
        $pixPayment->setRequestDetails($requestDetails);

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

        $pixPayment->setHttpClient($httpClient);
        $pixPayment->postRequest();

        $this->assertEquals(true, $pixPayment->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/payment', $pixPayment->getApiEndpoint());
        $this->assertEquals('your_secret_api_secret', $pixPayment->getApiSecret());
        $this->assertEquals(1, $pixPayment->getMerchantId());
        $this->assertEquals('{"status":2,"external_references":{"transaction_reference":"your_tx_reference_123"},"transaction":{"id":240168518,"amount":"10.50","currency":"USD","datetime":"2022-06-15T15:31:14+01:00"},"result":{"code":6307,"message":"Vendo Risk rules requires verification for this transaction.","verification_url":"https:\/\/secure.vend-o.com\/v\/verification?transaction_id=240168641&systemsignature=PyZaal0pUxehT2A-MfSgNSH0mfA"},"request_id":"yij_234"}', $pixPayment->getRawResponse());
    }

    public function testPixPaymentError()
    {
        $pixPayment = new \VendoSdk\Gateway\PixPayment();
        $pixPayment->setApiSecret('your_secret_api_secret');

        $pixPayment->setMerchantId(1);//Your Vendo Merchant ID
        $pixPayment->setSiteId(0);//Your Vendo Site ID (invalid value)
        $pixPayment->setAmount(10.50);
        $pixPayment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
        $pixPayment->setIsTest(true);

        $externalRef = new \VendoSdk\Gateway\Request\Details\ExternalReferences();
        $externalRef->setTransactionReference('your_tx_reference_123');
        $pixPayment->setExternalReferences($externalRef);

        /**
         * Add items to your request, you can add one or more
         */
        $cartItem = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem->setId(123);//set your product id
        $cartItem->setDescription('Registration fee');//your product description
        $cartItem->setPrice(4.00);
        $cartItem->setQuantity(1);
        $pixPayment->addItem($cartItem);

        $cartItem2 = new \VendoSdk\Gateway\Request\Details\Item();
        $cartItem2->setId(123);//set your product id
        $cartItem2->setDescription('Unlimited video download');//your product description
        $cartItem2->setPrice(6.50);
        $cartItem2->setQuantity(1);
        $pixPayment->addItem($cartItem2);

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
        $pixPayment->setCustomerDetails($customer);

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
        $pixPayment->setShippingAddress($shippingAddress);

        $requestDetails = $this->createMock(Request::class);
        $pixPayment->setRequestDetails($requestDetails);

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

        $pixPayment->setHttpClient($httpClient);
        $pixPayment->postRequest();

        $this->assertEquals(true, $pixPayment->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/payment', $pixPayment->getApiEndpoint());
        $this->assertEquals('your_secret_api_secret', $pixPayment->getApiSecret());
        $this->assertEquals(1, $pixPayment->getMerchantId());
        $this->assertEquals('{"status":0,"error":{"code":8105,"message":"Invalid siteId value"},"request_id":"yij_234"}', $pixPayment->getRawResponse());
    }
}
