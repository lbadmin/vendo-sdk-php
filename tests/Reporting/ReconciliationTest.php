<?php
namespace VendoSdkUnit\Reporting;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use VendoSdk\Exception;

class ReconciliationTest extends \PHPUnit\Framework\TestCase
{

    private function getMockHttpClient($response): Client
    {
        $mock = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }

    public function testGetTransactions(): void
    {
        $mockHttpClient = $this->getMockHttpClient(new Response(200, [], file_get_contents(__DIR__ . '/reconciliationTest_ok.xml')));
        $reporting = new \VendoSdk\Reporting\Reconciliation('the_secret');
        $reporting->setMerchantId(1);
        $reporting->setSiteIds([1, 2]);
        $reporting->setStartDate(\DateTime::createFromFormat('Y-m-d', '2021-06-28'));
        $reporting->setEndDate(\DateTime::createFromFormat('Y-m-d', '2021-07-05'));
        $reporting->setHttpClient($mockHttpClient);

        $this->assertEquals(true, $reporting->postRequest());

        $rows = $reporting->getTransactions();

        $row = $rows[0];
        $this->assertEquals('73768494', $row->transaction['id']);
        $this->assertEquals('test249572', $row->subscription->username);
        $this->assertEquals('TestLastname', $row->customer->lastname);

        $row = $rows[1];
        $this->assertEquals('73769604', $row->transaction['id']);
        $this->assertEquals('testusername', $row->subscription->username);
        $this->assertEquals('Test', $row->customer->lastname);
    }

    public function testGetTransactionsError(): void
    {
        $this->expectException(Exception::class);

        $mockHttpClient = $this->getMockHttpClient(new Response(200, [], file_get_contents(__DIR__ . '/reconciliationTest_error.xml')));
        $reporting = new \VendoSdk\Reporting\Reconciliation('the_secret');
        $reporting->setMerchantId(1);
        $reporting->setSiteIds([1, 2]);
        $reporting->setStartDate(\DateTime::createFromFormat('Y-m-d', '2021-06-28'));
        $reporting->setEndDate(\DateTime::createFromFormat('Y-m-d', '2021-07-05'));
        $reporting->setHttpClient($mockHttpClient);
        $reporting->postRequest();
        $rows = $reporting->getTransactions();
    }
}
