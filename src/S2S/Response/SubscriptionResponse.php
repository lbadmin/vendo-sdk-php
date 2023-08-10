<?php

namespace VendoSdk\S2S\Response;

use VendoSdk\Exception;
use VendoSdk\S2S\Response\Details\ResultDetails;
use VendoSdk\S2S\Response\Details\Subscription;

class SubscriptionResponse
{
    /** @var int */
    protected $status;

    /** @var ?int */
    protected $errorCode;
    /** @var ?string */
    protected $errorMessage;

    /** @var ?string */
    protected $requestId;

    /** @var ?Subscription */
    protected $subscriptionDetails;

    /** @var ?ResultDetails */
    protected $resultDetails;

    /** @var ?string */
    protected $verificationUrl;

    /** @var ?int */
    protected $verificationId;

    /**
     * @param string $rawJsonResponse
     * @throws Exception
     * @throws \Exception
     */
    public function __construct(string $rawJsonResponse)
    {
        $this->errorCode = null;
        $this->errorMessage = null;

        $responseArray = json_decode($rawJsonResponse, true);

        if (empty($responseArray)) {
            throw new Exception('The response from Vendo\'s API cannot be decoded');
        }

        $this->status = $responseArray['status'];
        $this->requestId = $responseArray['request_id'] ?? null;

        if (!empty($responseArray['subscription'])) {
            $this->setSubscriptionDetails(new Subscription($responseArray['subscription']));
        }

        if (!empty($responseArray['error']['code'])) {
            $this->setErrorCode($responseArray['error']['code']);
        }

        if (!empty($responseArray['error']['message'])) {
            $this->setErrorMessage($responseArray['error']['message']);
        }

        if (!empty($responseArray['verification_id'])) {
            $this->setVerificationId($responseArray['verification_id']);
        }

        if (!empty($responseArray['verification_url'])) {
            $this->setVerificationUrl($responseArray['verification_url']);
        }
    }

    /**
     * Returns the request status. Potential values are:
     * Vendo::S2S_STATUS_NOT_OK - The transaction failed. Use getErrorCode and getErrorMessage for more details.
     * Vendo::S2S_VERIFICATION_REQUIRED
     * Vendo::S2S_STATUS_OK - The transaction was accepted. Inspect the available methods to get all available details.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * @param int|null $errorCode
     */
    public function setErrorCode($errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     */
    public function setErrorMessage($errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string|null
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string|null $requestId
     */
    public function setRequestId($requestId): void
    {
        $this->requestId = $requestId;
    }

    /**
     * @return Subscription
     */
    public function getSubscriptionDetails(): Subscription
    {
        return $this->subscriptionDetails;
    }

    /**
     * @param Subscription $subscriptionDetails
     */
    public function setSubscriptionDetails(Subscription $subscriptionDetails): void
    {
        $this->subscriptionDetails = $subscriptionDetails;
    }

    /**
     * @return ResultDetails|null
     */
    public function getResultDetails(): ?ResultDetails
    {
        return $this->resultDetails;
    }

    /**
     * @param ResultDetails|null $resultDetails
     */
    public function setResultDetails(?ResultDetails $resultDetails): void
    {
        $this->resultDetails = $resultDetails;
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
