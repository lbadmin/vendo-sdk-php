<?php
namespace VendoSdk\S2S\Response\Details;

class Transaction
{
    /** @var ?int */
    protected $id;
    /** @var ?float */
    protected $amount;
    /** @var ?string */
    protected $currency;
    /** @var ?\DateTime */
    protected $datetime;

    /**
     * @param array $tx
     * @throws \Exception
     */
    public function __construct(array $tx)
    {
        $this->setId($tx['id'] ?? null);
        $this->setAmount($tx['amount'] ?? null);
        $this->setCurrency($tx['currency'] ?? null);
        $this->setDatetime($tx['datetime'] ?? null);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float|null $amount
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return \DateTime|null
     */
    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }

    /**
     * @param string|null $datetime
     * @throws \Exception
     */
    public function setDatetime(?string $datetime): void
    {
        $dt = null;
        if (!empty($datetime)) {
            $dt = new \DateTime($datetime);
        }
        $this->datetime = $dt;
    }

}