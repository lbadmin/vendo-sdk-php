<?php
namespace VendoSdk\S2S\Response;

use VendoSdk\Exception;
use VendoSdk\S2S\Response\Details\Transaction;
use VendoSdk\Vendo;

class RefundResponse
{
    /** @var int */
    protected $status;
    /** @var ?string */
    protected $requestId;

    /** @var ?int */
    protected $errorCode;
    /** @var ?string */
    protected $errorMessage;

    /** @var ?Transaction */
    protected $transactionDetails;

    /**
     * @param string $rawJsonResponse
     * @throws Exception
     * @throws \Exception
     */
    public function __construct(string $rawJsonResponse)
    {
        $responseArray = json_decode($rawJsonResponse, true);
        if (empty($responseArray)) {
            throw new Exception('The response from Vendo\'s API cannot be decoded');
        }

        $this->setStatus($responseArray['status']);
        $this->setRequestId($responseArray['request_id'] ?? null);

        if (!empty($responseArray['transaction_id'])) {
            $this->setTransactionDetails(new Transaction(['id' => $responseArray['transaction_id']]));
        }

        if ($responseArray['status'] == Vendo::S2S_STATUS_NOT_OK) {
            $this->setErrorCode($responseArray['error']['code'] ?? null);
            $this->setErrorMessage($responseArray['error']['message'] ?? '-unknown-');
        }
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
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * @param string|null $requestId
     */
    public function setRequestId(?string $requestId): void
    {
        $this->requestId = $requestId;
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
     * @return Transaction|null
     */
    public function getTransactionDetails(): ?Transaction
    {
        return $this->transactionDetails;
    }

    /**
     * @param Transaction|null $transactionDetails
     */
    public function setTransactionDetails(?Transaction $transactionDetails): void
    {
        $this->transactionDetails = $transactionDetails;
    }
}
