<?php
namespace VendoSdk;

final class Vendo
{
    const SDK_VERSION = '2.4.2';

    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_GBP = 'GBP';
    const CURRENCY_JPY = 'JPY';
    const CURRENCY_MXN = 'MXN';
    const CURRENCY_BRL = 'BRL';

    /**
     * Currencies allowed for the Standard Join link billing_currency parameter.
     * @return string[]
     */
    public static function getAllowedBillingCurrencies(): array
    {
        return [self::CURRENCY_USD, self::CURRENCY_EUR, self::CURRENCY_GBP];
    }

    const BASE_URL = 'https://secure.vend-o.com';

    const S2S_STATUS_NOT_OK = 0;
    const S2S_STATUS_OK = 1;
    const S2S_STATUS_VERIFICATION_REQUIRED = 2;

    /**
     * Values for hosted checkout `pm` query parameter (Standard Join, Custom Offer; future hosted flows).
     * Distinct from {@see self::PAYMENT_TYPE_*} which are S2S API payload identifiers (e.g. card vs cc).
     * @see https://docs.vendoservices.com/docs/standard-join-link
     */
    const PAYMENT_METHOD_CREDIT_CARD = 'cc';
    const PAYMENT_METHOD_PAY_BY_BANK = 'pi';
    const PAYMENT_METHOD_SEPA = 'sepa';
    const PAYMENT_METHOD_PAYGARDEN = 'pg';
    const PAYMENT_METHOD_INTERAC = 'in';
    const PAYMENT_METHOD_PIX = 'pix';
    const PAYMENT_METHOD_CRYPTO = 'crypto';
    const PAYMENT_METHOD_OXXO = 'oxxo';

    /**
     * Allowed values for hosted checkout parameter `pm`.
     * @return string[]
     */
    public static function getAllowedHostedCheckoutPaymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_CREDIT_CARD,
            self::PAYMENT_METHOD_PAY_BY_BANK,
            self::PAYMENT_METHOD_SEPA,
            self::PAYMENT_METHOD_PAYGARDEN,
            self::PAYMENT_METHOD_INTERAC,
            self::PAYMENT_METHOD_PIX,
            self::PAYMENT_METHOD_CRYPTO,
            self::PAYMENT_METHOD_OXXO,
        ];
    }

    const PAYMENT_TYPE_CREDIT_CARD = 'card';
    const PAYMENT_TYPE_SEPA = 'sepa';
    const PAYMENT_TYPE_OXXO = 'oxxo';
    const PAYMENT_TYPE_WALLET = 'wallet';

    const SUBSCRIPTION_CANCEL_RESPONSE_CODE_OK = '5915';
    const SUBSCRIPTION_REFUND_CODE_OK = '5907';
    const SUBSCRIPTION_REACTIVATE_RESPONSE_CODE_OK = '5956';
}
