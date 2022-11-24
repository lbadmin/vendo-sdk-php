<?php
namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\CapturePayment;
use VendoSdk\Vendo;

class CapturePaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testCapturePaymentSuccess()
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
            'status' => Vendo::S2S_STATUS_OK,
            'request_id' => 234,
            'transaction' => [
                'id' => 9876543,
                'amount' => 10.23,
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
        $this->assertEquals('{"status":1,"request_id":234,"transaction":{"id":9876543,"amount":10.23,"currency":"USD","datetime":"2021-11-10 14:52:34"}}', $payment->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "transaction_id": 87654321
}', $payment->getRawRequest(true));
    }

    public function testCapturePaymentClientException()
    {
        $payment = new CapturePayment();
        $httpClient = $this->createMock(Client::class);
        $payment->setHttpClient($httpClient);

        $payment->setApiSecret('test-secret');
        $payment->setMerchantId(1234567);
        $payment->setTransactionId(87654321);

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

    public function testCapturePaymentServerException()
    {
        $payment = new CapturePayment();
        $httpClient = $this->createMock(Client::class);
        $payment->setHttpClient($httpClient);

        $payment->setApiSecret('test-secret');
        $payment->setMerchantId(1234567);
        $payment->setTransactionId(87654321);

        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $httpClient->method('send')->willThrowException(new ServerException('Test ServerException', $request, $response));

        $this->expectException(\Exception::class);
        $this->expectErrorMessage('A server exception occurred. If this persists then contact Vendo Client Support');
        $payment->postRequest();
    }
}
