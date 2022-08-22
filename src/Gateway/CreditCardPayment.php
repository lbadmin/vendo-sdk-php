<?php
namespace VendoSdk\Gateway;

use VendoSdk\Exception;
use VendoSdk\Gateway\Request\Details\CreditCard;

/**
 * Class CreditCardPayment
 * @package VendoSdk\Gateway
 *
 * @deprecated please use VendoSdk\Gateway\Payment instead
 */
class CreditCardPayment extends PaymentBase implements \JsonSerializable
{
    /** @var CreditCard */
    protected $creditCardDetails;
    /** @var bool */
    protected $isPreAuth;

    public function __construct()
    {
        parent::__construct();
        $this->isPreAuth = false;
    }

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/payment';
    }

    /**
     * @return CreditCard
     */
    public function getCreditCardDetails(): CreditCard
    {
        return $this->creditCardDetails;
    }

    /**
     * @param CreditCard $creditCardDetails
     */
    public function setCreditCardDetails(CreditCard $creditCardDetails): void
    {
        $this->creditCardDetails = $creditCardDetails;
    }

    /**
     * @return bool
     */
    public function isPreAuth(): bool
    {
        return $this->isPreAuth;
    }

    /**
     * Set this flag to true when you do not want to capture the transaction amount immediately but only validate the
     * payment details and block (reserve) the amount.
     * The capture of a preauth-only transaction can be performed with the CapturePayment class.
     *
     * @param bool $isPreAuth
     */
    public function setIsPreAuth(bool $isPreAuth): void
    {
        $this->isPreAuth = $isPreAuth;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $fields = $this->getBaseFields();
        $fields['site_id'] = $this->getSiteId();
        $fields['preauth_only'] = $this->isPreAuth();
        $fields['payment_details'] = $this->getCreditCardDetails();

        return $fields;
    }
}
