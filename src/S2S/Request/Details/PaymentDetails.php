<?php

namespace VendoSdk\S2S\Request\Details;


interface PaymentDetails
{
    /**
     * @return array - an array of fields to serialize
     */
    public function jsonSerialize();
}