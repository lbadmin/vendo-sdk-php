<?php
namespace VendoSdk\S2S\Response;

use VendoSdk\Exception;
use VendoSdk\S2S\Response\Details\CreditCardPaymentResult;
use VendoSdk\S2S\Response\Details\ExternalReferences;
use VendoSdk\S2S\Response\Details\ResultDetails;
use VendoSdk\S2S\Response\Details\SepaPaymentResult;
use VendoSdk\S2S\Response\Details\Transaction;
use VendoSdk\Vendo;
use \VendoSdk\S2S\Response\Details\SubscriptionSchedule;

class PaymentResponse
{
    /** @var mixed */
    protected $status;

    /** @var ?int */
    protected $errorCode;
    /** @var ?string */
    protected $errorMessage;
    /** @var ?string */
    protected $errorBankStatus;

    /** @var ?string */
    protected $requestId;

    /** @var ?ExternalReferences */
    protected $externalReferences;

    /** @var ?Transaction */
    protected $transactionDetails;

    /** @var ?string */
    protected $paymentType;

    /** @var ?SepaPaymentResult */
    protected $sepaPaymentResult;

    /** @var ?CreditCardPaymentResult */
    protected $creditCardPaymentResult;

    /** @var ?string */
    protected $paymentToken;

    /** @var ?ResultDetails */
    protected $resultDetails;

    /** @var SubscriptionSchedule */
    protected $subscriptionSchedule;

    /** @var string|null */
    protected $rawResponse;


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

        $this->setRawResponse($rawJsonResponse);

        $this->setStatus($responseArray['status'] ?? Vendo::S2S_STATUS_NOT_OK);

        $this->requestId = $responseArray['request_id'] ?? null;

        if (!empty($responseArray['external_references'])) {
            $this->setExternalReferences(new ExternalReferences($responseArray['external_references']));
        }

        if (!empty($responseArray['transaction'])) {
            $this->setTransactionDetails(new Transaction($responseArray['transaction']));
        }

        if (!empty($responseArray['card_details'])) {
            $this->setPaymentType(Vendo::PAYMENT_TYPE_CREDIT_CARD);
            $this->setCreditCardPaymentResult(new CreditCardPaymentResult($responseArray['card_details']));
        }

        if (!empty($responseArray['sepa_details'])) {
            $this->setPaymentType(Vendo::PAYMENT_TYPE_SEPA);
            $this->setSepaPaymentResult(new SepaPaymentResult($responseArray['sepa_details']));
        }

        if (!empty($responseArray['payment_details_token'])) {
            $this->setPaymentToken($responseArray['payment_details_token']);
        }

        if ($this->status == Vendo::S2S_STATUS_NOT_OK) {
            $this->setErrorCode($responseArray['error']['code'] ?? null);
            $this->setErrorMessage($responseArray['error']['message'] ?? '-unknown-');
            $this->setErrorBankStatus($responseArray['error']['processor_status'] ?? null);
        }

        if (!empty($responseArray['subscription_schedule'])) {
            $this->setSubscriptionSchedule(new SubscriptionSchedule($responseArray['subscription_schedule']));
        }

        if (!empty($responseArray['result'])) {
            $this->setResultDetails(new ResultDetails($responseArray['result']));
        }

    }

    /**
     * Returns the transaction status. Potential values are:
     * Vendo::S2S_STATUS_NOT_OK - The transaction failed. Use getErrorCode and getErrorMessage for more details.
     * Vendo::S2S_STATUS_OK - The transaction was accepted. Inspect the available methods to get all available details.
     * Vendo::S2S_STATUS_VERIFICATION_REQUIRED - You must redirect the user to getResultDetails->getVerificationUrl()
     * or custom status from custom API
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
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
    public function getErrorBankStatus(): ?string
    {
        return $this->errorBankStatus;
    }

    /**
     * @param string|null $errorBankStatus
     */
    public function setErrorBankStatus(?string $errorBankStatus): void
    {
        $this->errorBankStatus = $errorBankStatus;
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
     * @return ExternalReferences|null
     */
    public function getExternalReferences(): ?ExternalReferences
    {
        return $this->externalReferences;
    }

    /**
     * @param ExternalReferences|null $externalReferences
     */
    public function setExternalReferences(?ExternalReferences $externalReferences): void
    {
        $this->externalReferences = $externalReferences;
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

    /**
     * @return string|null
     */
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    /**
     * @param string|null $paymentType
     */
    public function setPaymentType(?string $paymentType): void
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return SepaPaymentResult|null
     */
    public function getSepaPaymentResult(): ?SepaPaymentResult
    {
        return $this->sepaPaymentResult;
    }

    /**
     * @param SepaPaymentResult|null $sepaPaymentResult
     */
    public function setSepaPaymentResult(?SepaPaymentResult $sepaPaymentResult): void
    {
        $this->sepaPaymentResult = $sepaPaymentResult;
    }

    /**
     * @return CreditCardPaymentResult|null
     */
    public function getCreditCardPaymentResult(): ?CreditCardPaymentResult
    {
        return $this->creditCardPaymentResult;
    }

    /**
     * @param CreditCardPaymentResult|null $creditCardPaymentResult
     */
    public function setCreditCardPaymentResult(?CreditCardPaymentResult $creditCardPaymentResult): void
    {
        $this->creditCardPaymentResult = $creditCardPaymentResult;
    }

    /**
     * @return string|null
     */
    public function getPaymentToken(): ?string
    {
        return $this->paymentToken;
    }

    /**
     * @param string|null $paymentToken
     */
    public function setPaymentToken(?string $paymentToken): void
    {
        $this->paymentToken = $paymentToken;
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
     * @return SubscriptionSchedule|null
     */
    public function getSubscriptionSchedule()
    {
        return $this->subscriptionSchedule;
    }

    public function setSubscriptionSchedule(SubscriptionSchedule $subscriptionSchedule): void
    {
        $this->subscriptionSchedule = $subscriptionSchedule;
    }

    /**
     * @param string $response
     * @return void
     */
    public function setRawResponse(string $response): void
    {
        $this->rawResponse = $response;
    }

    /**
     * @return string|null
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

}
