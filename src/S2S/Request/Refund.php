<?php

namespace VendoSdk\S2S\Request;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use VendoSdk\Exception;
use VendoSdk\S2S\Response\RefundResponse;
use VendoSdk\Util\HttpClientTrait;
use VendoSdk\Vendo;

class Refund extends AbstractApiBase
{
    /** @var int */
    protected $transactionId;

    /** @var ?float */
    protected $partialAmount;

    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/refund';
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    /**
     * The Vendo Transaction ID that you want to capture.
     *
     * @param int $transactionId
     */
    public function setTransactionId(int $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return float|null
     */
    public function getPartialAmount(): ?float
    {
        return $this->partialAmount;
    }

    /**
     * @param float|null $partialAmount
     */
    public function setPartialAmount(?float $partialAmount): void
    {
        $this->partialAmount = $partialAmount;
    }

    /**
     * @return RefundResponse
     * @throws Exception
     */
    public function getResponse()
    {
        return new RefundResponse($this->rawResponse);
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'transaction_id' => $this->getTransactionId(),
            'partial_amount' => $this->getPartialAmount(),
        ]);
    }
}
