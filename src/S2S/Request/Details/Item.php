<?php
namespace VendoSdk\S2S\Request\Details;

use VendoSdk\Exception;

class Item implements \JsonSerializable
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $description;
    /** @var float */
    protected $price;
    /** @var int */
    protected $quantity;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize()
    {
        if (empty($this->id) || empty($this->description) || ($this->price != 0.0 && empty($this->price))
            || empty($this->quantity)
        ) {
            throw new Exception('You must set a value for each field of an item in ' . get_class($this));
        }

        return [
            'item_id' => $this->id,
            'item_description' => $this->description,
            'item_price' => $this->price,
            'item_quantity' => $this->quantity,
        ];
    }
}