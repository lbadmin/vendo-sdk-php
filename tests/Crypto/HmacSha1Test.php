<?php
namespace VendoSdkUnit\Crypto;

use VendoSdk\Crypto\HmacSha1;

class HmacSha1Test extends \PHPUnit\Framework\TestCase
{

    public function testAesEncryptDecrypt()
    {
        $sharedSecret = 'test';
        $plaintext = 'foobarbaz';
        $hmacSha1Helper = new HmacSha1($sharedSecret);
        $hash = $hmacSha1Helper->getHash($plaintext);

        $this->assertEquals('A8TXpuvf9jGDj_tzBgqSKvz0jKw', $hash);
    }
}
