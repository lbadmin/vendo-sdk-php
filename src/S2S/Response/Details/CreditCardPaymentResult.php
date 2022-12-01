<?php
namespace VendoSdk\S2S\Response\Details;

class CreditCardPaymentResult
{
    /** @var ?string */
    protected $authCode;

    /**
     * @param array $creditCardResult
     */
    public function __construct(array $creditCardResult)
    {
        $this->setAuthCode($creditCardResult['auth_code'] ?? null);
    }

    /**
     * @return string|null
     */
    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    /**
     * @param string|null $authCode
     */
    public function setAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;
    }
}