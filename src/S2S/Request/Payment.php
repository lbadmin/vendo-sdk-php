<?php

namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;

/**
 * Class Payment
 * @package VendoSdk\Gateway
 */
class Payment extends PaymentBase implements \JsonSerializable
{

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/payment';
    }


    /**
     * @return ?SubscriptionSchedule
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
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $fields = $this->getBaseFields();
        $fields['site_id'] = $this->getSiteId();
        $fields['payment_details'] = $this->getPaymentDetails()->jsonSerialize();
        $fields['preauth_only'] = $this->isPreAuth();
        if (!empty($this->getSubscriptionSchedule())) {
            $fields['subscription_schedule'] = $this->getSubscriptionSchedule();
        }

        return $fields;
    }
}
