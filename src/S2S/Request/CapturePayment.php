<?php
namespace VendoSdk\S2S\Request;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use VendoSdk\Exception;
use VendoSdk\S2S\Response\CaptureResponse;
use VendoSdk\Util\HttpClientTrait;
use VendoSdk\Vendo;

class CapturePayment implements \JsonSerializable
{
    use HttpClientTrait;

    /**
     * Raw API Response
     * @var ?string
     */
    protected $rawResponse;

    /** @var string */
    protected $apiSecret;
    /** @var bool */
    protected $isTest;
    /** @var int */
    protected $merchantId;
    /** @var int */
    protected $transactionId;

    public function __construct()
    {
        $this->setIsTest(false);
    }

    public function getApiEndpoint(): string
    {
        return Vendo::BASE_URL . '/api/gateway/capture';
    }

    /**
     * @return string
     */
    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    /**
     * @param string $apiSecret
     */
    public function setApiSecret(string $apiSecret): void
    {
        $this->apiSecret = $apiSecret;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }

    /**
     * @param bool $isTest
     */
    public function setIsTest(bool $isTest): void
    {
        $this->isTest = $isTest;
    }

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
     * The Vendo Transaction ID that you want to capture.
     *
     * @param int $transactionId
     */
    public function setTransactionId(int $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Post the request to Vendo's Gateway API
     *
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postRequest(): CaptureResponse
    {
        $client = $this->getHttpClient();
        $body = $this->getRawRequest();
        $request = $this->getHttpRequest('POST', $this->getApiEndpoint(), [], $body);

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
     * Serializes the request into a json string
     *
     * @param bool $jsonPrettyPrint
     * @return string|null
     */
    public function getRawRequest(bool $jsonPrettyPrint = false): ?string
    {
        $flags = JSON_OBJECT_AS_ARRAY;
        if ($jsonPrettyPrint) {
            $flags |= JSON_PRETTY_PRINT;
        }
        return json_encode($this, $flags);
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
     * @return CaptureResponse
     * @throws Exception
     */
    public function getResponse(): CaptureResponse
    {
        return new CaptureResponse($this->rawResponse);
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'api_secret' => $this->getApiSecret(),
            'merchant_id' => $this->getMerchantId(),
            'is_test' => (int)$this->isTest(),
            'transaction_id' => $this->getTransactionId(),
        ];
    }
}
