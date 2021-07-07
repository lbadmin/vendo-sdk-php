<?php
include __DIR__ . '/../../vendor/autoload.php';

/*
 * This scripts gets the details of one specific transaction.
 */
$sharedSecret = 'Your_Vendo_Shared_Secret__get_it_from_us';
$reporting = new VendoSdk\Reporting\Transaction($sharedSecret);
$reporting->setMerchantId(1);
$reporting->setTransactionId(73768494);

try {
    if ($reporting->postRequest()) {
        $row = $reporting->getDetails();

        if (!empty($row)) {
            //Do something with the data
            echo "Vendo Transaction ID = " . $row->transaction['id'] . ", ";
            echo "Merchant reference = " . $row->transaction->merchantReference . ", ";
            echo "Vendo Subscription ID = " . $row->subscription['id'] . ", ";
            echo "Subscription Username = " . $row->subscription->username . "\n";
            echo "Vendo Customer ID = " . $row->customer['id'] . ", ";
            echo "Customer First name = " . $row->customer->firstname . ", ";
            echo "Customer Email address = " . $row->customer->email . ", ";
            if (in_array($row->transaction->type , [30, 31])) {//is it a refund or a partial refund?
                echo "Refund reason = " . $row->transaction->refundReason . ", ";
            }
            if ($row->transaction->type == 20) {//is it a chargeback?
                echo "Chargeback reason = " . $row->transaction->chargebackReason . ", ";
            }
            echo "\n";
        } else {
            echo "No data returned by Vendo with the given Vendo Transaction Id.";
        }
    }
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    echo "Vendo SDK's HTTP client returned an error: " . $e->getMessage();
} catch (\VendoSdk\Exception $e) {
    echo "The VendoSdk threw an application exception: " . $e->getMessage();
}

