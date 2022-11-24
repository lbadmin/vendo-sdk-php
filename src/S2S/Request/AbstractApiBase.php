<?php

namespace VendoSdk\S2S\Request;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\Customer;
use VendoSdk\S2S\Request\Details\ExternalReferences;
use VendoSdk\S2S\Request\Details\Item;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\ShippingAddress;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;
use VendoSdk\S2S\Response\PaymentResponse;
use VendoSdk\Util\HttpClientTrait;
use VendoSdk\Vendo;

abstract class AbstractApiBase
{
    use HttpClientTrait;

    /**
     * Raw API Response
     *
     * @var ?string
     */
    protected $rawResponse;

    /** @var string */
    protected $apiSecret;

    /** @var int */
    protected $isTest = 0;

    /** @var int */
    protected $merchantId;

    /**
     * Returns the API endpoint for this operation
     *
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return Vendo::BASE_URL . '/api/gateway';
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
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }

    /**
     * @param bool $isTest
     */
    public function setIsTest(bool $isTest = true): void
    {
        $this->isTest = $isTest;
    }

    /**
     * Post the request to Vendo's Gateway API
     *
     * @return PaymentResponse
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postRequest(): PaymentResponse
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
     * @return PaymentResponse
     * @throws Exception
     */
    public function getResponse(): PaymentResponse
    {
        return new PaymentResponse($this->rawResponse);
    }

    /**
     * @return array
     */
    protected function getBaseFields(): array
    {
        return [
            'api_secret' => $this->getApiSecret(),
            'is_test' => (int)$this->isTest(),
            'merchant_id' => $this->getMerchantId(),
        ];
    }
}