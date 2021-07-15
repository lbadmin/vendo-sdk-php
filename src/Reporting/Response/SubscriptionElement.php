<?php
namespace VendoSdk\Reporting\Response;

/**
 * You can get the Vendo Subscription ID with $subscriptionElement['id']
 *
 * @package VendoSdk\Reporting\Response
 *
 * @property string $username
 * @property string $password
 */
abstract class SubscriptionElement implements \ArrayAccess
{}