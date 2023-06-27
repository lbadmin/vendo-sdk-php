<?php
namespace VendoSdk;

final class Vendo
{
    const SDK_VERSION = '2.0.1';

    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_GBP = 'GBP';
    const CURRENCY_JPY = 'JPY';

    const BASE_URL = 'https://secure.vend-o.com';

    const S2S_STATUS_NOT_OK = 0;
    const S2S_STATUS_OK = 1;
    const S2S_STATUS_VERIFICATION_REQUIRED = 2;

    const PAYMENT_TYPE_CREDIT_CARD = 'card';
    const PAYMENT_TYPE_SEPA = 'sepa';

    const SUBSCRIPTION_CANCEL_RESPONSE_CODE_OK = '5915';
    const SUBSCRIPTION_REFUND_CODE_OK = '5907';
    const SUBSCRIPTION_REACTIVATE_RESPONSE_CODE_OK = '5956';
}
