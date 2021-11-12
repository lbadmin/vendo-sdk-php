<?php
namespace VendoSdk\Subscription;

use VendoSdk\Exception;
use VendoSdk\Subscription\Response\RefundResponse;
use VendoSdk\Util\HttpClientTrait;
use GuzzleHttp\Exception\GuzzleException;
use VendoSdk\Reporting\Response\Parser;
use VendoSdk\Url\Base;

/**
 * Queries Vendo's Subscription API.
 *
 * @package VendoSdk\Reporting
 */
class RefundSubscription extends Base
{
    use HttpClientTrait;

    const ACTION_REFUND_ONLY = 0;
    const ACTION_REFUND_AND_CANCEL = 1;
    const ACTION_REFUND_AND_BLACKLIST = 2;

    const ACTION_TYPES = [
        self::ACTION_REFUND_ONLY,
        self::ACTION_REFUND_AND_CANCEL,
        self::ACTION_REFUND_AND_BLACKLIST,
    ];

    /** @var string */
    protected $rawResponse;

    public function setMerchantId(int $vendoMerchantId): void
    {
        $this->merchantId = $vendoMerchantId;
    }

    public function setTransactionId(int $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function setActionType(int $actionType): void
    {
        if(!in_array($actionType, self::ACTION_TYPES)){
            throw new Exception(\sprintf('Illegal action type: %s', $actionType));
        }

        $this->actionType = $actionType;
    }

    public function setPartialAmount(float $partialAmount): void
    {
        $this->partial_amount = $partialAmount;
    }

    public function setRefundReasonId(int $refundReasonId): void
    {
        $this->refund_reason_id = $refundReasonId;
    }

    /**
     * @inheritdoc
     */
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . '/api/refund';
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
     * @return RefundResponse
     * @throws Exception
     * @throws \Exception
     */
    public function getResponse()
    {
        $rawResponse = $this->getRawResponse();
        if (empty($rawResponse)) {
            throw new Exception('Your must call $cancelSubscription->postRequest() first.');
        }

        return new RefundResponse(new Parser($rawResponse));
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
     * @return RefundResponse
     */
    public function postRequest(): RefundResponse
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
            'transactionId',
            'actionType',
            'partial_amount',
            'refund_reason_id',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUrlParametersValidators(): void {
        $this->urlParamValidators['transactionId'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This Transaction ID is invalid: " . $value);
            }
        };

        $this->urlParamValidators['merchantId'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This merchant ID is invalid: " . $value);
            }
        };

        $this->urlParamValidators['actionType'] = function ($value) {
            if (!in_array($value, self::ACTION_TYPES)) {
                throw new Exception("This action ID is invalid: " . $value);
            }
        };

        $this->urlParamValidators['partial_amount'] = function ($value) {
            if ($value < 0.0) {
                throw new Exception("Partial refund must be >0.0, is: " . $value);
            }
        };

        $this->urlParamValidators['refund_reason_id'] = function ($value) {
            if (!is_numeric($value)) {
                throw new Exception("This reason ID is invalid: " . $value);
            }
        };
    }
}