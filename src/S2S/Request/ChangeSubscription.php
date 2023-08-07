<?php

namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;

class ChangeSubscription extends SubscriptionBase implements \JsonSerializable
{
    /** @var SubscriptionSchedule */
    protected $subscriptionSchedule = null;

    /** @var PaymentDetails */
    protected $paymentDetails = null;

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
     * @return PaymentDetails
     */
    public function getPaymentDetails(): ?PaymentDetails
    {
        return $this->paymentDetails;
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public function setPaymentDetails(PaymentDetails $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $fields = $this->getBaseFields();

        return $fields;
    }

    public function getBaseFields(): array
    {
        $result = parent::getBaseFields();
        if (!empty($this->getSubscriptionSchedule())) {
            $result['subscription_schedule'] = $this->getSubscriptionSchedule();
        }

        if (!empty($this->getPaymentDetails())) {
            $result['payment_details'] = $this->getPaymentDetails();
        }

        if (!empty($this->getRequestDetails())) {
            $result['request_details'] = $this->getRequestDetails();
        }

        return $result;
    }
}
