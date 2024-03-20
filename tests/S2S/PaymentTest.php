<?php

namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Exception;
use VendoSdk\S2S\Request\AbstractApiBase;
use VendoSdk\S2S\Request\Details\Customer;
use VendoSdk\S2S\Request\Details\ExternalReferences;
use VendoSdk\S2S\Request\Details\Item;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\ShippingAddress;
use VendoSdk\S2S\Request\Payment;
use VendoSdk\Vendo;

class TestAbstractApiBase extends AbstractApiBase
{
    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/test-payment-endpoint';
    }
}

class PaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testApiEndpoint()
    {
        $payment = new Payment();
        self::assertEquals('https://secure.vend-o.com/api/gateway/payment', $payment->getApiEndpoint());
    }

    public function testPaymentSuccess()
    {
        $payment = new Payment();
        $payment->setIsTest(true);
        $payment->setApiSecret('test-secret');
        $payment->setIsTest(true);
        $payment->setMerchantId(1234567);
        $payment->setAmount(10.23);
        $payment->setCurrency('USD');
        $payment->setSiteId(12345);
        $payment->setIsMerchantInitiatedTransaction(false);
        $payment->setSuccessUrl('http://www.somesuccessurl.com/payment');

        $shippingAddress = new ShippingAddress();
        $shippingAddress->setState('Don San Escobar');
        $shippingAddress->setPostalCode('00951');
        $shippingAddress->setPhone('88 555 555 555');
        $shippingAddress->setLastName('Testovitchova');
        $shippingAddress->setFirstName('Sonia');
        $shippingAddress->setCountryCode('DS');
        $shippingAddress->setAddress('Testova 1');
        $shippingAddress->setCity('Testovogrod');
        $payment->setShippingAddress($shippingAddress);

        $customerDetails = new Customer();
        $customerDetails->setAddress('Testova 1');
        $customerDetails->setCity('Testovo');
        $customerDetails->setCountryCode('SE');
        $customerDetails->setEmail('jan.test@test.local');
        $customerDetails->setFirstName('Jan');
        $customerDetails->setLastName('Testovitch');
        $customerDetails->setPhone('+99 555 555 555');
        $customerDetails->setPostalCode('00950');
        $customerDetails->setState('San Escobar');
        $customerDetails->setLanguageCode('xx');
        $payment->setCustomerDetails($customerDetails);

        $externalReferences = new ExternalReferences();
        $externalReferences->setAffiliateId('affid-123');
        $externalReferences->setCampaignId('campid-234');
        $externalReferences->setProgramId('progid456');
        $externalReferences->setTransactionReference('transref 12345');
        $payment->setExternalReferences($externalReferences);

        $requestDetails = new ClientRequest();
        $requestDetails->setBrowserUserAgent('test browser');
        $requestDetails->setIpAddress('10.10.10.111');
        $payment->setRequestDetails($requestDetails);

        $payment->setPaymentDetails($this->createMock(PaymentDetails::class));

        $actualResult = $payment->jsonSerialize();

        $this->assertSame('https://secure.vend-o.com/api/gateway/payment', $payment->getApiEndpoint());

        $this->assertSame([
            'api_secret' => 'test-secret',
            'is_test' => 1,
            'merchant_id' => 1234567,
            'site_id' => 12345,
            'amount' => 10.23,
            'currency' => 'USD',
            'external_references' => [
                'transaction_reference' => 'transref 12345',
                'program_id' => 'progid456',
                'campaign_id' => 'campid-234',
                'affiliate_id' => 'affid-123',
            ],
            'items' => [],
            'payment_details' => null,
            'customer_details' => [
                'first_name' => 'Jan',
                'last_name' => 'Testovitch',
                'language' => 'xx',
                'email' => 'jan.test@test.local',
                'state' => 'San Escobar',
                'country' => 'SE',
                'postal_code' => '00950',
                'phone' => '+99 555 555 555',
            ],
            'shipping_address' => [
                'first_name' => 'Sonia',
                'last_name' => 'Testovitchova',
                'address' => 'Testova 1',
                'city' => 'Testovogrod',
                'state' => 'Don San Escobar',
                'country' => 'DS',
                'postal_code' => '00951',
                'phone' => '88 555 555 555',
            ],
            'request_details' => [
                'ip_address' => '10.10.10.111',
                'browser_user_agent' => 'test browser',
            ],
            'subscription_schedule' => null,
            'preauth_only' => false,
            'non_recurring' => false,
            'success_url' => 'http://www.somesuccessurl.com/payment',
            'mit' => false,
        ], $actualResult);
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testSetCurrencyTest(string $currencyIso, ?string $exceptionMessage)
    {
        if (isset($exceptionMessage)) {
            self::expectException(\Exception::class);
            self::expectExceptionMessage($exceptionMessage);
        }

        $payment = new Payment();
        $payment->setCurrency($currencyIso);
        self::assertEquals($currencyIso, $payment->getCurrency());
    }

    public function currencyDataProvider()
    {
        return [
            // iso / exception message
            ['USD', null],
            ['EUR', null],
            ['GBP', null],
            ['JPY', null],
            ['INV', 'The currency INV is not valid.'],
        ];
    }

    /**
     * @dataProvider itemsDataProvider
     */
    public function testSetItemsTest(?array $itemData, ?string $exceptionMessage)
    {
        $payment = new Payment();

        if (is_array($itemData)) {
            $item = new Item();
            $item->setId($itemData[0]);
            $item->setDescription($itemData[1]);
            $item->setPrice($itemData[2]);
            $item->setQuantity($itemData[3]);
        }

        if (isset($exceptionMessage)) {
            self::expectException(Exception::class);
            self::expectExceptionMessage($exceptionMessage);
        }

        $payment->setItems([$item ?? null]);
        self::assertEquals([$item], $payment->getItems());
    }

    public function itemsDataProvider()
    {
        return [
            // [id / description / price / quantity] / exception message
            [[1, 'test product', 10.23, 5], null],
            [null, 'Items must contain instances of VendoSdk\Gateway\Details\Item'],
        ];
    }

    public function testGetSet()
    {
        $payment = new Payment();

        $item = $this->createMock(Item::class);
        $payment->addItem($item);
        $payment->setAmount(5.34);
        $externalRef = $this->createMock(ExternalReferences::class);
        $payment->setExternalReferences($externalRef);
        $customerDetails = $this->createMock(Customer::class);
        $payment->setCustomerDetails($customerDetails);
        $shippingAddress = $this->createMock(ShippingAddress::class);
        $payment->setShippingAddress($shippingAddress);
        $requestDetails = $this->createMock(ClientRequest::class);
        $payment->setRequestDetails($requestDetails);

        self::assertEquals([$item], $payment->getItems());
        self::assertEquals(5.34, $payment->getAmount());
        self::assertEquals($externalRef, $payment->getExternalReferences());
        self::assertEquals($customerDetails, $payment->getCustomerDetails());
        self::assertEquals($shippingAddress, $payment->getShippingAddress());
        self::assertEquals($requestDetails, $payment->getRequestDetails());
    }

    public function testBasePaymentClientException()
    {
        $payment = new TestAbstractApiBase();
        $httpClient = $this->createMock(Client::class);
        $payment->setHttpClient($httpClient);

        $payment->setApiSecret('test-secret');
        $payment->setMerchantId(1234567);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_NOT_OK,
            'error_code' => 999,
            'error_message' => 'Test client exception',
        ]));

        $request = $this->createMock(RequestInterface::class);
        $httpClient->method('send')->willThrowException(new ClientException('Test ServerException', $request, $response));

        $payment->postRequest();
        $this->assertEquals('{"status":0,"error_code":999,"error_message":"Test client exception"}', $payment->getRawResponse());
    }

    public function testBasePaymentServerException()
    {
        $payment = new TestAbstractApiBase();
        $httpClient = $this->createMock(Client::class);
        $payment->setHttpClient($httpClient);

        $payment->setApiSecret('test-secret');
        $payment->setMerchantId(1234567);

        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $httpClient->method('send')->willThrowException(new ServerException('Test ServerException', $request, $response));

        $this->expectException(\Exception::class);
        $this->expectErrorMessage('A server exception occurred. If this persists then contact Vendo Client Support');
        $payment->postRequest();
    }
}
