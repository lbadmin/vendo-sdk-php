<?php
namespace VendoSdk\Reporting;

use GuzzleHttp\Exception\GuzzleException;
use VendoSdk\Exception;
use VendoSdk\Reporting\Response\RowElement;
use VendoSdk\Reporting\Response\SubscriptionStatus;
use VendoSdk\Url\Base;
use VendoSdk\Util\HttpClientTrait;

class Subscription extends Base
{
    use HttpClientTrait;

    /** @var ?string */
    protected $rawResponse;

    /**
     * @inheritdoc
     */
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/api/subscription-status';
    }

    public function setSubscriptionId(int $subscriptionId): void
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Returns the status of the subscription
     *
     * @return ?RowElement
     * @throws Exception
     */
    public function getDetails()
    {
        return $this->getStatus();
    }

    /**
     * Queries Vendo's Subscription Status  API. Returns true if the request was successful.
     *
     * @return bool
     * @throws GuzzleException
     * @throws \Exception
     */
    public function sendGetRequest(): bool
    {
        $url = $this->getSignedUrl();
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest('GET', $url);

        $response = $client->send($request);
        $this->rawResponse = $response->getBody();

        return true;
    }

    /**
     * @return string|null
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * @inheritdoc
     */
    protected function setAllowedUrlParameters(): void {
        $this->allowedUrlParams = [
            'subscriptionId',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUrlParametersValidators(): void
    {
        $this->urlParamValidators['subscriptionId'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception(\sprintf('Invalid subscription ID: %s', $value));
            }
        };
    }

    /**
     * @return ?SubscriptionStatus
     * @throws Exception
     */
    protected function getStatus(): ?SubscriptionStatus
    {
        $rawResponse = $this->getRawResponse();

        if (empty($rawResponse)) {
            throw new Exception('Your must call $status->sendGetRequest() first.');
        }

        $data = json_decode($rawResponse, true);

        $subscriptionId = ($data['request']['subscription_id'] ?? null);
        $code = $data['result']['subscription_status_code'] ?? null;
        $message = $data['result']['subscription_status_message'] ?? null;

        if(empty($code) || empty($message)){
            throw new Exception(\sprintf('Missing subscription status data: code or message'));
        }

        return new SubscriptionStatus($subscriptionId, $code, $message);
    }
}