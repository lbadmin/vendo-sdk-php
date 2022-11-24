<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class Sepa implements \JsonSerializable
{
    /** @var string */
    protected $iban;
    /** @var string */
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
     * @return string
     */
    public function getBicSwift(): string
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
        if (empty($this->bicSwift)) {
            throw new Exception('You must set the field bicSwift in ' . get_class($this));
        }

        return [
            'iban' => $this->iban,
            'bic_swift' => $this->bicSwift,
        ];
    }
}
