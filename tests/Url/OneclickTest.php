<?php
namespace VendoSdkUnit\Url;

use VendoSdk\Url\Oneclick;
use VendoSdk\Vendo;

class OneclickTest extends \PHPUnit\Framework\TestCase
{

    public function testGetUrl()
    {
        $sharedSecret = 'test';
        $url = new Oneclick($sharedSecret);
        $url->setSubscription(112233);
        $url->setOffer(998877);
        $url->setDeclineUrl('http://yahoo.com');
        $url->setSuccessUrl('http://google.com');
        $url->setRef('xyzfoobar');

        $linkTo = $url->getUrl();

        $expectedUrl = 'https://secure.vend-o.com/v/oneclick?subscription=112233&offer=998877&decline_url=http%3A%2F%2Fyahoo.com&success_url=http%3A%2F%2Fgoogle.com&ref=xyzfoobar&sdkv=' . Vendo::SDK_VERSION;

        $this->assertEquals($expectedUrl, $linkTo);

    }
}
