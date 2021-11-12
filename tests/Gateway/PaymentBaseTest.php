<?php

namespace VendoSdkUnit\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Exception;
use VendoSdk\Gateway\BasePayment;
use VendoSdk\Gateway\PaymentBase;
use VendoSdk\Gateway\Request\Details\Base;
use VendoSdk\Gateway\Request\Details\Customer;
use VendoSdk\Gateway\Request\Details\ExternalReferences;
use VendoSdk\Gateway\Request\Details\Item;
use VendoSdk\Gateway\Request\Details\Request;
use VendoSdk\Gateway\Request\Details\ShippingAddress;
use VendoSdk\Gateway\Response\PaymentResponse;
use VendoSdk\Vendo;

class TestPaymentBase extends PaymentBase
{
    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/test-payment-endpoint';
    }
}

class PaymentBaseTest extends \PHPUnit\Framework\TestCase
{
    public function testBasePaymentSuccess()
    {
        $payment = new TestPaymentBase();
        $payment->setIsTest(true);
        $payment->setApiSecret('test-secret');
        $payment->setIsTest(true);
        $payment->setMerchantId(1234567);
        $payment->setAmount(10.23);
        $payment->setCurrency('USD');
        $payment->setSiteId(12345);
        $payment->setIsMerchantInitiatedTransaction(false);

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

        $requestDetails = new Request();
        $requestDetails->setBrowserUserAgent('test browser');
        $requestDetails->setIpAddress('10.10.10.111');
        $payment->setRequestDetails($requestDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_OK,
            'request_id' => 234,
            'external_references' => [
                'affiliate_id' => 'affid-123',
                'campaign_id' => 'campid-234',
                'program_id' => 'prog_id',
                'transaction_reference' => 'transref 12345',
            ],
            'transaction' => [
                'id' => 9876543,
                'amount' => 10.23,
                'currency' => 'USD',
                'datetime' => '2021-11-10 14:52:34',
            ],
            'card_details' => [
                'auth_code' => 'test-auth-code',
            ],
            'sepa_details' => [
                'mandate_id' => '123-345',
                'mandate_signed_date' => '2021-11-10 14:52:34',
            ],
            'payment_details_token' => 'some-paymentdetails-token-1234abcd',
        ]));
        $httpClient->method('send')->willReturn($response);

        $payment->setHttpClient($httpClient);
        $paymentResponse = $payment->postRequest();

        $this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
        $this->assertEquals(true, $payment->isTest());
        $this->assertEquals('https://secure.vend-o.com/api/gateway/test-payment-endpoint', $payment->getApiEndpoint());
        $this->assertEquals('test-secret', $payment->getApiSecret());
        $this->assertEquals(1234567, $payment->getMerchantId());
        $this->assertEquals('{"status":1,"request_id":234,"external_references":{"affiliate_id":"affid-123","campaign_id":"campid-234","program_id":"prog_id","transaction_reference":"transref 12345"},"transaction":{"id":9876543,"amount":10.23,"currency":"USD","datetime":"2021-11-10 14:52:34"},"card_details":{"auth_code":"test-auth-code"},"sepa_details":{"mandate_id":"123-345","mandate_signed_date":"2021-11-10 14:52:34"},"payment_details_token":"some-paymentdetails-token-1234abcd"}', $payment->getRawResponse());
        $this->assertEquals('{}', $payment->getRawRequest(true));
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testSetCurrencyTest(string $currencyIso, ?string $exceptionMessage)
    {
        if (isset($exceptionMessage)){
            self::expectException(\Exception::class);
            self::expectExceptionMessage($exceptionMessage);
        }

        $payment = new TestPaymentBase();
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
        $payment = new TestPaymentBase();

        if(is_array($itemData)){
            $item = new Item();
            $item->setId($itemData[0]);
            $item->setDescription($itemData[1]);
            $item->setPrice($itemData[2]);
            $item->setQuantity($itemData[3]);
        }

        if (isset($exceptionMessage)){
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
        $payment = new TestPaymentBase();

        $item = $this->createMock(Item::class);
        $payment->addItem($item);
        $payment->setAmount(5.34);
        $externalRef = $this->createMock(ExternalReferences::class);
        $payment->setExternalReferences($externalRef);
        $customerDetails = $this->createMock(Customer::class);
        $payment->setCustomerDetails($customerDetails);
        $shippingAddress = $this->createMock(ShippingAddress::class);
        $payment->setShippingAddress($shippingAddress);
        $requestDetails = $this->createMock(Request::class);
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
        $payment = new TestPaymentBase();
        $httpClient = $this->createMock(Client::class);
        $payment->setHttpClient($httpClient);

        $payment->setApiSecret('test-secret');
        $payment->setMerchantId(1234567);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_NOT_OK,
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
        $payment = new TestPaymentBase();
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
