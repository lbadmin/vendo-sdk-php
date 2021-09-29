<?php
namespace VendoSdk\Gateway\Request\Details;

use VendoSdk\Exception;

class Token implements \JsonSerializable
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
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->token)) {
            throw new Exception('You must set the token field in ' . get_class($this));
        }

        return [
            'token' => $this->token,
        ];
    }
}
