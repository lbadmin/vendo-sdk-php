<?php
namespace VendoSdkUnit\Url;

use VendoSdk\Url\Join;
use VendoSdk\Vendo;

class JoinTest extends \PHPUnit\Framework\TestCase
{

    public function testGetUrl()
    {
        $sharedSecret = 'test';
        $url = new Join($sharedSecret);
        $url->setSite(1);
        $url->setAffiliateId(0);
        $url->setDeclineUrl('http://yahoo.com');
        $url->setSuccessUrl('http://google.com');
        $url->setRef('xyz');
        $url->setUsername('foo');
        $url->setPassword('barbarbar');
        $url->setEmail('foo@bazdummy.com');

        $linkTo = $url->getUrl();

        $expectedUrl =  'https://secure.vend-o.com/v/signup?site=1&affiliate_id=0&decline_url=http%3A%2F%2Fyahoo.com&success_url=http%3A%2F%2Fgoogle.com&ref=xyz&username=foo&password_encrypted=%D5%22%BB%B1%87%7D%C9%09%F0%DC%9C%8A%C9%97w%D6&email=foo%40bazdummy.com&sdkv=' . Vendo::SDK_VERSION;

        $this->assertEquals($expectedUrl, $linkTo);

    }
}
