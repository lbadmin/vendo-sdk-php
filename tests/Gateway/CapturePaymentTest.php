<?php
namespace VendoSdkUnit\Gateway;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Gateway\CapturePayment;
use VendoSdk\Vendo;

class CapturePaymentTest extends \PHPUnit\Framework\TestCase
{

    public function testCapturePaymentGetSet()
    {
        $payment = new CapturePayment();
        $payment->setIsTest(true);
        $payment->setApiSecret('test-secret');
        $payment->setIsTest(true);
        $payment->setMerchantId(1234567);
        $payment->setTransactionId(87654321);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_OK,
            'request_id' => 234,
            'transaction' => [
                'id' => 9876543,
                'amount' => '10.23',
                'currency' => 'USD',
                'datetime' => '2021-11-10 14:52:34',
            ],
        ]));
        $httpClient->method('send')->willReturn($response);

        $payment->setHttpClient($httpClient);
        $payment->postRequest();

        $this->assertEquals(true, $payment->isTest());
        $this->assertEquals('https://secure.vend-o.com/api/gateway/capture', $payment->getApiEndpoint());
        $this->assertEquals('test-secret', $payment->getApiSecret());
        $this->assertEquals(1234567, $payment->getMerchantId());
        $this->assertEquals(87654321, $payment->getTransactionId());
        $this->assertEquals('{"status":1,"request_id":234,"transaction":{"id":9876543,"amount":"10.23","currency":"USD","datetime":"2021-11-10 14:52:34"}}', $payment->getRawResponse());
        $this->assertEquals('{"api_secret":"test-secret","merchant_id":1234567,"is_test":1,"transaction_id":87654321}', $payment->getRawRequest());
    }
}
