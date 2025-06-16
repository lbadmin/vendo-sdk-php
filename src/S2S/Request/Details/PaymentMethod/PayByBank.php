<?php
namespace VendoSdk\S2S\Request\Details\PaymentMethod;

use VendoSdk\S2S\Request\Details\PaymentDetails;

class PayByBank implements PaymentDetails, \JsonSerializable
{
    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'payment_method' => 'paybybank',
        ];
    }
}
