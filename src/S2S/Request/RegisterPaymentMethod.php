<?php

namespace VendoSdk\S2S\Request;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\Customer;
use VendoSdk\S2S\Request\Details\ExternalReferences;
use VendoSdk\S2S\Request\Details\Item;
use VendoSdk\S2S\Request\Details\PaymentDetails;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\ShippingAddress;
use VendoSdk\S2S\Request\Details\SubscriptionSchedule;
use VendoSdk\S2S\Request\Details\CrossSale;
use VendoSdk\Vendo;

/**
 * Class RegisterPaymentMethod
 */
class RegisterPaymentMethod extends AbstractApiBase
{
    /** @var int */
    protected $siteId;

    /** @var float */
    protected $amount;

    /** @var string Any value of Vendo::CURRENCY_* */
    protected $currency;

    /** @var ExternalReferences */
    protected $externalReferences;

    /** @var Item[] */
    protected $items = [];

    /** @var PaymentDetails */
    protected $paymentDetails;

    /** @var ?Customer */
    protected $customerDetails;

    /** @var ShippingAddress */
    protected $shippingAddress;

    /** @var ClientRequest */
    protected $requestDetails;

    /** @var SubscriptionSchedule */
    protected $subscriptionSchedule;

    /** @var bool */
    protected $isMerchantInitiatedTransaction = false;

    /** @var bool */
    protected $isNonRecurring = false;

    /** @var bool */
    protected $preAuthOnly = false;

    /** @var string */
    protected $successUrl;

    /** @var ?CrossSale */
    protected $crossSale;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/register-payment-method';
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
     * @return ClientRequest
     */
    public function getRequestDetails(): ClientRequest
    {
        return $this->requestDetails;
    }

    /**
     * @param ClientRequest $requestDetails
     */
    public function setRequestDetails(ClientRequest $requestDetails): void
    {
        $this->requestDetails = $requestDetails;
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
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $result = array_merge(parent::jsonSerialize(), [
            'site_id' => $this->getSiteId(),
            'currency' => $this->getCurrency(),
            'external_references' => $this->getExternalReferences()->jsonSerialize(),
            'payment_details' => $this->getPaymentDetails()->jsonSerialize(),
            'customer_details' => $this->getCustomerDetails() ? $this->getCustomerDetails()->jsonSerialize() : null,
            'request_details' => $this->getRequestDetails()->jsonSerialize()
        ]);


        return $result;
    }
}
