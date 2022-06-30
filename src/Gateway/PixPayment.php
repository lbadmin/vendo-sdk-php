<?php
namespace VendoSdk\Gateway;

use VendoSdk\Exception;

class PixPayment extends PaymentBase implements \JsonSerializable
{
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
        $fields['payment_details'] = [
            'payment_method' => 'pix',
        ];

        return $fields;
    }
}
