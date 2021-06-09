<?php
include __DIR__ . '../vendor/autoload.php';

$sharedSecret = 'Your_Vendo_Shared_Secret__get_it_from_us';
$joinLink = new \VendoSdk\Url\Join($sharedSecret);
$joinLink->setSite(2);
$joinLink->setOffers([123,222,333]);
$joinLink->setSelectedOffer(222);
$joinLink->setSuccessUrl('http://google.com');
$joinLink->setDeclineUrl('http://yahoo.com?q=test');
$joinLink->setRef('refxyz_999_www');

$url = $joinLink->getSignedUrl();

//redirect the user to Vendo's payment page
header('Location: ' . $url);