<?php
namespace VendoSdk\Gateway\Request\Details;

class Crypto implements PaymentDetails, \JsonSerializable
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'payment_method' => 'crypto',
        ];
    }
}
