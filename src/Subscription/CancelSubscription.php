<?php
namespace VendoSdk\Subscription;

use VendoSdk\Exception;
use VendoSdk\Subscription\Response\CancelResponse;
use VendoSdk\Util\HttpClientTrait;
use GuzzleHttp\Exception\GuzzleException;
use VendoSdk\Reporting\Response\Parser;
use VendoSdk\Url\Base;

/**
 * Queries Vendo's Subscription API.
 *
 * @package VendoSdk\Reporting
 */
class CancelSubscription extends Base
{
    use HttpClientTrait;

    /** @var string */
    protected $rawResponse;

    public function setMerchantId(int $vendoMerchantId): void
    {
        $this->merchantId = $vendoMerchantId;
    }

    public function setSubscriptionId(int $subscriptionId): void
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @inheritdoc
     */
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/api/cancel';
    }

    /**
     * @inheritdoc
     */
    public function __construct(string $sharedSecret)
    {
        parent::__construct($sharedSecret);
        $this->rawResponse = null;
    }

    /**
     * Returns the parsed response
     * Throws \VendoSdk\Exception if the API returned an error.
     *
     * @return CancelSubscription
     * @throws Exception
     * @throws \Exception
     */
    public function getResponse()
    {
        $rawResponse = $this->getRawResponse();
        if (empty($rawResponse)) {
            throw new Exception('Your must call $cancelSubscription->postRequest() first.');
        }

        return new CancelResponse(new Parser($rawResponse));
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
     * Queries Vendo's Cancel Subscription API.
     *
     * @return bool
     * @throws GuzzleException
     * @throws \Exception
     * @return CancelResponse
     */
    public function postRequest(): CancelResponse
    {
        $url = $this->getSignedUrl();
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest('POST', $url);

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
        $this->urlParamValidators['startDate'] = function ($value) {
            if ((bool)preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value) == false ) {
                throw new Exception('This date is not valid: ' . $value);
            }
        };
        $this->urlParamValidators['endDate'] = $this->urlParamValidators['startDate'];
        $this->urlParamValidators['siteIDs'] = function ($value) {
            array_map(function($value) {
                if (!is_numeric($value)) {
                    throw new Exception("This Site ID is invalid: " . $value);
                }
            }, explode(',', $value));
        };
        $this->urlParamValidators['format'] = function ($value) {
            if ($value != \VendoSdk\Reporting\Reconciliation::FORMAT_CSV && $value != Reconciliation::FORMAT_XML) {
                throw new Exception("This format is invalid: " . $value);
            }
        };
        $this->urlParamValidators['merchantID'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This merchantID is invalid: " . $value);
            }
        };
    }
}