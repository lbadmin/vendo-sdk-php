<?php
namespace VendoSdk\Subscription;

use VendoSdk\Exception;
use VendoSdk\Subscription\Response\ReactivateResponse;
use VendoSdk\Util\HttpClientTrait;
use GuzzleHttp\Exception\GuzzleException;
use VendoSdk\Reporting\Response\Parser;
use VendoSdk\Url\Base;

class ReactivateSubscription extends Base
{
    use HttpClientTrait;

    /** @var string */
    protected $rawResponse;

    /**
     * @inheritdoc
     */
    public function __construct(string $sharedSecret)
    {
        parent::__construct($sharedSecret);
        $this->rawResponse = null;
    }

    /**
     * @param int $vendoMerchantId
     * @return void
     */
    public function setMerchantId(int $vendoMerchantId): void
    {
        $this->merchantId = $vendoMerchantId;
    }

    /**
     * @param int $subscriptionId
     * @return void
     */
    public function setSubscriptionId(int $subscriptionId): void
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @inheritdoc
     */
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/api/reactivate';
    }


    /**
     * Returns the parsed response
     * Throws \VendoSdk\Exception if the API returned an error.
     *
     * @return ReactivateResponse
     * @throws Exception
     * @throws \Exception
     */
    public function getResponse()
    {
        $rawResponse = $this->getRawResponse();
        if (empty($rawResponse)) {
            throw new Exception('Your must call $reactivateSubscription->getRequest() first.');
        }

        return new ReactivateResponse(new Parser($rawResponse));
    }

    /**
     * Returns the raw response that was returned by Vendo
     * @return ?string
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * Queries Vendo's Reactivate Subscription API.
     *
     * @return bool
     * @throws GuzzleException
     * @throws \Exception
     * @return ReactivateResponse
     */
    public function getRequest(): ReactivateResponse
    {
        $url = $this->getSignedUrl();
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest('GET', $url);

        $response = $client->send($request);
        $this->rawResponse = $response->getBody();

        return $this->getResponse();
    }

    /**
     * @inheritdoc
     */
    protected function setAllowedUrlParameters(): void {
        $this->allowedUrlParams = [
            'merchantId',
            'subscriptionId',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUrlParametersValidators(): void {
        $this->urlParamValidators['subscriptionId'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This Subscription ID is invalid: " . $value);
            }
        };
        $this->urlParamValidators['merchantId'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This merchant ID is invalid: " . $value);
            }
        };
    }

}