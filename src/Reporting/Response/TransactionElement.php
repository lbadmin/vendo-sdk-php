<?php
namespace VendoSdk\Reporting\Response;

/**
 * You can get the Vendo Transaction ID with $transactionElement['id']
 *
 * @package VendoSdk\Reporting\Response
 *
 * @property int $id
 * @property int $type
 * @property bool $isNegative
 * @property bool $isTest
 * @property int $siteID
 * @property int $paymentMethodID
 * @property int $bin
 * @property int $lastFour
 * @property string $merchantReference
 * @property string $sellerAffiliateCode
 * @property string $originalTransactionID
 * @property int $programID
 * @property int $campaignID
 * @property int $affiliateID
 * @property InvoiceElement $invoice
 * @property ReportingElement $reporting
 * @property OfferElement $offer
 * @property string $refundReason
 * @property string $chargebackReason
 */
abstract class TransactionElement implements \ArrayAccess
{ }