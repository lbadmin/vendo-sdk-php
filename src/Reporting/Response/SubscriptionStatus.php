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
    public string|int|null $id;
    public ?int $code;
    public ?string $message;

    public function __construct(string|int|null $subscriptionId, ?int $code, ?string $message)
    {
        $this->id = $subscriptionId;
        $this->code = $code;
        $this->message = $message;
    }
}