<?php
namespace VendoSdk\Gateway;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use VendoSdk\Exception;
use VendoSdk\Gateway\Request\Details\Customer;
use VendoSdk\Gateway\Request\Details\ExternalReferences;
use VendoSdk\Gateway\Request\Details\Item;
use VendoSdk\Gateway\Request\Details\PaymentDetails;
use VendoSdk\Gateway\Request\Details\Request;
use VendoSdk\Gateway\Request\Details\ShippingAddress;
use VendoSdk\Gateway\Response\PaymentResponse;
use VendoSdk\Util\HttpClientTrait;
use VendoSdk\Vendo;

abstract class PaymentBase
{
    use HttpClientTrait;

    /**
     * Raw API Response
     * @var ?string
     */
    protected $rawResponse;

    /** Fields that will be serialized in the json request */
    /** @var int */
    protected $siteId;
    /** @var float */
    protected $amount;
    /** @var string Any value of Vendo::CURRENCY_* */
    protected $currency;
    /** @var bool */
    protected $isMerchantInitiatedTransaction;
    /** @var string */
    protected $apiSecret;
    /** @var int */
    protected $merchantId;
    /** @var bool */
    protected $isTest;
    /** @var ExternalReferences */
    protected $externalReferences;
    /** @var Item[] */
    protected $items;
    /** @var ?Customer */
    protected $customerDetails;
    /** @var PaymentDetails */
    protected $paymentDetails;
    /** @var ShippingAddress */
    protected $shippingAddress;
    /** @var Request */
    protected $requestDetails;

    public function __construct()
    {
        $this->items = [];
        $this->setIsMerchantInitiatedTransaction(false);
        $this->setIsTest(false);
    }

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
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     */
    public function setSiteId(int $siteId): void
    {
        $this->siteId = $siteId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set the currency for the request
     * You can use the constants set in \VendoSdk\Vendo::CURRENCY_*
     * Valid values: USD, EUR, GBP, JPY.
     *
     * @param string $currency
     * @throws Exception
     */
    public function setCurrency(string $currency): void
    {
        $validCurrencies = [
            Vendo::CURRENCY_USD,
            Vendo::CURRENCY_EUR,
            Vendo::CURRENCY_GBP,
            Vendo::CURRENCY_JPY,
        ];
        if (!in_array($currency, $validCurrencies)) {
            throw new Exception('The currency ' . $currency . ' is not valid.');
        }

        $this->currency = $currency;
    }

    /**
     * @return bool
     */
    public function isMerchantInitiatedTransaction(): bool
    {
        return $this->isMerchantInitiatedTransaction;
    }

    /**
     * @param bool $isMerchantInitiatedTransaction
     */
    public function setIsMerchantInitiatedTransaction(bool $isMerchantInitiatedTransaction): void
    {
        $this->isMerchantInitiatedTransaction = $isMerchantInitiatedTransaction;
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
     * @return ExternalReferences
     */
    public function getExternalReferences(): ExternalReferences
    {
        return $this->externalReferences;
    }

    /**
     * @param ExternalReferences $externalReferences
     */
    public function setExternalReferences(ExternalReferences $externalReferences): void
    {
        $this->externalReferences = $externalReferences;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @throws Exception
     */
    public function setItems(array $items): void
    {
        foreach($items as $item) {
            if (!($item instanceof Item)) {
                throw new Exception('Items must contain instances of VendoSdk\\Gateway\\Details\\Item');
            }
        }

        $this->items = $items;
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return ?Customer
     */
    public function getCustomerDetails(): ?Customer
    {
        return $this->customerDetails;
    }

    /**
     * @param ?Customer $customerDetails
     */
    public function setCustomerDetails(?Customer $customerDetails): void
    {
        $this->customerDetails = $customerDetails;
    }

    /**
     * @return ShippingAddress
     */
    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @param ShippingAddress $shippingAddress
     */
    public function setShippingAddress(ShippingAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return Request
     */
    public function getRequestDetails(): Request
    {
        return $this->requestDetails;
    }

    /**
     * @param Request $requestDetails
     */
    public function setRequestDetails(Request $requestDetails): void
    {
        $this->requestDetails = $requestDetails;
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
     * @return PaymentDetails
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public function setPaymentDetails(PaymentDetails $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
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
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'external_references' => $this->getExternalReferences(),
            'items' => $this->getItems(),
            'customer_details' => $this->getCustomerDetails(),
            'shipping_address' => $this->getShippingAddress(),
            'request_details' => $this->getRequestDetails(),
            'payment_details' => $this->getPaymentDetails(),
            'mit' => $this->isMerchantInitiatedTransaction(),
        ];
    }


}