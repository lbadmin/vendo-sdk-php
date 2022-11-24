<?php

namespace VendoSdk\S2S\Request\Details\PaymentMethod;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;

class Sepa implements PaymentDetails, \JsonSerializable
{
    /** @var string */
    protected $iban;

    /** @var ?string */
    protected $bicSwift;

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    /**
     * @return ?string
     */
    public function getBicSwift(): ?string
    {
        return $this->bicSwift;
    }

    /**
     * @param string $bicSwift
     */
    public function setBicSwift(string $bicSwift): void
    {
        $this->bicSwift = $bicSwift;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->iban)) {
            throw new Exception('You must set the field iban in ' . get_class($this));
        }

        return [
            'payment_method' => 'sepa',
            'iban' => $this->getIban(),
            'bic_swift' => $this->getBicSwift(),
        ];
    }
}
