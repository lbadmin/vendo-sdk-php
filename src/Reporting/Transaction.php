<?php
namespace VendoSdk\Reporting;

use VendoSdk\Exception;
use VendoSdk\Reporting\Response\RowElement;
use VendoSdk\Reporting\Response\TransactionElement;

class Transaction extends Reconciliation
{
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