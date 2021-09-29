<?php
include __DIR__ . '/../../vendor/autoload.php';

$sharedSecret = 'Your_Vendo_Shared_Secret__get_it_from_us';
$oneclikLink = new \VendoSdk\Url\Oneclick($sharedSecret);
$oneclikLink->setSubscription(222333444555);
$oneclikLink->setOffer(222);
$oneclikLink->setSuccessUrl('http://google.com');
$oneclikLink->setDeclineUrl('http://yahoo.com?q=test');
$oneclikLink->setRef('refxyz_999_www');

$url = $oneclikLink->getSignedUrl();

//redirect the user to Vendo's payment page
//header('Location: ' . $url);
echo $url . PHP_EOL;
