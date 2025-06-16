<?php

namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;

class ChangeSubscription extends SubscriptionBase implements \JsonSerializable
{
    /** @var SubscriptionSchedule */
    protected $subscriptionSchedule = null;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/change-subscription';
    }

    /**
     * @return SubscriptionSchedule
     */
    public function getSubscriptionSchedule(): ?SubscriptionSchedule
    {
        return $this->subscriptionSchedule;
    }

    /**
     * @param SubscriptionSchedule $subscriptionSchedule
     */
    public function setSubscriptionSchedule(SubscriptionSchedule $subscriptionSchedule): void
    {
        $this->subscriptionSchedule = $subscriptionSchedule;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getBaseFields();
    }

    public function getBaseFields(): array
    {
        $result = parent::getBaseFields();
        if (!empty($this->getSubscriptionSchedule())) {
            $result['subscription_schedule'] = $this->getSubscriptionSchedule();
        }

        return $result;
    }
}
