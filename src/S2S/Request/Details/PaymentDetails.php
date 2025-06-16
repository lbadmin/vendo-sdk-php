<?php

namespace VendoSdk\S2S\Request\Details;


interface PaymentDetails
{
    /**
     * @return mixed - an array of fields to serialize
     */
    public function jsonSerialize();
}