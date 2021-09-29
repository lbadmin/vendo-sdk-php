<?php
namespace VendoSdk\Gateway\Response;

use VendoSdk\Exception;
use VendoSdk\Gateway\Response\Details\Transaction;
use VendoSdk\Vendo;

class RefundResponse
{
    const API_REFUND_TRANSACTION_SUCCESSFULLY_REFUNDED = '5907';

    /** @var int */
    protected $status;
    /** @var ?Transaction */
    protected $transaction;
    /** @var ?string */
    protected $errorCode;
    /** @var ?string */
    protected $errorMessage;

    /**
     * @param string $xmlResponse
     * @throws Exception
     * @throws \Exception
     */
    public function __construct(string $xmlResponse)
    {
        $response= simplexml_load_string($xmlResponse);
        if ($response == false) {
            throw new Exception('The refund response cannot be parsed.');
        }
        if (!empty($response->response['code'])
            && (string)$response->response['code'] == self::API_REFUND_TRANSACTION_SUCCESSFULLY_REFUNDED
        ) {
            $this->setStatus(Vendo::GATEWAY_STATUS_OK);
            $this->setTransaction(new Transaction(['id' => (int)$response->transactionId]));
        } else {
            $this->setStatus(Vendo::GATEWAY_STATUS_NOT_OK);
            $this->setErrorCode((string)$response->response['code']);
            $this->setErrorMessage((string)$response->response);
        }
    }

    /**
     * @return ?Transaction
     */
    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    /**
     * @param ?Transaction $transaction
     */
    public function setTransaction(?Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * @param string|null $errorCode
     */
    public function setErrorCode(?string $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     */
    public function setErrorMessage(?string $errorMessage): void
    {
        if (!empty($errorMessage)) {
            $errorMessage = trim($errorMessage);
        }
        $this->errorMessage = $errorMessage;
    }
}
