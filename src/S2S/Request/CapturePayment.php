<?php
namespace VendoSdk\S2S\Request;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use VendoSdk\Exception;
use VendoSdk\S2S\Response\CaptureResponse;
use VendoSdk\Util\HttpClientTrait;
use VendoSdk\Vendo;

class CapturePayment extends AbstractApiBase
{
    /** @var int */
    protected $transactionId;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/capture';
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
     * @return CaptureResponse
     * @throws Exception
     */
    public function getResponse()
    {
        return new CaptureResponse($this->rawResponse);
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'transaction_id' => $this->getTransactionId(),
        ]);
    }
}
