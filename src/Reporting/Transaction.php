<?php
namespace VendoSdk\Reporting;

use VendoSdk\Exception;
use VendoSdk\Reporting\Response\RowElement;
use VendoSdk\Reporting\Response\TransactionElement;

class Transaction extends Reconciliation
{
    const TYPE_GATEWAY_SIGNUP = 1; // Gateway API: New subscription signup
    const TYPE_GATEWAY_TOKEN = 2; // Gateway API: Token purchase
    const TYPE_GATEWAY_PAYMENT = 3; // Gateway API: Payment
    const TYPE_SIGNUP = 10;
    const TYPE_TOKEN_SIGNUP = 11;
    const TYPE_V3_SALE_ONECLICK = 12;
    const TYPE_CROSS_SALE = 13;
    const TYPE_SALE_CHECKOUT = 14;
    const TYPE_REBILL = 15;
    const TYPE_UPGRADE = 16;
    const TYPE_INSTANT_UPGRADE = 17;
    const TYPE_CHANGE_OFFER_UPGRADE = 18;
    const TYPE_CHARGEBACK = 20;
    const TYPE_VOID = 21;
    const TYPE_RECHARGE = 25;
    const TYPE_REFUND = 30;
    const TYPE_PARTIAL_REFUND = 31;
    const TYPE_RECHARGE_REFUND = 35;
    const TYPE_REVOKE = 40;
    const TYPE_VERIFICATION = 60;
    const TYPE_PREAUTH_VOID = 61;
    const TYPE_FREE_SIGNUP = 62;
    const TYPE_UPDATE_PAYMENT_METHOD = 63;
    const TYPE_ONE_TIME_SHOT = 64;
    const TYPE_CROSS_SALE_UPSELL = 65;
    const TYPE_FREE_CROSS_SALE = 66;
    const TYPE_SETTLEMENT = 70;
    const TYPE_CAPTURE = 71;
    const TYPE_ONECLICK_SIGNUP = 100;
    const TYPE_ONECLICK_TOKEN_SIGNUP = 101;
    const TYPE_SIGNUP_EVERYWHERE = 102;
    const TYPE_TOKEN_SIGNUP_EVERYWHERE = 103;
    const TYPE_FREE_SIGNUP_EVERYWHERE = 104;
    const TYPE_REVSHARE_ONECLICK = 105;
    const TYPE_REVSHARE_ONECLICK_TOKEN = 106;
    const TYPE_REVSHARE_ONECLICK_FREE = 107;
    const TYPE_REPURCHASE_SIGNUP = 110;

    /**
     * Returns the details of the transaction
     *
     * @return ?RowElement
     * @throws Exception
     */
    public function getDetails()
    {
        $parsedRes = $this->getTransactions();
        return $parsedRes[0] ?? null;
    }

    /**
     * @inheritdoc
     */
    protected function setAllowedUrlParameters(): void {
        $this->allowedUrlParams = [
            'merchantID',
            'format',
            'transactionId',
        ];
    }

    public function setTransactionId(int $vendoTransactionId): void
    {
        $this->transactionId = $vendoTransactionId;
    }
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}