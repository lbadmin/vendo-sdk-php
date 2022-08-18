<?php

namespace VendoSdk\Gateway;

use VendoSdk\Exception;

/**
 * Class Payment
 * @package VendoSdk\Gateway
 */
class Payment extends PaymentBase implements \JsonSerializable
{
    /** @var bool */
    protected $isPreAuth = false;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/payment';
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

        return $fields;
    }
}
