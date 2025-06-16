<?php
namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\CancelSubscription;
use VendoSdk\Vendo;

class CancelSubscriptionTest extends \PHPUnit\Framework\TestCase
{
    public function testCancelSubscriptionSuccess()
    {
        $changeSubscription = new CancelSubscription();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(
            $this->returnStream(
            json_encode([
                'status' => Vendo::S2S_STATUS_OK,
                'request_id' => 234,
                'subscription' => [
                    'id' => 9876543,
                ],])
            )
        );
        $httpClient->method('send')->willReturn($response);

        $changeSubscription->setHttpClient($httpClient);
        $changeSubscription->postRequest();

        $this->assertEquals(true, $changeSubscription->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/cancel-subscription', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":1,"request_id":234,"subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321
}', $changeSubscription->getRawRequest(true));
    }

    protected function returnStream(string $json): \Psr\Http\Message\StreamInterface
    {
        if ($f = fopen('data://text/plain,' . $json,'r')) {
            $stream = new \GuzzleHttp\Psr7\Stream($f);
        } else {
            $stream = new \GuzzleHttp\Psr7\Stream(
                fopen('php://temp', 'r+')
            );
        }
        return $stream;
    }
}
