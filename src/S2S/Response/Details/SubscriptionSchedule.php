<?php
namespace VendoSdk\S2S\Response\Details;

class SubscriptionSchedule
{
    /** @var ?string */
    protected $subscriptionId;

    /**
     * @param array $subscriptionSchedule
     * @throws \Exception
     */
    public function __construct(array $subscriptionSchedule)
    {
        $this->setSubscriptionId($subscriptionSchedule['subscription_id'] ?? null);
    }

    /**
     * @return string|null
     */
    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    /**
     * @param string|null $subscriptionId
     */
    public function setSubscriptionId(?string $subscriptionId): void
    {
        $this->subscriptionId = $subscriptionId;
    }
}
