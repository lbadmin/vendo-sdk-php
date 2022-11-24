<?php
namespace VendoSdk\S2S\Request\Details;

class Pix implements PaymentDetails, \JsonSerializable
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'payment_method' => 'pix',
        ];
    }
}
