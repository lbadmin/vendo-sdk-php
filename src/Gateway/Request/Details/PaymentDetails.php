<?php

namespace VendoSdk\Gateway\Request\Details;


interface PaymentDetails
{
    /**
     * @return array - an array of fields to serialize
     */
    public function jsonSerialize();
}