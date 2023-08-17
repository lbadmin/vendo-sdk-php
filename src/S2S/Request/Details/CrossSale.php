<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class CrossSale implements \JsonSerializable
{
    /** @var ?int */
    protected $initialTransactionId;

    /**
     * @return int|null
     */
    public function getInitialTransactionId(): ?int
    {
        return $this->initialTransactionId;
    }

    /**
     * @param int|null $initialTransactionId
     */
    public function setInitialTransactionId(?int $initialTransactionId): void
    {
        $this->initialTransactionId = $initialTransactionId;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'initial_transaction_id' => $this->initialTransactionId,
        ];
    }
}