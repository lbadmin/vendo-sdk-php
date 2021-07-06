<?php
namespace VendoSdk\Reporting\Response;

/**
 * Class RowElement
 *
 * @package VendoSdk\Reporting\Response
 *
 * @property TransactionElement $transaction
 * @property CustomerElement $customer
 * @property SubscriptionElement $subscription
 */
abstract class RowElement implements \ArrayAccess
{}