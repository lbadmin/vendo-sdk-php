<?php
namespace VendoSdk\S2S\Response\Details;

class ResultDetails
{
    /** @var ?string */
    protected $code;
    /** @var ?string */
    protected $message;
    /** @var ?string */
    protected $verificationUrl;

    /** @var ?int */
    protected $verificationId;

    /**
     * @param array $resultDetails
     */
    public function __construct(array $resultDetails)
    {
        $this->setCode($resultDetails['code'] ?? null);
        $this->setMessage($resultDetails['message'] ?? null);
        $this->setVerificationUrl($resultDetails['verification_url'] ?? null);
        $this->setVerificationId($resultDetails['verification_id'] ?? null);
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string|null
     */
    public function getVerificationUrl(): ?string
    {
        return $this->verificationUrl;
    }

    /**
     * @param string|null $verificationUrl
     */
    public function setVerificationUrl(?string $verificationUrl): void
    {
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * @return int|null
     */
    public function getVerificationId(): ?int
    {
        return $this->verificationId;
    }

    /**
     * @param int|null $verificationId
     */
    public function setVerificationId(?int $verificationId): void
    {
        $this->verificationId = $verificationId;
    }
}
