<?php

namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\Customer;
use VendoSdk\S2S\Request\Details\ExternalReferences;
use VendoSdk\S2S\Request\Details\Item;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\ShippingAddress;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;
use VendoSdk\S2S\Request\Details\CrossSale;
use VendoSdk\Vendo;

/**
 * Class Payment
 */
class PaymentVerification extends Payment
{
    /**
     * @return array|mixed
     */
    public function getBaseFields()
    {
        return [
            'api_secret' => $this->getApiSecret(),
            'is_test' => (int)$this->isTest(),
            'merchant_id' => $this->getMerchantId(),
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = array_merge($this->getBaseFields(), [
            'site_id' => $this->getSiteId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'external_references' => $this->getExternalReferences()->jsonSerialize(),
            'payment_details' => $this->getPaymentDetails()->jsonSerialize(),
            'shipping_address' => $this->getShippingAddress()->jsonSerialize(),
            'subscription_schedule' => $this->getSubscriptionSchedule() ? $this->getSubscriptionSchedule()->jsonSerialize() : null,
            'request_details' => $this->getRequestDetails()->jsonSerialize(),
        ]);

        return $result;
    }
}
