<?php
namespace VendoSdkUnit\Util;

use VendoSdk\Util\Signature;

class SignatureTest extends \PHPUnit\Framework\TestCase
{
    public function testSign()
    {
        $sharedSecret = 'test';
        $signer = new Signature($sharedSecret);
        $signedUrl = $signer->sign("http://dummy/?test=test");
        $this->assertEquals("http://dummy/?test=test&signature=FHmXgS5ptIzbkeSWqc1AnPYZcxU", $signedUrl);
        $signedUrl = $signer->sign("http://mydummy.com/?test=test&foo=barz");
        $this->assertEquals("http://mydummy.com/?test=test&foo=barz&signature=Nj-UJLpDvEI-F-epN44SG-8nqCg", $signedUrl);
        $this->assertTrue($signer->isValidUrl($signedUrl));

        $signedUrl = $signedUrl . '&new=value';
        $this->assertFalse($signer->isValidUrl($signedUrl));
    }

    public function testGetSignature()
    {
        $sharedSecret = 'test';
        $signer = new Signature($sharedSecret);
        $signature = $signer->getSignature("/?test=test");

        $sharedSecret = 'test2';
        $signer = new Signature($sharedSecret);
        $signature2 = $signer->getSignature("/?test=test");

        $this->assertEquals("FHmXgS5ptIzbkeSWqc1AnPYZcxU", $signature);
        $this->assertNotEquals("FHmXgS5ptIzbkeSWqc1AnPYZcxU", $signature2);
    }

    public function testIsValid()
    {
        $sharedSecret = 'test';
        $signer = new Signature($sharedSecret);
        $isValid = $signer->isValid('/?test=test', 'FHmXgS5ptIzbkeSWqc1AnPYZcxU');
        $this->assertTrue($isValid);
        $isValid = $signer->isValid('/?test=t', 'FHmXgS5ptIzbkeSWqc1AnPYZcxU');
        $this->assertFalse($isValid);
        $isValid = $signer->isValid('/?test=test', 'dldldl');
        $this->assertFalse($isValid);
    }

    /**
     * @dataProvider isValidUrlData
     *
     * @param $url
     * @param $expectedResult
     * @throws \VendoSdk\Exception
     */
    public function testIsValidUrl($url, $expectedResult)
    {
        $sharedSecret = 'test';
        $signer = new Signature($sharedSecret);
        $actualResult = $signer->isValidUrl($url);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function isValidUrlData(): array
    {
        return [
            ['http://dummy.test/?expires=31553280&signature=xzy', false],
            ['http://dummy.test/?expires=31553280', false],
            ['http://dummy/?test=test&signature=FHmXgS5ptIzbkeSWqc1AnPYZcxU', true],
            ['http://dummy/?test=test&foo=bar&signature=FHmXgS5ptIzbkeSWqc1AnPYZcxU', false],
        ];
    }
}