<?php
namespace VendoSdk;

final class Vendo
{
    const SDK_VERSION = '1.0.3';

    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_GBP = 'GBP';
    const CURRENCY_JPY = 'JPY';

//@todo uncomment/remove before merge
//    const BASE_URL = 'https://secure.vend-o.com';
const BASE_URL = 'https://secure.staging.aws.vend-o.com';

    const GATEWAY_STATUS_NOT_OK = 0;
    const GATEWAY_STATUS_OK = 1;
    const GATEWAY_STATUS_VERIFICATION_REQUIRED = 2;

    const PAYMENT_TYPE_CREDIT_CARD = 'card';
    const PAYMENT_TYPE_SEPA = 'sepa';

    const SUBSCRIPTION_CANCEL_RESPONSE_CODE_OK = '5915';
    const SUBSCRIPTION_REFUND_CODE_OK = '5907';
}
