<?php
namespace VendoSdk\S2S\Request\Details\PaymentMethod;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentDetails;

class GooglePay implements PaymentDetails, \JsonSerializable
{
    /** @var string */
    protected $token;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token The JSON stringified Google Pay token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if (empty($this->token)) {
            throw new Exception('You must set the token field in ' . get_class($this));
        }

        return [
            'payment_method' => 'wallet',
            'provider' => 'googlepay',
            'data' => [
                'token' => $this->token,
            ],
        ];
    }
}

