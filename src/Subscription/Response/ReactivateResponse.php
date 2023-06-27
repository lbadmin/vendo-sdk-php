<?php
namespace VendoSdk\Subscription\Response;

use VendoSdk\Reporting\Response\Parser;

class ReactivateResponse
{
    /** @var int */
    protected $subscriptionId;

    /** @var int */
    protected $merchantId;

    /** @var string */
    protected $responseCode;

    /** @var string */
    protected $responseMessage;

    /**
     * ReactivateResponse constructor.
     * @param Parser $xml
     */
    public function __construct(Parser $xml)
    {
        $this->merchantId = (int)$xml->merchantId;
        $this->subscriptionId = (int)$xml->subscriptionId;
        $this->responseCode = (string)$xml->response['code'];
        $this->responseMessage = \trim((string)$xml->response);
    }

    /**
     * @return int
     */
    public function getSubscriptionId(): int
    {
        return $this->subscriptionId;
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getResponseCode(): string
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

}