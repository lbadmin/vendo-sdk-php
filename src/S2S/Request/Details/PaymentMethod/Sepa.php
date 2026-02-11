<?php

namespace VendoSdk\S2S\Request\Details\PaymentMethod;

use VendoSdk\S2S\Request\Details\PaymentDetails;

class Sepa implements PaymentDetails, \JsonSerializable
{
    /** @var string|null */
    protected $iban;

    /** @var ?string */
    protected $bicSwift;

    /**
     * @return string|null
     */
    public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @param string|null $iban
     */
    public function setIban(?string $iban): void
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
     * @param string|null $bicSwift
     */
    public function setBicSwift(?string $bicSwift): void
    {
        $this->bicSwift = $bicSwift;
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'payment_method' => 'sepa',
        ];

        if ($this->iban !== null && $this->iban !== '') {
            $data['iban'] = $this->getIban();
        }

        if ($this->bicSwift !== null) {
            $data['bic_swift'] = $this->getBicSwift();
        }

        return $data;
    }
}
