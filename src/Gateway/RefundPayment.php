<?php
namespace VendoSdk\Gateway;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use VendoSdk\Exception;
use VendoSdk\Gateway\Response\RefundResponse;
use VendoSdk\Util\HttpClientTrait;
use VendoSdk\Util\SignatureTrait;
use VendoSdk\Vendo;

class RefundPayment
{
    use SignatureTrait, HttpClientTrait;

    /** @var int */
    protected $merchantId;
    /** @var int */
    protected $transactionId;
    /** @var ?float */
    protected $partialAmount;

    /** @var string */
    protected $rawRequest;
    /** @var ?string */
    protected $rawResponse;

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    /**
     * @param int $merchantId
     */
    public function setMerchantId(int $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    /**
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
     * Returns the API endpoint for this operation
     *
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return Vendo::BASE_URL . '/api/refund?';
    }

    /**
     * Post the request to Vendo's Gateway API
     *
     * @return RefundResponse
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postRequest(): RefundResponse
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest('GET', $this->getRawRequest());

        try {
            $response = $client->send($request);
            $this->rawResponse = $response->getBody();
        } catch (ClientException $e) {
            $this->rawResponse = $e->getResponse()->getBody();
        } catch (ServerException $serverException) {
            throw new Exception(
                'A server exception occurred. If this persists then contact Vendo Client Support',
                $serverException->getCode(),
                $serverException
            );
        }

        return $this->getResponse();
    }

    /**
     * Serializes the request into an URL
     *
     * @return string
     */
    public function getRawRequest(): string
    {
        $this->rawRequest = $this->getApiEndpoint();
        $params = [
            'merchantId' => $this->getMerchantId(),
            'transactionId' => $this->getTransactionId(),
            'actionType' => 0,
            'partial_amount' => $this->getPartialAmount(),
        ];
        $this->rawRequest .= http_build_query($params);
        return $this->signUrl($this->rawRequest);
    }

    /**
     * Return the raw response (if any) received from Vendo's API.
     *
     * @return ?string
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * @return RefundResponse
     * @throws Exception
     */
    public function getResponse(): RefundResponse
    {
        return new RefundResponse($this->rawResponse);
    }
}
