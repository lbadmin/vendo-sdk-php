<?php
namespace VendoSdk\S2S\Response\Details;

class Subscription
{
    /** @var ?int */
    protected $id;

    /**
     * @param array $subscription
     * @throws \Exception
     */
    public function __construct(array $subscription)
    {
        $this->setId($subscription['id'] ?? null);
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
}
