<?php

namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;

class UpdatePaymentMethod extends SubscriptionBase implements \JsonSerializable
{
    /** @var PaymentDetails */
    protected $paymentDetails = null;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/update-payment-method';
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
        if (!empty($this->getPaymentDetails())) {
            $result['payment_details'] = $this->getPaymentDetails();
        }

        if (!empty($this->getRequestDetails())) {
            $result['request_details'] = $this->getRequestDetails();
        }

        return $result;
    }
}
