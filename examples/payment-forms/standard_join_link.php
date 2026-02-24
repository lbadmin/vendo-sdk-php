<?php
include __DIR__ . '/../../vendor/autoload.php';

$sharedSecret = getenv('VENDO_SHARED_SECRET', true) ?: 'Your_Vendo_Shared_Secret__get_it_from_us';
$joinLink = new \VendoSdk\Url\Join($sharedSecret);
$joinLink->setSite(2);
$joinLink->setOffers([123,222,333]);
$joinLink->setSelectedOffer(222);
$joinLink->setAffiliateId(0); // zero means organic traffic or search engine traffic
$joinLink->setSuccessUrl('http://google.com');
$joinLink->setDeclineUrl('http://yahoo.com?q=test');
$joinLink->setRef('refxyz_999_www');

// Optional: force display and billing currency (USD, EUR or GBP). When set, the URL must be signed.
$joinLink->setBillingCurrency(\VendoSdk\Vendo::CURRENCY_EUR);

// Use getSignedUrl() when billing_currency (or other signed-only params) are set; otherwise getUrl() is enough
$url = $joinLink->getSignedUrl();

// redirect the user to Vendo's payment page
// header('Location: ' . $url);
echo $url . PHP_EOL;
