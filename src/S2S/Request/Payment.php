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
 * Class Payment
 */
class Payment extends AbstractApiBase
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
        return parent::getApiEndpoint() . '/payment';
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
     * @return bool
     */
    public function isPreAuthOnly(): bool
    {
        return $this->preAuthOnly;
    }

    /**
     * Set this flag to true when you do not want to capture the transaction amount immediately but only validate the
     * payment details and block (reserve) the amount.
     * The capture of a preauth-only transaction can be performed with the CapturePayment class.
     *
     * Should be used only with payment methods that support pre-auth e.g. CreditCard.
     *
     * @param bool $preAuthOnly
     */
    public function setPreAuthOnly(bool $preAuthOnly): void
    {
        $this->preAuthOnly = $preAuthOnly;
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
        foreach ($items as $item) {
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
     * @return bool
     */
    public function isNonRecurring(): bool
    {
        return $this->isNonRecurring;
    }

    /**
     * @param bool $isNonRecurring
     */
    public function setIsNonRecurring(bool $isNonRecurring): void
    {
        $this->isNonRecurring = $isNonRecurring;
    }

    /**
     * @return ?SubscriptionSchedule
     */
    public function getSubscriptionSchedule(): ?SubscriptionSchedule
    {
        return $this->subscriptionSchedule;
    }

    /**
     * @param SubscriptionSchedule $subscriptionSchedule
     */
    public function setSubscriptionSchedule(SubscriptionSchedule $subscriptionSchedule): void
    {
        $this->subscriptionSchedule = $subscriptionSchedule;
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
     * @return string|null
     */
    public function getSuccessUrl()
    {
        return $this->successUrl ?? null;
    }

    /**
     * @param string $successUrl
     */
    public function setSuccessUrl(string $successUrl): void
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @return ?CrossSale
     */
    public function getCrossSale(): ?CrossSale
    {
        return $this->crossSale;
    }

    /**
     * @param ?CrossSale $crossSale
     */
    public function setCrossSale(?CrossSale $crossSale): void
    {
        $this->crossSale = $crossSale;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $result = array_merge(parent::jsonSerialize(), [
            'site_id' => $this->getSiteId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'external_references' => $this->getExternalReferences()->jsonSerialize(),
            'items' => $this->getItems(),
            'payment_details' => $this->getPaymentDetails()->jsonSerialize(),
            'customer_details' => $this->getCustomerDetails() ? $this->getCustomerDetails()->jsonSerialize() : null,
            'shipping_address' => $this->getShippingAddress()->jsonSerialize(),
            'request_details' => $this->getRequestDetails()->jsonSerialize(),
            'subscription_schedule' => $this->getSubscriptionSchedule() ? $this->getSubscriptionSchedule()->jsonSerialize() : null,
            'mit' => $this->isMerchantInitiatedTransaction(),
            'preauth_only' => $this->isPreAuthOnly(),
            'non_recurring' => $this->isNonRecurring(),
            'success_url' => $this->getSuccessUrl(),
        ]);

        if (!empty($this->getCrossSale())) {
            $result['cross_sale'] = $this->getCrossSale();
        }

        return $result;
    }
}
