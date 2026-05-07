<?php
include __DIR__ . '/../../vendor/autoload.php';

$sharedSecret = getenv('VENDO_SHARED_SECRET', true) ?: 'Your_Vendo_Shared_Secret__get_it_from_us';
$customOfferLink = new \VendoSdk\Url\CustomOffer($sharedSecret);
$customOfferLink->setSite(20);
$customOfferLink->setType('normal');
$customOfferLink->setBillingScheduleType('trial');
$customOfferLink->setInitialAmount(2.95);
$customOfferLink->setInitialDuration(3);
$customOfferLink->setRebillAmount(30);
$customOfferLink->setRebillDuration(30);
// Pre-select SEPA on the hosted checkout (`pm` — see Standard Join docs).
$customOfferLink->setPm(\VendoSdk\Vendo::PAYMENT_METHOD_SEPA);
$customOfferLink->setAffiliateId(0); // zero means organic traffic or search engine traffic
$customOfferLink->setSuccessUrl('http://google.com');
$customOfferLink->setDeclineUrl('http://yahoo.com?q=test');
$customOfferLink->setRef('refxyz_999_www');

// EUR is typical for SEPA; billing_currency requires a signed URL (Custom Offer always uses signing).
$customOfferLink->setBillingCurrency(\VendoSdk\Vendo::CURRENCY_EUR);

$url = $customOfferLink->getSignedUrl();

// redirect the user to Vendo's payment page
// header('Location: ' . $url);
echo $url . PHP_EOL;
