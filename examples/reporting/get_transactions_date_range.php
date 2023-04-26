<?php

use VendoSdk\Reporting\Transaction;

include __DIR__ . '/../../vendor/autoload.php';

/*
 * This scripts gets all the successful transactions processed
 * for Vendo Merchant Id 1 and their Vendo Site Ids 1 and 2
 * on June 28th, 2021 00:00 to July 5th, 2021 23:59:59
 */
$sharedSecret = getenv('VENDO_SHARED_SECRET', true)?:'Your_Vendo_Shared_Secret__get_it_from_us';
$siteIds = explode(",", getenv('VENDO_TRANSACTION_SITE_IDS', true)?:'1,2');
$reporting = new \VendoSdk\Reporting\Reconciliation($sharedSecret);
$reporting->setMerchantId(getenv('VENDO_MERCHANT_ID',  true) ?: 'Your_vendo_merchant_id');
$reporting->setSiteIds($siteIds);
$reporting->setStartDate(DateTime::createFromFormat('Y-m-d', '2021-06-28'));
$reporting->setEndDate(DateTime::createFromFormat('Y-m-d', '2021-07-05'));

try {
    if ($reporting->postRequest()) {
        $reportingData = $reporting->getTransactions();

        if (!empty($reportingData)) {
            foreach ($reportingData as $row) {
                //Do something with the data
                echo "Vendo Transaction ID = " . $row->transaction['id'] . ", ";
                echo "Merchant reference = " . $row->transaction->merchantReference . ", ";
                echo "Vendo Subscription ID = " . $row->subscription['id'] . ", ";
                echo "Subscription Username = " . $row->subscription->username . "\n";
                echo "Vendo Customer ID = " . $row->customer['id'] . ", ";
                echo "Customer First name = " . $row->customer->firstname . ", ";
                echo "Customer Email address = " . $row->customer->email . ", ";
                if (in_array($row->transaction->type , [Transaction::TYPE_REFUND, Transaction::TYPE_PARTIAL_REFUND])) {
                    echo "Refund reason = " . $row->transaction->refundReason . ", ";
                }
                if ($row->transaction->type == Transaction::TYPE_CHARGEBACK) {
                    echo "Chargeback reason = " . $row->transaction->chargebackReason . ", ";
                }
                echo "\n";
            }
        } else {
            echo "No data returned by Vendo with the given Site IDs and date range.";
        }
    }
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    echo "Vendo SDK's HTTP client returned an error: " . $e->getMessage();
} catch (\VendoSdk\Exception $e) {
    echo "The VendoSdk threw an application exception: " . $e->getMessage();
}

