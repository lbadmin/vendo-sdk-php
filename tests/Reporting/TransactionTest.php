<?php
namespace VendoSdkUnit\Reporting;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use VendoSdk\Exception;

class TransactionTest extends \PHPUnit\Framework\TestCase
{

    private function getMockHttpClient($response): Client
    {
        $mock = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }

    public function testGetDetailsOk(): void
    {
        $mockHttpClient = $this->getMockHttpClient(new Response(200, [], file_get_contents(__DIR__ . '/transactionTest_ok.xml')));
        $reporting = new \VendoSdk\Reporting\Transaction('the_secret');
        $reporting->setMerchantId(1);
        $reporting->setTransactionId(73806356);
        $reporting->setHttpClient($mockHttpClient);

        $this->assertEquals(true, $reporting->postRequest());

        $row = $reporting->getDetails();
        $this->assertEquals('73806356', $row->transaction['id']);
        $this->assertEquals('test249576', $row->subscription->username);
        $this->assertEquals('Test', $row->customer->lastname);
    }

    public function testGetDetailsError(): void
    {
        $this->expectException(Exception::class);

        $mockHttpClient = $this->getMockHttpClient(new Response(200, [], file_get_contents(__DIR__ . '/transactionTest_error.xml')));
        $reporting = new \VendoSdk\Reporting\Transaction('the_secret');
        $reporting->setMerchantId(1);
        $reporting->setTransactionId(7380635);
        $reporting->setHttpClient($mockHttpClient);
        $reporting->postRequest();
        $row = $reporting->getDetails();
    }
}
