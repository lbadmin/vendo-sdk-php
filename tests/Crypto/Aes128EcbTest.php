<?php
namespace VendoSdkUnit\Crypto;

use VendoSdk\Crypto\Aes128Ecb;

class Aes128EcbTest extends \PHPUnit\Framework\TestCase
{

    public function testAesEncryptDecrypt()
    {
        $sharedSecret = 'test';
        $plaintext = 'foobarbaz';
        $encrypted = Aes128Ecb::encrypt($plaintext, $sharedSecret);
        $decrypted = Aes128Ecb::decrypt($encrypted, $sharedSecret);

        $this->assertEquals($plaintext, $decrypted);
    }
}
