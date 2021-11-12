<?php
namespace VendoSdk\Subscription\Response;

use VendoSdk\Reporting\Response\Parser;

class RefundResponse
{
    /** @var int */
    protected $transactionId;

    /** @var int */
    protected $merchantId;

    /** @var int */
    protected $actionType;

    /** @var string */
    protected $responseCode;

    /** @var string */
    protected $responseMessage;

    /**
     * CancelResponse constructor.
     * @param Parser $xml
     */
    public function __construct(Parser $xml)
    {
        $this->merchantId = (int)$xml->merchantId;
        $this->transactionId = (int)$xml->transactionId;
        $this->actionType = (int)$xml->actionType;
        $this->responseCode = (string)$xml->response['code'];
        $this->responseMessage = \trim((string)$xml->response);
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transactionId;
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

    /**
     * @return int
     */
    public function getActionType(): int
    {
        return $this->actionType;
    }
}
