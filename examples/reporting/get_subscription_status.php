<?php
include __DIR__ . '/../../vendor/autoload.php';

use VendoSdk\Reporting\Subscription;

/*
 * This scripts gets the details of one specific transaction.
 */
$sharedSecret = getenv('VENDO_SHARED_SECRET', true)?:'Your_Vendo_Shared_Secret__get_it_from_us';

$reporting = new \VendoSdk\Reporting\Subscription($sharedSecret);
$reporting->setSubscriptionId(74183461);

try {
    if ($reporting->sendGetRequest()) {
        $status = $reporting->getDetails();
        if (!empty($status)) {
            //Do something with the data
            echo "Subscription ID = " . $status->id . ", ";
            echo "Status code = " . $status->code . ", ";
            echo "Status name = " . $status->message . ", ";
            echo "\n";
        } else {
            echo "No data returned by Vendo with the given Vendo Subscription Id.";
        }
    }
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    echo "Vendo SDK's HTTP client returned an error: " . $e->getMessage();
} catch (\VendoSdk\Exception $e) {
    echo "The VendoSdk threw an application exception: " . $e->getMessage();
}

