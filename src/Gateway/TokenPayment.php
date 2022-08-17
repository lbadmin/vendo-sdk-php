<?php
namespace VendoSdk\Gateway;

use VendoSdk\Exception;
use VendoSdk\Gateway\Request\Details\CreditCard;
use VendoSdk\Gateway\Request\Details\Token;

/**
 * This class allows you to use a payment details token to process a new payment
 *
 * @deprecated please use Payment class instead. see example: ./examples/gateway/payment_with_saved_token.php
 */
class TokenPayment extends PaymentBase implements \JsonSerializable
{
    /** @var Token */
    protected $paymentDetailsToken;

    /**
     * @inheritdoc
     */
    public function getApiEndpoint(): string
    {
        return parent::getApiEndpoint() . '/payment';
    }

    /**
     * @return Token
     */
    public function getPaymentDetailsToken(): Token
    {
        return $this->paymentDetailsToken;
    }

    /**
     * @param Token $paymentDetailsToken
     */
    public function setPaymentDetailsToken(Token $paymentDetailsToken): void
    {
        $this->paymentDetailsToken = $paymentDetailsToken;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        $fields = $this->getBaseFields();
        unset($fields['customer_details']);
        $fields['site_id'] = $this->getSiteId();
        $fields['preauth_only'] = false;
        $fields['payment_details'] = $this->getPaymentDetailsToken();

        return $fields;
    }
}
