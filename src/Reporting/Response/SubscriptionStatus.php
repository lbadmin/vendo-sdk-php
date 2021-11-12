<?php
namespace VendoSdk\Reporting\Response;

/**
 *
 * @package VendoSdk\Reconciliation\Response
 *
 * @property int $id
 * @property int $code
 * @property int $message
 */
class SubscriptionStatus
{
    public function __construct(int $subscriptionId, ?int $code, ?string $message)
    {
        $this->id = $subscriptionId;
        $this->code = $code;
        $this->message = $message;
    }
}